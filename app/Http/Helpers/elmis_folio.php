<?php
use Illuminate\Contracts\Routing\ResponseFactory;

if (!function_exists('eLMISFolioCreation')) {

function eLMISFolioCreation() {
     $responses=[];
     $sql="SELECT 'string' AS sourceOrderId, 'facility_code' AS facilityCode,'string program'  AS programCode,'201808' AS periodId,'UNIFIED' AS sourceApplication,'true' AS emergency,'status' AS status             
         FROM tbl_patients t1 LIMIT 1";  
	       $responses[]=DB::SELECT($sql); 


    $sql_1="SELECT 0 AS quantityRequested,0 AS reasonForRequestedQuantity,'productCode' AS productCode,'fullSupply' AS fullSupply, 'quantityDispensed' AS quantityDispensed,'quantityReceived' AS quantityReceived,'beginningBalance' AS beginningBalance,'stockInHand' AS stockInHand, 'stockOutDays' AS stockOutDays FROM tbl_patients t1 
             LIMIT 1";
           $responses[]=DB::SELECT($sql_1); 

    $sql_2="SELECT 'code' AS code,'quantity' AS quantity,'additive' AS additive FROM tbl_patients LIMIT 1";
           $responses[]=DB::SELECT($sql_2); 

        $foliolist_array=array();		
        $RnR_Info=array();
        $nonFullSupplyProds=array(); 
        $nonSupply= array();       
        $items_array =array();
        $otherComponents=array();
             $otherComponents['quantityRequested']=0;
             $otherComponents['reasonForRequestedQuantity']="string";


           foreach($responses[0] as $row) {
                       
            $RnR_Info['sourceOrderId']="sorcID";
            $RnR_Info['facilityCode']="FACILITYCODE";
            $RnR_Info['programCode']="programCode";
            $RnR_Info['periodId']="periodID";
            $RnR_Info['sourceApplication']="sourceApp";
            $RnR_Info['emergency']="emergenc";  
            $RnR_Info['status']= "status";
            $RnR_Info['nonFullSupplyProducts']=array();
            $nonSupply=array();
            $nonFullSupplyProducts=array();
            $lossesAndAdjustments['lossesAndAdjustments']=array();
            
              foreach($responses[1] as $nonFullSupplyProd) {
                $nonFullSupplyProds["productCode"]="productcde";
                $nonFullSupplyProds['fullSupply']="fullSupply";
                $nonFullSupplyProds['quantityDispensed']="quanttyDispensed";
                $nonFullSupplyProds['quantityReceived']="quantytyReceived";
                $nonFullSupplyProds['beginningBalance']=0;
                $nonFullSupplyProds['stockInHand']=0;
                $nonFullSupplyProds['stockOutDays']=0; 

               array_push($RnR_Info['nonFullSupplyProducts'],$nonFullSupplyProds);       
		      
            
            } 
           
            foreach($responses[2] as $folio_item) {
                $nonSupply["code"]="code";
                $nonSupply['quantity']=0;
                $nonSupply['additive']=null; 

                array_push($lossesAndAdjustments['lossesAndAdjustments'],$nonSupply);
                array_push($lossesAndAdjustments['lossesAndAdjustments'],$otherComponents);
            }  

             
              
              
                 array_push($RnR_Info['nonFullSupplyProducts'],$lossesAndAdjustments);

                
            

            array_push($foliolist_array,$RnR_Info);             
        }
         $data_string=json_encode($foliolist_array,JSON_PRETTY_PRINT);
       
        return $data_string;
    }
}




















?>