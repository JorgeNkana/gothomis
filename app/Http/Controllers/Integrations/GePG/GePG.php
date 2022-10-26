<?php

namespace App\Http\Controllers\Integrations\GePG;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\GePG\Gepg_account;
use App\GePG\Tbl_cash_deposit;
use App\Payments\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use App\Drf\Tbl_sale;
use App\Drf\Tbl_drf_sale_stock_balance;
use DB;
use QrCode;

class GePG extends Controller
{
	private $gepg_account;

	public function gepgSendBill(Request $request){
		$request= JSON_decode(file_get_contents("php://input"));
		if($request->InvoiceLine->BillAmount == 0){
			return array("success"=>0,"generic"=>"Cannot process a zero amount bill","real"=> "");
		}
		
		$facility = DB::select("select * from tbl_facilities where id = '".$request->facility_id."'")[0];
		
		//commented to allow service to continue before gepg switchewd to new gfs
		if(date("Y-m-d") >= "2021-08-21" && $facility->facility_type_id > 3 && !isset($request->drf)){
			$missing_gfs = DB::select("select GROUP_CONCAT(DISTINCT item_name SEPARATOR ', ') as missers from tbl_invoice_lines invoice where invoice.invoice_id=$request->InvoiceId and invoice.is_payable = 1 and not exists(select id from tbl_gfs_codes_item_mapping mapping where invoice.item_id = mapping.item_id)");
			if($missing_gfs[0]->missers != null){
				return array("success"=>0,"generic"=>"Missing GFS Code Mapping for: " . $missing_gfs[0]->missers,"real"=> "");
			}
		}
		
		
		$facility_code = $facility->facility_code;
		
		$this->gepg_account = Gepg_account::where('facility_code',$facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);

		if($request->InvoiceLine->CashDeposit == 0 && !isset($request->drf)){
			// to make sure that the same BillId is submitted, the value is recorded 
			//into the tbl_encounter_invoices and read afterwards on re-submission
			$bill = DB::select("SELECT BillId, created_at FROM tbl_encounter_invoices WHERE id=".$request->InvoiceId);
			if(empty($bill[0]->BillId))
				DB::statement("UPDATE tbl_encounter_invoices set BillId = UUID() WHERE id=".$request->InvoiceId);
			
			$BillId = DB::select("SELECT BillId FROM tbl_encounter_invoices WHERE id=".$request->InvoiceId)[0]->BillId;
			
			$bill_date = new \DateTime($bill[0]->created_at);
		}else{
			$BillId = DB::select("SELECT UUID() BillId")[0]->BillId;
			$bill_date = new \DateTime();
		}
		
		$gen_date = clone $bill_date;
		
		$expiry_date = $gen_date->add(new \DateInterval('P60D'));
		
		$billAmount = 0;
		
		$GfsCode = "";
				
		if(!isset($request->drf)){
			if($request->InvoiceLine->CashDeposit == 0 && date("Y-m-d") >= "2021-08-21" && $facility->facility_type_id > 3)
			{
				$items = DB::select("select invoice.*, gfs.code as GfsCode from tbl_invoice_lines invoice join tbl_gfs_codes_item_mapping mapping on invoice.item_id = mapping.item_id and invoice.invoice_id=$request->InvoiceId and invoice.is_payable = 1 and ((invoice.price * invoice.quantity) - invoice.discount) > 0 join tbl_gfs_codes gfs on mapping.gfs_code_id = gfs.id");
			}
			else if($request->InvoiceLine->CashDeposit == 0 && ($facility->facility_type_id <= 3 || date("Y-m-d") <= "2021-08-21"))
			{
				$GfsCode = $this->gepg_account->GfsCode;
				
				$items = DB::select("select invoice.*, $GfsCode  GfsCode from tbl_invoice_lines invoice where invoice.invoice_id=$request->InvoiceId and invoice.is_payable = 1 and ((invoice.price * invoice.quantity) - invoice.discount) > 0");
			}
		}
		else{
			$GfsCode = $this->gepg_account->GfsCode;
				
			$items = DB::select("select id, quantity, unit_price as price, 0 as discount, $GfsCode as GfsCode from tbl_sales invoice where invoice.invoice_number='$request->InvoiceId'");
		}
		
		
		if($request->InvoiceLine->CashDeposit == 0){
			$xmlItems = "";
			foreach($items as $item){
				$amount = ($item->price * $item->quantity) - $item->discount;
				
				//do not include zero amount items as will snap at gepg
				if($amount <= 0)
					continue;
				
				 //force bills to nearest payable 50 shs
				if(((int)$amount) % 50 !== 0 && $request->InvoiceLine->CashDeposit == 0){
					$amount = (int)$amount;
					$amount = ($amount - ($amount % 50)) + 50;
				}
				
				$billAmount += $amount;
				
						$xmlItems .= 	($xmlItems != "" ? "\n\t\t\t\t\t\t\t\t\t\t" : "")."<BillItem>
											<BillItemRef>$BillId-$item->id</BillItemRef>
											<UseItemRefOnPay>".trim($this->gepg_account->UseItemRefOnPay)."</UseItemRefOnPay>
											<BillItemAmt>$amount</BillItemAmt>
											<BillItemEqvAmt>$amount</BillItemEqvAmt>
											<BillItemMiscAmt>0</BillItemMiscAmt>
											<GfsCode>$item->GfsCode</GfsCode>
										</BillItem>";
			}
		}else{
			$amount = $request->InvoiceLine->BillAmount;
			$billAmount = $amount;
			$gfs = $request->InvoiceLine->GfsCode;
						$xmlItems = 	"<BillItem>
											<BillItemRef>$BillId</BillItemRef>
											<UseItemRefOnPay>".trim($this->gepg_account->UseItemRefOnPay)."</UseItemRefOnPay>
											<BillItemAmt>$amount</BillItemAmt>
											<BillItemEqvAmt>$amount</BillItemEqvAmt>
											<BillItemMiscAmt>0</BillItemMiscAmt>
											<GfsCode>$gfs</GfsCode>
										</BillItem>";
		}
		
		
		$gepg_request = "<Request>
							<FacilityCode>$facility_code</FacilityCode>
							<InvoiceId>".$request->InvoiceId."</InvoiceId>
							<CashDeposit>".$request->InvoiceLine->CashDeposit."</CashDeposit>
							<gepgBillSubReq>
								<BillHdr>
									<SpCode>".trim($this->gepg_account->SpCode)."</SpCode>
									<RtrRespFlg>".trim($this->gepg_account->RtrRespFlg)."</RtrRespFlg>
								</BillHdr>
								<BillTrxInf>
									<BillId>$BillId</BillId>
									<SubSpCode>".trim($this->gepg_account->SubSpCode)."</SubSpCode>
									<SpSysId>".trim($this->gepg_account->SpSysId)."</SpSysId>
									<BillAmt>$billAmount</BillAmt>
									<MiscAmt>0</MiscAmt>
									<BillExprDt>".$expiry_date->format('Y-m-d\TH:i:s')."</BillExprDt>
									<PyrId>".trim($request->InvoiceLine->PayerId)."</PyrId>
									<PyrName>".preg_replace('/\s+/', ' ', trim(htmlspecialchars($request->InvoiceLine->PayerName, ENT_XML1 | ENT_QUOTES, 'UTF-8')))."</PyrName>
									<BillDesc>".preg_replace('/\s+/', ' ', trim(htmlspecialchars($request->InvoiceLine->BillDescription, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."</BillDesc>
									<BillGenDt>".$bill_date->format('Y-m-d\TH:i:s'))."</BillGenDt>
									<BillGenBy>".preg_replace('/\s+/', ' ', trim(htmlspecialchars($request->UserName, ENT_XML1 | ENT_QUOTES, 'UTF-8')))."</BillGenBy>
									<BillApprBy>".preg_replace('/\s+/', ' ', trim(htmlspecialchars($request->UserName, ENT_XML1 | ENT_QUOTES, 'UTF-8')))."</BillApprBy>
									<PyrCellNum>".preg_replace('/\s+/', ' ', ($request->InvoiceLine->PayerPhone != '' ? $request->InvoiceLine->PayerPhone : $this->gepg_account->default_phone))."</PyrCellNum>
									<PyrEmail>".preg_replace('/\s+/', ' ', ($request->InvoiceLine->PayerEmail != '' ? $request->InvoiceLine->PayerEmail : $this->gepg_account->default_email))."</PyrEmail>
									<Ccy>".trim($this->gepg_account->Ccy)."</Ccy>
									<BillEqvAmt>$billAmount</BillEqvAmt>
									<RemFlag>".trim($this->gepg_account->RemFlag)."</RemFlag>
									<BillPayOpt>".trim($this->gepg_account->BillPayOpt)."</BillPayOpt>
									<BillItems>
										$xmlItems
									</BillItems>
								</BillTrxInf>
							</gepgBillSubReq>
						</Request>";
		
		
		$ch = curl_init($this->gepg_account->intermediate_url."new/send_bill");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		try{
			$data = curl_exec($ch);


			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_sending_bill.html',curl_error($ch));
				return array("success"=>0,"generic"=>"Unexpected error encountered while submitting bill to Intermediate GoT-HoMIS Server","real"=> curl_error($ch));
			}
			curl_close($ch);
			if(!is_object(JSON_decode($data))){
				file_put_contents('gepg_error_while_sending_bill.html',$data);
				return array("success"=>0,"generic"=>"Unexpected error encountered while submitting bill to Intermediate GoT-HoMIS Server","real"=> "");
			}elseif(JSON_decode($data)->success == 1){
				if($request->InvoiceLine->CashDeposit == 1){//cash deposits/payments
					$deposit = Tbl_cash_deposit::create(["transaction"=>$request->InvoiceLine->BillDescription, "user_id"=>$request->UserId,"amount"=>$billAmount,"facility_id"=>$request->facility_id,"BillId"=>$BillId, "created_at"=>$bill_date->format('Y-m-d H:i:s'), "GfsCode"=>$request->InvoiceLine->GfsCode, "drf"=>(isset($request->drf) ? 1 : 0)]);
					
					DB::statement("insert into gepg_bills(CashDeposit,BillId, GfsCode, BillItemRef, BillAmount, Amount, Name,created_at, PyrId, BillExprDt, BillGenDt, BillGenBy, BillApprBy, PyrCellNum, PyrEmail, BillDesc, drf) select  ".$deposit->id.", '$BillId','".$request->InvoiceLine->GfsCode."','$BillId',".$billAmount.",".$billAmount.", name,'".$bill_date->format('Y-m-d H:i:s')."','".trim($request->InvoiceLine->PayerId)."','".$expiry_date->format('Y-m-d\TH:i:s')."','".$bill_date->format('Y-m-d\TH:i:s')."','".trim(htmlspecialchars($request->UserName, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."','".trim(htmlspecialchars($request->UserName, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."','".($request->InvoiceLine->PayerPhone != '' ? $request->InvoiceLine->PayerPhone : $this->gepg_account->default_phone)."','".($request->InvoiceLine->PayerEmail != '' ? $request->InvoiceLine->PayerEmail : $this->gepg_account->default_email)."','".trim(htmlspecialchars($request->InvoiceLine->BillDescription, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."',". (isset($request->drf) ? 1 : 0). " from users where id = ".$request->UserId);
				}else{
					if(count(DB::select("select * from gepg_bills where BillId='$BillId'")) == 0){
						foreach($items as $item){
							$ref = $BillId."-".$item->id;
							$amount = ($item->price * $item->quantity) - $item->discount;
							 //force bills to nearest payable 50 shs
							if(((int)$amount) % 50 !== 0 && $request->InvoiceLine->CashDeposit == 0){
								$amount = (int)$amount;
								$amount = ($amount - ($amount % 50)) + 50;
							}
							DB::statement("insert into gepg_bills(InvoiceId, BillId, GfsCode, BillItemRef, BillAmount, Amount, Name, created_at, PyrId, BillExprDt, BillGenDt, BillGenBy, BillApprBy, PyrCellNum, PyrEmail, BillDesc, drf) select '".$request->InvoiceId."', '$BillId','".$item->GfsCode."','".$ref."',".$billAmount.",".$amount.",".(isset($request->drf) ? "'".$request->InvoiceLine->PayerName."'" : "(select concat(first_name,' ',middle_name,' ',last_name) from tbl_invoice_lines  where invoice_id = ".$request->InvoiceId." limit 1)").",'".$bill_date->format('Y-m-d H:i:s')."','".trim($request->InvoiceLine->PayerId)."','".$expiry_date->format('Y-m-d\TH:i:s')."','".$bill_date->format('Y-m-d\TH:i:s')."','".trim(htmlspecialchars($request->UserName, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."','".trim(htmlspecialchars($request->UserName, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."','".($request->InvoiceLine->PayerPhone != '' ? $request->InvoiceLine->PayerPhone : $this->gepg_account->default_phone)."','".($request->InvoiceLine->PayerEmail != '' ? $request->InvoiceLine->PayerEmail : $this->gepg_account->default_email)."','".trim(htmlspecialchars($request->InvoiceLine->BillDescription, ENT_XML1 | ENT_QUOTES, 'UTF-8'))."',". (isset($request->drf) ? 1 : 0). " from users where id = ".$request->UserId);
						}
					}
				}
			}
			
			$bills = DB::select("SELECT DISTINCT `Name`, `InvoiceId`, `CashDeposit`, `BillAmount`, `BillId`, `PayCntrNum`, `Paid`, `PaidAt`, `PspReceiptNumber`, `created_at`, `PyrId`, `BillExprDt`, `BillGenDt`, `BillGenBy`, `BillApprBy`, `PyrCellNum`, `PyrEmail`, `BillDesc`, TIMESTAMPDIFF(MINUTE, IFNULL(PaidAt,CURRENT_TIMESTAMP), CURRENT_TIMESTAMP) AS since_payment FROM gepg_bills WHERE drf IS  ". (isset($request->drf) ? "TRUE" : "FALSE") ." ORDER BY created_at DESC");
			$response = JSON_decode($data);
			$response->data = $bills;
			return JSON_encode($response);
		}catch(Exception $ex){
				return array("success"=>0, "generic"=>"An error was encountered","real"=> $ex->getMessage());
		}
    }
	
	public function resendBill(Request $request){
		$facility_code = DB::select("select facility_code from tbl_facilities where id = '".$request->facility_id."'")[0]->facility_code;

		$this->gepg_account = Gepg_account::where('facility_code',$facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);
		
		$bill = DB::select("SELECT * FROM gepg_bills WHERE BillId='".$request->BillId."'");
		
		if(count($bill) == 0)
			return  array("success"=>0,"generic"=>"Bill not found","real"=> "","account"=>-1);
		
		$xmlItems = "";
		foreach($bill as $item){
			{
				$xmlItems .= "<BillItem>
								<BillItemRef>$item->BillItemRef</BillItemRef>
								<UseItemRefOnPay>".trim($this->gepg_account->UseItemRefOnPay)."</UseItemRefOnPay>
								<BillItemAmt>$item->Amount</BillItemAmt>
								<BillItemEqvAmt>$item->Amount</BillItemEqvAmt>
								<BillItemMiscAmt>0</BillItemMiscAmt>
								<GfsCode>$item->GfsCode</GfsCode>
							 </BillItem>";
			}
		}
		
		$bill = $bill[0];
		$gepg_request = "<Request>
							<FacilityCode>$facility_code</FacilityCode>
							<InvoiceId>$bill->InvoiceId</InvoiceId>
							<CashDeposit>$bill->CashDeposit</CashDeposit>
							<gepgBillSubReq>
								<BillHdr>
									<SpCode>".trim($this->gepg_account->SpCode)."</SpCode>
									<RtrRespFlg>".trim($this->gepg_account->RtrRespFlg)."</RtrRespFlg>
								</BillHdr>
								<BillTrxInf>
									<BillId>$bill->BillId</BillId>
									<SubSpCode>".trim($this->gepg_account->SubSpCode)."</SubSpCode>
									<SpSysId>".trim($this->gepg_account->SpSysId)."</SpSysId>
									<BillAmt>$bill->BillAmount</BillAmt>
									<MiscAmt>0</MiscAmt>
									<BillExprDt>$bill->BillExprDt</BillExprDt>
									<PyrId>$bill->PyrId</PyrId>
									<PyrName>$bill->Name</PyrName>
									<BillDesc>$bill->BillDesc</BillDesc>
									<BillGenDt>$bill->BillGenDt</BillGenDt>
									<BillGenBy>$bill->BillGenBy</BillGenBy>
									<BillApprBy>$bill->BillApprBy</BillApprBy>
									<PyrCellNum>$bill->PyrCellNum</PyrCellNum>
									<PyrEmail>$bill->PyrEmail</PyrEmail>
									<Ccy>".trim($this->gepg_account->Ccy)."</Ccy>
									<BillEqvAmt>$bill->BillAmount</BillEqvAmt>
									<RemFlag>".trim($this->gepg_account->RemFlag)."</RemFlag>
									<BillPayOpt>".trim($this->gepg_account->BillPayOpt)."</BillPayOpt>
									<BillItems>
									 $xmlItems
									</BillItems>
								</BillTrxInf>
							</gepgBillSubReq>
						</Request>";
				
		$ch = curl_init($this->gepg_account->intermediate_url."new/send_bill");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		
		try{
			$data = curl_exec($ch);


			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_sending_bill.html',curl_error($ch));
				return array("success"=>0,"generic"=>"Unexpected error encountered while submitting bill to Intermediate GoT-HoMIS Server","real"=> curl_error($ch));
			}
			curl_close($ch);
			if(!is_object(JSON_decode($data))){
				file_put_contents('gepg_error_while_sending_bill.html',$data);
				return array("success"=>0,"generic"=>"Unexpected error encountered while submitting bill to Intermediate GoT-HoMIS Server","real"=> "");
			}
			
			$bills = DB::select("SELECT DISTINCT `Name`, `InvoiceId`, `CashDeposit`, `BillAmount`, `BillId`, `PayCntrNum`, `Paid`, `PaidAt`, `PspReceiptNumber`, `created_at`, `PyrId`, `BillExprDt`, `BillGenDt`, `BillGenBy`, `BillApprBy`, `PyrCellNum`, `PyrEmail`, `BillDesc`, TIMESTAMPDIFF(MINUTE, IFNULL(PaidAt,CURRENT_TIMESTAMP), CURRENT_TIMESTAMP) AS since_payment FROM gepg_bills WHERE drf IS  ". ($request->has('drf') ? "TRUE" : "FALSE") ." ORDER BY created_at DESC");
			$response = JSON_decode($data);
			$response->data = $bills;
			return JSON_encode($response);
		}catch(Exception $ex){
				file_put_contents('gepg_error_while_resending_bill.html',$ex->getMessage());
				return array("success"=>0, "generic"=>"An error was encountered","real"=> $ex->getMessage());
		}
    }
	
	public function gepgCancelBill(Request $request){
		$request= JSON_decode(file_get_contents("php://input"));
		
		$facility_code = DB::select("select facility_code from tbl_facilities where id = '".$request->facility_id."'")[0]->facility_code;

		$this->gepg_account = Gepg_account::where('facility_code',$facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);

		$gepg_request = "<Request>
							<FacilityCode>$facility_code</FacilityCode>
							<gepgBillCanclReq>
								<SpCode>".$this->gepg_account->SpCode."</SpCode>
								<SpSysId>".$this->gepg_account->SpSysId."</SpSysId>
								<BillId>".$request->BillId."</BillId>
							</gepgBillCanclReq>
						</Request>";

		$ch = curl_init($this->gepg_account->intermediate_url."new/cancel_bill");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		try{
			$data = curl_exec($ch);

			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_cancelling_bill.html',curl_error($ch));
				return  array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> curl_error($ch));
			}
			curl_close($ch);

			if(!is_object(JSON_decode($data))){
				file_put_contents('gepg_error_while_cancelling_bill.html',$data);
				return  array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> "Please, call support <b>+255 714 680 825</b>");
			}elseif(JSON_decode($data)->success == 1){
				if($request->CashDeposit != 0){
					Tbl_cash_deposit::where( "BillId", $request->BillId)
										->update(["cancelled" => 1, 
													"user_id"=>$request->user_id,
													"cancelling_reason"=>$request->reason
												]);
				}else if(!isset($request->drf)){
					//cancel the actual bill only if the same user created it and is not yet paid
					if(Tbl_invoice_line::where('invoice_id', $request->InvoiceId)->where("user_id",$request->user_id)->where("status_id", 1)->count() > 0){
						$update  = [
									"cancelling_reason"=>$request->reason,
									"user_id" => $request->user_id,
									"status_id" => 3,
									];
					
						Tbl_invoice_line::where('invoice_id', $request->InvoiceId)
											->update($update);
											
						Tbl_encounter_invoice::where('id', $request->InvoiceId)
										->update([
													"BillId" => null
												]);
					}
				}
				
				//remove the record from the access table
				DB::statement("delete from gepg_bills where BillId='$request->BillId'");
			}
			return $data;
		}catch(Exception $ex){
				file_put_contents('gepg_error_while_cancelling_bill.html',$ex->getMessage());
				return array("success"=>0, "generic"=>"An error encountered","real"=> $ex->getMessage());
		}

    }

	public function gepgReconcile(Request $request){
		$request= JSON_decode(file_get_contents("php://input"));
		
		$facility_code = DB::select("select facility_code from tbl_facilities where id = '".$request->facility_id."'")[0]->facility_code;

		$this->gepg_account = Gepg_account::where('facility_code',$facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);

		
		$gepg_request = "<Request><FacilityCode>$facility_code</FacilityCode></Request>";


		$ch = curl_init($this->gepg_account->intermediate_url."new/reconcile");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		try{
			$data = curl_exec($ch);

			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_relaying_reconciliation_request.html',curl_error($ch));
				return array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> curl_error($ch));
			}
			curl_close($ch);

			if(!is_object(JSON_decode($data))){
				file_put_contents('gepg_error_while_relaying_reconciliation_request.html',$data);
				return array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> "Please, call support <b>+255 714 680 825</b>");
			}
			return $data;
		}catch(Exception $ex){
				file_put_contents('gepg_error_while_relaying_reconciliation_request.html',$ex->getMessage());
				return array("success"=>0, "generic"=>"An error encountered","real"=> $ex->getMessage());
		}
    }
	
	public function downloadPendingControlNumbersAndPayments(Request $request){
		$request= JSON_decode(file_get_contents("php://input"));
		
		$facility_code = DB::select("select facility_code from tbl_facilities where id = '".$request->facility_id."'")[0]->facility_code;

		$this->gepg_account = Gepg_account::where('facility_code',$facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);

		$pendings = DB::select("SELECT * FROM gepg_bills WHERE PayCntrNum IS NULL  OR PspReceiptNumber IS NULL AND TIMESTAMPDIFF(HOUR, created_at, CURRENT_TIMESTAMP) <= 3");
		
		if(count($pendings) > 0){
			$gepg_request = "<Request>
								<FacilityCode>$facility_code</FacilityCode>
							</Request>";

			$ch = curl_init($this->gepg_account->intermediate_url."new/pending_details");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
			curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
			
			try{
				$data = curl_exec($ch);
				

				if (curl_errno($ch)) {
					curl_close($ch);
					file_put_contents('gepg_error_while_downloading_pending_bill_details.html',curl_error($ch));
					return array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> curl_error($ch));
				}
				curl_close($ch);
				if(!is_object(JSON_decode($data))){
					file_put_contents('gepg_error_while_downloading_pending_bill_details.html',$data);
					return array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> "Please, call support <b>+255 714 680 825</b>");
				}else{
					$response = JSON_decode($data);
					ini_set('max_execution_time', -1);
					$processed1 = $this->updateControlNumbers($response->pending_control_numbers, isset($request->drf));
					$processed2 = $this->updatePayments($response->pending_payments_details, isset($request->drf));
					$this->markSuccessfullyDownloadedControlNumbersAndPayments($processed1, $processed2);
				}
			}catch(Exception $ex){
					file_put_contents('gepg_error_while_downloading_pending_bill_details.html',$ex->getMessage());
					return array("success"=>0, "generic"=>"An error encountered","real"=> $ex->getMessage());
			}
		}
		return  DB::select("SELECT DISTINCT `Name`, `InvoiceId`, `CashDeposit`, `BillAmount`, `BillId`, `PayCntrNum`, `Paid`, `PaidAt`, `PspReceiptNumber`, `created_at`, `PyrId`, `BillExprDt`, `BillGenDt`, `BillGenBy`, `BillApprBy`, `PyrCellNum`, `PyrEmail`, `BillDesc`, TIMESTAMPDIFF(MINUTE, IFNULL(PaidAt,CURRENT_TIMESTAMP), CURRENT_TIMESTAMP) AS since_payment FROM gepg_bills WHERE drf IS ". (isset($request->drf) ? "TRUE" : "FALSE") ." ORDER BY created_at DESC");
	}

