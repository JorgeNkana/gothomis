/**
 * Created by Mazigo Jr on 2017-02-28.
 */
(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('reportsController', reportsController);
    function reportsController($http, $scope, $rootScope, $uibModal,Helper,$timeout) {
        var user_id = $rootScope.currentUser.id;

        $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
		
		$scope.cardTitle=cardTitle.data[0];                      
        });

        var facility_id = $rootScope.currentUser.facility_id;

        $http.get('/api/getUsermenu/' + user_id).then(function (data) {
            $scope.menu = data.data;
        });
		
		$scope.changePayOption = function(){
			var html = '<form class="form-horizontal" role="form" name="myForm" autocomplete="off" >\
				<br />\
				<div class="row">\
					<div class="form-group">\
						<label class="col-md-3 control-label">User:</label>\
						<div class="col-md-9">\
							<input type="text" disabled class="form-control" value="' +$rootScope.currentUser.name+ '"/>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Action:</label>\
						<div class="col-md-9">\
							<select id="transaction" class="form-control">\
								<option value="">Change of Payment Method in Facility</option>\
							</select>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Change To:</label>\
						<div class="col-md-9">\
							<select id="method" class="form-control">\
								<option value="1">GePG</option>\
								<option value="0">Allow Cash through GoTHOMIS</option>\
							</select>\
						</div>\
					</div>\
				</div>\
				<br /><div class="col-md-12 text-center">Proceed?</div>\
				</form>';
			swal({
					title: 'Changing of Payment Method Mode',
					html: html,
					type: 'info',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes',
					customClass: 'swal-wide',
					allowOutsideClick:false
				}).then(function () {
						Helper.overlay(true);
						$http.post('/gepg/new/changePayOption', {Option: $('#method').val(), UserId: user_id}).then(function(response){
							Helper.overlay(false);
							swal({
									title:'PAYMENT METHOD',
									html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
									type:'info',
									customClass: 'swal-wide',
									allowOutsideClick:false
								});
							}, function(data){Helper.overlay(false);});
					}, function(){ return;});
		}

        $scope.getDepartmental = function (item) {
            $http.post('/api/getDepartmentalReports', {
                "start": item.start,
                "end": item.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.departments = data.data;
                var report_generated_on = new Date() + "";
                $scope.department_report_generated_on = report_generated_on.substring(0, 24);
                $scope.depGrandTotal = $scope.depTotal();
            });
        }
		
        $scope.getSubDepartmental = function (item) {
            $http.post('/api/getSubDepartmentalReports', {
                "start": item.start,
                "end": item.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.subdepartments = data.data;
                var report_generated_on = new Date() + "";
                $scope.department_report_generated_on = report_generated_on.substring(0, 24);
                $scope.subDepGrandTotal = $scope.subdepTotal();
            });
        }
        $scope.getDiscountReport = function (item) {
            $http.post('/api/discountReport', {
                "start": item.start,
                "end": item.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.discounts = data.data;
                var report_generated_on = new Date() + "";
                $scope.discount_report_generated_on = report_generated_on.substring(0, 24);
                $scope.discountGrandTotal = $scope.discountTotal();
            });
        }

        $scope.sum = function () {
            var total = 0;
            for (var i = 0; i < $scope.detailedData.length; i++) {
                total -= -($scope.detailedData[i].sub_total);
            }
            return total;
        }
        $scope.discountTotal = function () {
            var total = 0;
            for (var i = 0; i < $scope.discounts.length; i++) {
                total -= -($scope.discounts[i].discount);
            }
            return total;
        }
        $scope.sumPending = function () {
            var total = 0;
            for (var i = 0; i < $scope.pendingBills.length; i++) {
                total -= -($scope.pendingBills[i].sub_total);
            }
            return total;
        }
        $scope.sumGePG = function () {
            var total = 0;
            for (var i = 0; i < $scope.detailedDataGePG.length; i++) {
                total -= -($scope.detailedDataGePG[i].sub_total);
            }
            return total;
        }
        $scope.depTotal = function () {
            var total = 0;
            for (var i = 0; i < $scope.departments.length; i++) {
                total -= -($scope.departments[i].total);
            }
            return total;
        }
        $scope.subdepTotal = function () {
            var total = 0;
            for (var i = 0; i < $scope.subdepartments.length; i++) {
                total -= -($scope.subdepartments[i].total);
            }
            return total;
        }
        $scope.cashierTtl = function () {
            var total = 0;
            for (var i = 0; i < $scope.cashiers[0].length; i++) {
                total -= -($scope.cashiers[0][i].sub_total);
            }
            return total;
        }
        $scope.cashierTtlGePG = function () {
            var total = 0;
            for (var i = 0; i < $scope.cashiers[1].length; i++) {
                total -= -($scope.cashiers[1][i].sub_total);
            }
            return total;
        }
        $scope.getDate = function (item) {
            $http.post('/api/getDetailedReports', {
                "start": item.start,
                "end": item.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.detailedData = data.data[0];
                $scope.detailedDataGePG = data.data[1];
                $scope.selIdx = -1;
                $scope.detailedTotal = $scope.sum();
                $scope.detailedTotalGePG = $scope.sumGePG();
                $scope.selData = function (d, idx) {
                    $scope.selectedData = d;
                    $scope.selIdx = idx;
                };
                var report_generated_on = new Date() + "";
                $scope.report_generated_on = report_generated_on.substring(0, 24);


                $scope.isSelData = function (d) {
                    return $scope.selectedData === d;
                }
            });
        };

        $scope.getPendingBills = function () {
            swal('Pending bills','section contains bills created more than 48 hours ago and not yet cleared.Patients can still go to ' +
                'cash collector to clear their pending bills','info');
            $http.post('/api/pendingBills',{facility_id:facility_id}).then(function (data) {
                $scope.pendingBills = data.data;
                $scope.selIdx = -1;
                $scope.pendingBillsTotal = $scope.sumPending();
                $scope.selData = function (d, idx) {
                    $scope.selectedData = d;
                    $scope.selIdx = idx;
                };
                var report_generated_on = new Date() + "";
                $scope.report_generated_on = report_generated_on.substring(0, 24);


                $scope.isSelData = function (d) {
                    return $scope.selectedData === d;
                }
            });
        }
      $scope.printDepartmentReport = function () {
            
           //location.reload();
           var DocumentContainer = document.getElementById('departmentdivtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
            
        };



               $scope.printCashiersReport = function () {           

  var DocumentContainer = document.getElementById('employeedivtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
            

        };
		
		
		$scope.printDepositsReport = function () {           

			var DocumentContainer = document.getElementById('depositdivtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
            

        };
  $scope.Print_discount = function () {

      //location.reload();
      var DocumentContainer = document.getElementById('print_disc');
      var WindowObject = window.open("", "PrintWindow",
          "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
      WindowObject.document.title = "printout: GoT-HOMIS";
      WindowObject.document.writeln(DocumentContainer.innerHTML);
      WindowObject.document.close();

      setTimeout(function () {
          WindowObject.focus();
          WindowObject.print();
          WindowObject.close();
      });

        };

        $scope.printReport = function () {
            var DocumentContainer = document.getElementById('divtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
        };
        $scope.printTransGeepgReport = function () {
            var DocumentContainer = document.getElementById('divtoprint2');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
        };


        $scope.getDetailedData = function (item, dates) {
            $http.post('/api/detailedData', {
                "receipt_number": item.receipt_number,
                "start": dates.start,
                "end": dates.end
            }).then(function (data) {
                $scope.getDetailedReports = data.data;
            });
        }
        $scope.getPendingBillData = function (item) {
            $http.post('/api/pendingBillData', {
                "receipt_number": item.receipt_number
            }).then(function (data) {
                $scope.pendingBillData = data.data;
            });
        }
        $scope.getCashiers = function (item) {
            $http.post('/api/getCashierReports', {
                "start": item.start,
                "end": item.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cashiers = data.data;

	 var report_generated_on = new Date() + "";
                $scope.employee_report_generated_on = report_generated_on.substring(0, 24);

                $scope.csGrandTotal = $scope.cashierTtl();
                $scope.csGrandTotalGepg = $scope.cashierTtlGePG();
            });
			$scope.getDeposits(item);
        }
		
		$scope.getDeposits = function (item) {
			var condition = "";
			if(item.start && item.end)
				condition = " and t1.updated_at between timestamp('"+item.start+"') and timestamp('"+item.end+"')";
			else if(item.start && !item.end)
				condition = " and t1.updated_at >= timestamp('"+item.start+"')";
            else if(!item.start && item.end)
				condition = " and t1.updated_at <= timestamp('"+item.end+"')";
            $http.post('/gepg/new/getCashDeposits', {
                "condition": condition,"facility_id": facility_id
            }).then(function (data) {
                $scope.deposits = data.data;
				var report_generated_on = new Date() + "";
                $scope.employee_report_generated_on = report_generated_on.substring(0, 24);

                $scope.totalDeposits = function(){
					var total = 0;
					$scope.deposits.forEach(function(deposit){
							total +=parseFloat(deposit.AmountPaid);
					});
					return total;
                }
            });
        }
		
		
        var reprint = [];
        $scope.getReceipt = function (item, dates) {
            $http.post('/api/detailedData', {
                "receipt_number": item.receipt_number,
                "start": dates.start,
                "end": dates.end
            }).then(function (data) {
                $scope.getDetailedReports = data.data;
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/payments/receipts.html',
                    size: 'lg',
                    animation: true,
                    controller: 'printReceipt',
                    resolve: {
                        object: function () {

                            return $scope.getDetailedReports;
                        }
                    }
                });
            });

        }
        $scope.getReceiptCopy = function (item,category) {
            $http.post('/api/getReceiptData',{"receipt_number":item.receipt_number,"payment_method_id":category}).then(function (data) {
                $scope.getDetailedReports = data.data;
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/payments/receiptCopy.html',
                    size: 'lg',
                    animation: true,
                    controller: 'printReceipt',
                    resolve: {
                        object: function () {

                            return $scope.getDetailedReports;
                        }
                    }
                });
            });
        }


        
		 $scope.exemption_finance_depts = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            var s = moment(item.start_date).toISOString();
             

            $http.post('/api/exemption_finance_depts', datee).then(function (data) {


                $scope.exempted_departments = data.data;

                $scope.totalexempted = totalexempted($scope.exempted_departments);


            });

        }
 var totalexempted = function () {
            var totalexempted = 0;

            for (var i = 0; i < $scope.exempted_departments.length; i++) {
                totalexempted -= -($scope.exempted_departments[i].total);
            }

            return totalexempted;

        }
        var totalexfinancesFequence = function () {
            var totalexfinancefeq = 0;

            for (var i = 0; i < $scope.finances.length; i++) {
                totalexfinancefeq -= -($scope.finances[i].freq);
            }

            return totalexfinancefeq;

        }
		
		  $scope.exempted_department=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint90');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
