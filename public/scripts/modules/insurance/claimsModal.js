/**
 * Created by Mazigo Jr on 2017-04-18.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('claimsModal',['$scope','$http','$rootScope','$uibModal' ,'$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal, $uibModalInstance, object) {
            var facility_id = $rootScope.currentUser.facility_id;
            var user_id = $rootScope.currentUser.id;
            $scope.claims = object;
            var bills = [];
            var fee = 0;
            bills =  $scope.claims;
          var dt = object.date_attended;
          var pt = object.patient_id;
            //console.log(dt);
            //console.log(pt);
            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
                //////console.log($scope.menu);

            });

            angular.element(document).ready(function () {
                $scope.claimsData=[];
                $http.post('/api/investigationDone',{"facility_id":facility_id,"patient_id":pt,"date_attended":dt}).then(function (data) {
					$scope.investigationLists=data.data[0];
					$scope.procedures=data.data[2];

for(var i=0;i<data.data.length;i++){
    $scope.claimsData.push({
        item_name:data.data[0][i].item_name,inv_code:data.data[0][i].inv_code,price:data.data[0][i].price,doctor_name:data.data[0][i].user_name,
        mobile_number:data.data[0][i].mobile_number,profession:data.data[0][i].prof_name,
        medicine:data.data[1][i].medicine,medi_code:data.data[1][i].medi_code,
        quantity:data.data[1][i].quantity, sub_med_total:data.data[1][i].sub_med_total,
        procedure_name:data.data[2][i].procedure_name, proc_code:data.data[2][i].proc_code,
        proc_price:data.data[2][i].proc_price, procedure_category:data.data[2][i].procedure_category,

    })
}

               $scope.MedTotal1=     $scope.medTotal($scope.claimsData);
               $scope.invTotal1=     $scope.medTotal($scope.claimsData);
               $scope.procTotal1=     $scope.procTotal($scope.claimsData);

                });
               });

            $http.post('/api/getConfirmed',{"patient_id":pt,"date_attended":dt}).then(function (data) {
                $scope.diagnosis = data.data;
                //console.log(data.data)
            });

            $http.get('/api/getConsultationFee/'+facility_id).then(function (data) {
                $scope.fee = data.data;
                fee = data.data[0].price;

            });

                $scope.invTotal = function () {
                    var  total = 0;
                    for(var i = 0; i <  $scope.investigationLists.length ; i++) {
                        total += ( $scope.investigationLists[i].price);
                    }
                    return total;
                }
                $scope.medTotal = function () {
                    var  total = 0;
                    for(var i = 0; i <  $scope.claimsData.length ; i++) {
                        total += ( $scope.claimsData[i].sub_med_total);
                    }
                    return total;
                }
                $scope.procTotal = function () {
                    var  total = 0;
                    for(var i = 0; i <  $scope.claimsData.length ; i++) {
                        total += ( $scope.claimsData[i].proc_price);
                    }
                    return total;
                }
                $scope.grandTotal = function () {
                    var med= $scope.medTotal();
					var inve=$scope.invTotal();
                    var proc=$scope.procTotal();
				
                }
				
				$scope.sendClaim = function(claim) {
                    $scope.dataLoading = true;
                  var creditials={"visit_id":claim.visit_id,"facility_id":claim.facility_id};
                $http.post('/api/createPatientFolio',creditials).then(
                    function (response) {
                        console.log(response.data);
                        if(response.data.StatusCode==0) {
                            return toastr.error('','Check Internet connection');
                        }
                        else  if(response.data.StatusCode==500) {
                            var  remarks = response.data.Message;
                             return sweetAlert(remarks, "", "error");
                     
                        }
                        else  if(response.data.StatusCode==200) {
                             //var  remarks = response.data.Message;
                             var  remarks = response.data.Message;
                             return sweetAlert(remarks, "", "success");
                     
                        }
                        else{
                            return toastr.error('','Something went wrong ,Try again.');
                        }
                    },
                    function (data) {
                        // Handle error here
                        toastr.error('','Error in Claims submissions!');
                    }).finally(function () {
                    $scope.dataLoading = false;
                });




            };
				
				
            $scope.printInvoice = function printInvoice()
            {
                var DocumentContainer = document.getElementById('contentToPrint');
                var WindowObject = window.open("", "PrintWindow",
                    "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
                WindowObject.document.title = "Print Receipt: GoT-HOMIS";
                WindowObject.document.writeln(DocumentContainer.innerHTML);
                WindowObject.document.close();

                setTimeout(function(){
                    WindowObject.focus();
                    WindowObject.print();
                    WindowObject.close();
                },2000);

                $state.reload();

            }


    }]);
}());