	public function downloadPendingRecons(Request $request){
		$request= JSON_decode(file_get_contents("php://input"));
		
		$facility_code = DB::select("select facility_code from tbl_facilities where id = '".$request->facility_id."'")[0]->facility_code;

		$this->gepg_account = Gepg_account::where('facility_code',$facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);

		$gepg_request = "<Request>
						<FacilityCode>$facility_code</FacilityCode>
					</Request>";

		$ch = curl_init($this->gepg_account->intermediate_url."new/pending_recons");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		try{
			$data = curl_exec($ch);

			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_downloading_pending_recons.html',curl_error($ch));
				return array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> curl_error($ch));
			}
			curl_close($ch);
			if(!is_object(JSON_decode($data))){
				file_put_contents('gepg_error_while_downloading_pending_recons.html',$data);
				return array("success"=>0, "generic"=>"Sorry. Seems like the central processing server encountered an error.","real"=> "Please, call support <b>+255 714 680 825</b>");
			}else{
				$data = JSON_decode($data)->pending_reconcilliations;
				$reconcilled_bills = 0;
				$last_recon = "...";
				$recons = "";
				ini_set('max_execution_time', -1);
				foreach($data as $recon){
					foreach(unserialize($recon->recons) as $bill){
						if((isset($bill->CashDeposit) && $bill->CashDeposit != 0) || (isset($bill->cashDeposit) && $bill->cashDeposit !=0)){
							Tbl_cash_deposit::where( "BillId", $bill->BillId)
									->update([
												'AmountPaid' => $bill->AmountPaid,
												'PayCntrNum' => (isset($bill->PayCntrNum) ? $bill->PayCntrNum : null),
												'PspReceiptNumber' => $bill->PspReceiptNumber,
												'paid_at' => (isset($bill->paid_at) ? $bill->paid_at : $bill->PaidAt)
											]);
						}else{
							Tbl_invoice_line::where('invoice_id', (isset($bill->invoice_id) ? $bill->invoice_id : $bill->InvoiceId))
											->where('status_id', 1)//not cancelled
											->update([
												"status_id" => 2,
												"payment_method_id" => 2,
												"gepg_receipt" => $bill->PspReceiptNumber,
												"updated_at" => (isset($bill->paid_at) ? $bill->paid_at : $bill->PaidAt)]
											);
							Tbl_encounter_invoice::where( "BillId", $bill->BillId)
													->update([
																'PayCntrNum' => (isset($bill->PayCntrNum) ? $bill->PayCntrNum : null)
															]);
						}
						$reconcilled_bills++;
					}
					$last_recon = $recon->ReconcilliationDate;
					$recons .= "<SpReconcReqId>".$recon->SpReconcReqId."</SpReconcReqId>";
				}
				$response = $this->markSuccessfulReconcilliations($recons);

				if($reconcilled_bills > 0)
					return array("success"=>1,"completed"=>1, "generic"=>"A total of <span style='color:red'>$reconcilled_bills bills</span> were successfully reconcilled up to <span style='color:red'>$last_recon</span>");
				else
					return array("success"=>1,"completed"=>1, "generic"=>"No pending bills were found, you are already reconcilled");
			}
		}catch(Exception $ex){
				file_put_contents('gepg_error_while_downloading_pending_recons.html',$ex->getMessage());
				return array("success"=>0, "generic"=>"An error encountered","real"=> $ex->getMessage());
		}
	}
	
	public function markSuccessfullyDownloadedControlNumbersAndPayments($controlNumbers, $payments){
		if(count($controlNumbers) == 0 && count($payments) == 0)
			return;
		
		$cntrnums = "";
		$pays = "";
		foreach($controlNumbers as $billId)
			$cntrnums .= "<BillId>$billId</BillId>";
		
		foreach($payments as $billId)
			$pays .= "<BillId>$billId</BillId>";
			
		$gepg_request = "<Request>
							<ControlNumbers>$cntrnums</ControlNumbers>
							<Payments>$pays</Payments>
						</Request>";


		$ch = curl_init($this->gepg_account->intermediate_url."new/successful_details");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		try{
			$data = curl_exec($ch);

			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_marking_successful_bill_details.html',curl_error($ch));
			}
			curl_close($ch);
		}catch(Exception $ex){
				file_put_contents('gepg_error_while_marking_successful_bill_details.html',$ex->getMessage());
				return array("success"=>0, "generic"=>"An error encountered","real"=> $ex->getMessage());
		}
    }
	
	public function markSuccessfulReconcilliations($recons){
		$gepg_request = "<Request>
							<SuccessfulReconcilliations>$recons</SuccessfulReconcilliations>
						</Request>";


		$ch = curl_init($this->gepg_account->intermediate_url."new/successful_recons");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gepg_request);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
		
		try{
			$data = curl_exec($ch);

			if (curl_errno($ch)) {
				curl_close($ch);
				file_put_contents('gepg_error_while_marking_successful_recons.html',curl_error($ch));
			}
			curl_close($ch);
		}catch(Exception $ex){
				file_put_contents('gepg_error_while_marking_successful_recons.html',$ex->getMessage());
				return array("success"=>0, "generic"=>"An error encountered","real"=> $ex->getMessage());
		}
    }
	
	public function updateControlNumbers($bill_details, $is_drf){
		$processed = [];
		foreach($bill_details as $bill){
			if($bill->CashDeposit != 0){
				$rows = Tbl_cash_deposit::where( "BillId", $bill->BillId)
									->whereNull("PayCntrNum") 
									->update(["PayCntrNum" => $bill->PayCntrNum]);
				array_push($processed,$bill->BillId); 

			}else if(!$is_drf){
				Tbl_encounter_invoice::where('id', $bill->InvoiceId)
									->whereNull("PayCntrNum") 
									->update(["PayCntrNum" => $bill->PayCntrNum]);
				array_push($processed,$bill->BillId); 
			}else if($is_drf){
				Tbl_sale::where('invoice_number', $bill->InvoiceId)
									->whereNull("PayCntrNum") 
									->update(["PayCntrNum" => $bill->PayCntrNum, "BillId"=>$bill->BillId]);
				array_push($processed,$bill->BillId); 
			}
			DB::statement("UPDATE gepg_bills SET PayCntrNum = '$bill->PayCntrNum' WHERE BillId='$bill->BillId'");
		}
		return $processed;
	}

	public function updatePayments($payments, $is_drf){
		$processed = [];
		foreach($payments as $bill){
			if($bill->CashDeposit != 0){
				$rows = Tbl_cash_deposit::where( "BillId", "=", $bill->BillId)
									->whereNull("PspReceiptNumber") 
									->update(["AmountPaid" => $bill->AmountPaid, "PspReceiptNumber" => $bill->PspReceiptNumber, "paid_at" => $bill->PaidAt]);
				array_push($processed,$bill->BillId); 

			}else if(!$is_drf){
				$rows = Tbl_invoice_line::where('invoice_id',$bill->InvoiceId)
								->whereNull('gepg_receipt')
								->where('status_id', 1)//not cancelled
								->update([
									"status_id"=>2,
									"payment_method_id"=>2,
									"gepg_receipt"=>$bill->PspReceiptNumber,
									"updated_at"=>$bill->PaidAt]
								);
				array_push($processed,$bill->BillId); 
			}else if($is_drf){
				$rows = Tbl_sale::where('invoice_number',$bill->InvoiceId)
								->whereNull('gepg_receipt')
								->update([
									"payment_status"=>"PAID",
									"gepg_receipt"=>$bill->PspReceiptNumber,
									"updated_at"=>$bill->PaidAt]
								);
				array_push($processed,$bill->BillId); 
			}
			DB::statement("UPDATE gepg_bills SET PspReceiptNumber = '$bill->PspReceiptNumber', Paid = 1, PaidAt='$bill->PaidAt' WHERE BillId='$bill->BillId'");
		}
		return $processed;
	}

	public function gepgMarkProcessedBills(Request $request){
		$request= JSON_decode(file_get_contents("php://input"));
		if($request->CashDeposit != 0)
			Tbl_cash_deposit::where("BillId", $request->BillId)->update(['Processed'=>1]);
		else if(!isset($request->drf))
			Tbl_encounter_invoice::where("BillId", $request->BillId)->update(['Processed'=>1]);
		
		//remove the record from the access table
		DB::statement("delete from gepg_bills where BillId='$request->BillId' OR (TIMESTAMPDIFF(MINUTE, IFNULL(PaidAt,CURRENT_TIMESTAMP), CURRENT_TIMESTAMP) > 90  AND Paid = 1) OR TIMESTAMPDIFF(DAY, created_at, CURRENT_TIMESTAMP) > 2");
		
		return  array("success"=>1, "generic"=>"Item successfully removed from list","real"=> "");
	}

	public function printBill(Request $request){
		
		$facility = DB::select("select facility_code, facility_name from tbl_facilities where id = '".$request->facility_id."'")[0];

		$this->gepg_account = Gepg_account::where('facility_code',$facility->facility_code)->get();
		if(count($this->gepg_account) > 0)
			$this->gepg_account = $this->gepg_account[0];
		else
			return  array("success"=>0,"generic"=>"Facility GePG account not found","real"=> "","account"=>-1);
		
		$bill = DB::select("SELECT DISTINCT `Name`, `InvoiceId`, `CashDeposit`, `BillAmount`, `BillId`, `PayCntrNum`, `Paid`, `PaidAt`, `PspReceiptNumber`, `created_at`, `PyrId`, `BillExprDt`, `BillGenDt`, `BillGenBy`, `BillApprBy`, `PyrCellNum`, `PyrEmail`, `BillDesc`, TIMESTAMPDIFF(MINUTE, IFNULL(PaidAt,CURRENT_TIMESTAMP), CURRENT_TIMESTAMP) AS since_payment FROM gepg_bills WHERE BillId = '".$request->BillId."'");
		if(count($bill) ==1)
			$bill = $bill[0];
		
		$qrcode = "";
		if(class_exists("QrCode")){
			$qrcode = JSON_encode(['opType'=>$this->gepg_account->BillPayOpt,'shortCode'=>'001001','billReference'=>$bill->PayCntrNum,'amount'=>$bill->BillAmount,'billCcy'=>$this->gepg_account->Ccy,'billExprDt'=>(new \DateTime($bill->BillExprDt))->format('Y-m-d'),'billPayOpt'=>$this->gepg_account->BillPayOpt,'billRsv01'=>$facility->facility_name."|".$bill->Name]);

		
			$qrcode = QrCode::format('svg')->size(150)->generate($qrcode);
		}
		
		return array("success"=>1, "generic"=>"<table align=\"center\" width=\"95%\" class='talbe-responsive table-bordered'><tr><th>Amount:</th><th><span style='font-weight:bold; font-style:italic'>".number_format ($bill->BillAmount , 2 , "." , ",")."</span></th></tr><tr><th>Payer:</th><th><span style='font-weight:bold; font-style:italic'>".strtoupper($bill->Name)."</span></th></tr><tr><th>Control Number:</th><th><b style='color:red'>".number_format ($bill->PayCntrNum , 0 , "." , "  ")."</b></th></tr><tr><th>QR Code:</th><th>$qrcode</th></tr><tr><td colspan='2'>Pay the bill by producing the control number at a nearby NMB/CRDB branch or use Mobile Money services</td></tr></table>","real"=> "", "title"=>$bill->BillDesc);
	}
	
	public function getGePGPaidBill(Request $request){
		return DB::select("SELECT * FROM vw_paid_bills WHERE gepg_receipt = '".$request['gepg_receipt']."'");
	}

	public function rollback(Request $request){
		Tbl_invoice_line::where("invoice_id", "=", $request['invoice_id'])
							->update(["status_id" => 3]);
	}

	public function cashDepositTrail(Request $request){
		return DB::select("select t1.*,t2.name from tbl_cash_deposits t1 join users t2 on t1.user_id=t2.id where t1.cancelled IS FALSE and t1.AmountPaid is not null ".$request['condition']);
	}

	public function changePayOption(Request $request){
		try{
			Gepg_account::whereNotNull("id")->update(["PaymentMethod"=>$request->Option, "user_id"=>$request->UserId]);
			return array("success"=>1, "generic"=>"Change successfully saved","real"=> "");
		}catch(Exception $x){
			return array("success"=>0, "generic"=>"Change successfully saved","real"=> $x->getMessage());
		}
	}
	
	public function getPaymentOption(){
		try{
			return Gepg_account::select('PaymentMethod as BillPayOpt')->take(1)->get();
		}catch(Exception $ex){
			return null;
		}
	}
	
	public function facilityConfiguration(){
		$request = simplexml_load_string(file_get_contents("php://input"));
		$account = DB::select("select * from gepg_accounts where \"FacilityCode\" ='$request->FacilityCode'");
		return JSON_encode($account);
	}
	
	public function useGovNETAddress(){
		$success = Gepg_account::whereNotNull("id")->update(['intermediate_url' => 'http://172.16.18.147/gepg/gepg_handler/']);
		
		if($success > 0)
			echo "Link changed to use Local GovNET route";
		else
			echo "Could not perform the task";
	}
	
	public function useInternetAddress(){
		$success = Gepg_account::whereNotNull("id")->update(['intermediate_url' => 'http://196.192.72.107/gepg/gepg_handler/']);
		
		if($success > 0)
			echo "Link changed to use internet route";
		else
			echo "Could not perform the task";
	}
}