//reports based on patient category
        $http.get('/api/payment_sub_category_to_set_price').then(function(data) {
            $scope.patientCategories=data.data;
        });
    $scope.getInsurance = function (item) {
        var report_generated_on = new Date() + "";
       var items ={category:item.category,start:item.start,end:item.end,facility_id:facility_id};
        $http.post('api/categoriesReport',items).then(function (data) {
            if(data.data.status==0){
                swal('',data.data.msg,'info');
            }else {
                $scope.categoriesReports=data.data;
                $scope.insuGrandTotal=$scope.insuPesa();
                $scope.insu_report_generated_on = report_generated_on.substring(0, 24);
            }
        });
    }
    //paid insurance
        $scope.getPaidInsurance = function (item) {
            var report_generated_on = new Date() + "";
            var items ={category:item.category,start:item.start,end:item.end,facility_id:facility_id};
            $http.post('api/paidInsuranceReports',items).then(function (data) {
                if(data.data.status==0){
                    swal('',data.data.msg,'info');
                }else {
                    $scope.paidCategoriesReports=data.data;
                    $scope.insuPaidGrandTotal=$scope.insuPaidPesa();
                    $scope.insu_report_generated_on = report_generated_on.substring(0, 24);
                }
            });
        }
        $scope.insuPesa = function () {
            var total = 0;
            for (var i = 0; i < $scope.categoriesReports.length; i++) {
                total -= -($scope.categoriesReports[i].quantity*$scope.categoriesReports[i].price);
            }
            return total;
        }
        $scope.insuPaidPesa = function () {
            var total = 0;
            for (var i = 0; i < $scope.paidCategoriesReports.length; i++) {
                total -= -($scope.paidCategoriesReports[i].quantity*$scope.paidCategoriesReports[i].price);
            }
            return total;
        }
        $scope.printInsuReport = function () {
//location.reload();
            var DocumentContainer = document.getElementById('insutoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        };
        $scope.printInsuPaidReport = function () {
//location.reload();
            var DocumentContainer = document.getElementById('insupaidtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        };

 $scope.getcancelsReport = function (item) {
            $http.post('/api/cancelsReport', {
                "start": item.start,
                "end": item.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cancels = data.data;
                var report_generated_on = new Date() + "";
                $scope.discount_report_generated_on = report_generated_on.substring(0, 24);
                $scope.cancelsGrandTotal = $scope.cancelsTotal();
            });
        }
        $scope.cancelsTotal = function () {
            var total = 0;
            for (var i = 0; i < $scope.cancels.length; i++) {
                total -= -($scope.cancels[i].amount_total);
            }
            return total;
        }
        $scope.Print_discount1 = function () {

            //location.reload();
            var DocumentContainer = document.getElementById('print_disc1');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        };
 $scope.printSubDepartmentReport = function () {
            
           //location.reload();
           var DocumentContainer = document.getElementById('subdepartmentdivtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
            
        };
    }
})();