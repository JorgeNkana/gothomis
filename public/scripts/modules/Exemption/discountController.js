/**
 * Created by USER on 2017-02-26.
 */
/**
 * Created by USER on 2017-02-18.
 */
/**
 * Created by USER on 2017-02-14.
 */

(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('discountController', discountController);

    function discountController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {

        $http.get('/api/facility_list').then(function(data) {
            $scope.facilities=data.data;

        });



        
        $scope.ExportFile=function () {
            $http.get('public/downloadExcel/xls').then(function(data) {

               console.log( data) ;

            });
        }
       

        var resdata =[];
        $scope.showSearch = function(searchKey) {
            if(searchKey.length<5){
 
            }
 else{
                $http.post('/api/searchpatientForBill',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;
                });
                return resdata;
            }
            }



var user_id=$rootScope.currentUser.id;
var facility_id=$rootScope.currentUser.facility_id;

        //user menu
        $scope.printUserMenu=function (user_id) {

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;


            });

        }
        var user_id=$rootScope.currentUser.id;
        $scope.printUserMenu(user_id);


        //Exemption (CRUD) Registration
        var patientName= $scope.selectedPatient;

        $scope.exemption_registration=function (exempt) {




            var exemption_data={'exemption_no':1,'user_id':$rootScope.currentUser.id,'facility_code':$rootScope.currentUser.facility_id,"patient_id":exempt.selectedPatient.patient_id,"status_id":exempt.status_id,"exemption_type_id":exempt.exemption_type_id,
                "exemption_reason":exempt.exemption_reason,"reason_for_revoke":exempt.reason_for_revoke,'description':exempt.description};
            console.log(exemption_data)
            $http.post('/api/patient_exemption',exemption_data).then(function(data) {
                console.log(data);
            });
        }

        

        $scope.loadBill=function (selectedPatient) {


            $http.get('/api/loadDiscountBill/'+selectedPatient.patient_id).then(function(data) {
                $scope.discounts=data.data;
                $scope.previusDiscount = calcDiscountfromDB($scope.discounts);

                $scope.jumla = calctotal($scope.discounts);

$scope.TotalDiscount="";

            });
        }
        $scope.discountArray=[];
       var discount_reason="";


        $scope.discounting=function (discount) {

var asilimia=(discount.amount/(discount.price * discount.quantity) * 100);

    if ((discount.price * discount.quantity) - discount.amount >= 0){
    $scope.discountArray.push({'patient_id':discount.patient_id,'receipt_number':discount.invoice_id,'id':discount.item_refference,'user_id':user_id,'invoice_id':discount.item_refference,
        'quantity':discount.quantity,'price':discount.price,'discount':discount.amount});

console.log( $scope.discountArray);
    $scope.TotalDiscount=calcDiscountFromSocial($scope.discountArray);

 }
else if(discount.amount ==null){

    }
            else{
        swal(
            'Warning',
            'Please Check again Your Discount. Otherwise Your discount for any row data exceeds limit, will be ignored',
            'warning'
        )
  }




}
var discounting_resaon="";
        $scope.discount_reason=function (reason) {
            discount_reason=reason;

            console.log(discounting_resaon);
        }


        $scope.CommitDiscount=function () {


            if($scope.discountArray.length >0)
            {

                swal({
                    title: 'Are you sure?',

                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!  ',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {

//please write reasons for this discount

                    swal({
                        title: 'Reasons for This discount',
                        input: 'textarea',

                        showCancelButton: true,
                        inputValidator: function (value) {
                            return new Promise(function (resolve, reject) {
                                if (value) {
                                    resolve()
                                } else {
                                    reject('You need to write Reasons for This discount!')
                                }



                             //--------------------




                             //--------------------
                            })
                        }
                    }).then(function (result) {
                        discounting_resaon={'discount_reason':result,'patient_id':$scope.discountArray[0].patient_id,
                            'receipt_number':$scope.discountArray[0].receipt_number,'facility_id':facility_id};

                        $http.post('/api/invoice_discount',$scope.discountArray).then(function(data) {
                            if(data){
                                $http.post('/api/discountingReason',discounting_resaon).then(function(data) {

                                });
                            }


                            swal(

                                'Success!!!',
                                'Discount Successful Granted..',
                                'success'
                            )

                            $scope.discountArray=[];

                        });
                    })





                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Discount Has Cancelled',
                            'error'
                        )
                    }
                })



            }
            else{
                swal(

                    'Oops!!!..',
                    'Discount Already Granted..',
                    'info'
                )
            }







        }
        $scope.Create_debt = function (trans_id) {
            $scope.debt=[];
            $scope.debt.push({id:trans_id.id,patient_id:trans_id.patient_id});

            swal({
                title: 'Are you sure You Want To Change This Into DEBT ?',

                text: "This can not be Reversed",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!  ',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/Create_debt',$scope.debt).then(function (data) {

                    var sending = data.data.msg;

                    if (data.data.status == 0) {
                        swal(
                            'Error',
                            sending,
                            'error'
                        )
                    }
                    else {
                        swal(
                            'Success',
                            sending,
                            'success'
                        )
                    }


                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })
        }




        //calculation of transactions from all Point Of services
        var calctotal = function(){
            var sum = 0;

            for(var i=0; i<$scope.discounts.length;i++){
                sum -= -($scope.discounts[i].price * $scope.discounts[i].quantity);
            }

            return sum;

        }
        //calculation of transactions Discount from all Point Of services by social welfare officer
 var calcDiscountFromSocial = function(){
            var DiscountFromSocial = 0;

            for(var i=0; i<$scope.discountArray.length;i++){
                DiscountFromSocial -= -($scope.discountArray[i].discount);
            }

            return DiscountFromSocial;

        }

        //calculation of transactions Discount from all Point Of services  though by default is zero discount
        var calcDiscountfromDB = function(){
            var TotalDiscountfromDB = 0;

            for(var i=0; i<$scope.discounts.length;i++){
                TotalDiscountfromDB -= -($scope.discounts[i].discount);
            }

            return TotalDiscountfromDB;

        }
$scope.swalImage=function () {
    swal({
        title: 'Select image',
        input: 'file',
        timer: 2000,
        inputAttributes: {
            accept: 'image/*'
        }
    }).then(function (file) {
        var reader = new FileReader
        reader.onload = function (e) {
            swal({
                imageUrl: e.target.result

            })
            console.log(e.target.result);
        }
        reader.readAsDataURL(file)
        console.log(reader);
    })
}


     
    }

})();