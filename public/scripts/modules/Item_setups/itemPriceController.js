/**
 * Created by USER on 2017-02-25.
 */
/**
 * Created by USER on 2017-02-25.
 */
/**
 * Created by USER on 2017-02-13.
 */
/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('itemPriceController', itemPriceController);

    function itemPriceController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {
		$scope.regex = /\s/g;
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////console.log($scope.menu);

        });
var item_list=[];
        $scope.item_ist_search=function (item) {
            $http.post('/api/item_ist_search',{'search':item}).then(function(data) {
                item_list=data.data;

            });
            return item_list;
        }

        //item_price registration CRUD
// items list
        $http.get('/api/item_list').then(function(data) {
            $scope.items=data.data;

        });

        $http.get('/api/payment_sub_category_to_set_price').then(function(data) {
            $scope.payment_sub_categories=data.data;

        });
		
		$scope.itemWardGradeSearch=function (item) {
            $http.post('/api/itemWardGradeSearch',{'search':item}).then(function(data) {
                item_list=data.data;

            });
            return item_list;
        }


         $scope.itemLabSearch=function (item) {
            $http.post('/api/itemLabSearch',{'search':item}).then(function(data) {
                item_list=data.data;

            });
            return item_list;
        }

        $scope.item_price_registration=function (item_price) {

            var prices = [];
            var field_id;
            $scope.payment_sub_categories.forEach(function(category) {

                field_id = category.sub_category_name.replace(' ', '_');
                if($("#"+field_id).val() != ''){
                    prices.push({
                        "facility_id":facility_id,
                        "sub_category_id":category.id,
                        "price":$("#"+field_id).val().replace(',',''),
                        "item_id": item_price.selectedItem.id,
                        "startingFinancialYear": item_price.startingFinancialYear,
                        "endingFinancialYear": item_price.endingFinancialYear,

                    });
                    $("#"+field_id).val('');
                }
            });




                var item_pricesDAta = {
                    'sub_category_id': item_price.sub_category_id,
                    'price': item_price.price,
                    'item_id': item_price.selectedItem.id,
                    'facility_id': facility_id,
                    'startingFinancialYear': item_price.startingFinancialYear,
                    'endingFinancialYear': item_price.endingFinancialYear
                };
                //  //console.log(item_pricesDAta);
                $http.post('/api/item_price_registration', prices).then(function (data) {


                    var sending = data.data;
                    var msg = data.data.msg;
                    if (data.data.status == 0) {
                        swal(
                            'Error',
                            msg,
                            'error'
                        )
                    }
                    else {
                        swal(
                            'Success',
                            msg,
                            'success'
                        )
                        $scope.item_price_list();
                    }

                });

             

        }
   $scope.itemLabSearch=function (item) {
            $http.post('/api/itemLabSearch',{'search':item}).then(function(data) {
                item_list=data.data;

            });
            return item_list;
        }

//displaying item_price when function clicked
        $scope.item_price_list=function () {

            $http.get('/api/item_price_list/'+facility_id).then(function(data) {
                $scope.item_prices=data.data;
				console.log($scope.item_prices);

            });
        }
        
//displaying item_price when browser loading
        $http.get('/api/department_list').then(function(data) {
            $scope.departments=data.data;

        });

        //  update


        $scope.item_price_update=function (item_price) {

            var item_pricesDAta={'id':item_price.id,'price':item_price.price,'facility_id':facility_id,
                'startingFinancialYear':item_price.startingFinancialYear,'endingFinancialYear':item_price.endingFinancialYear};
            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/item_price_update',item_pricesDAta).then(function (data) {


                    $scope.item_price_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updated..',
                        'success'
                    )
                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })



           
             
        }


//  delete
        $scope.item_price_delete=function (item_price,id) {
            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.get('/api/item_price_delete/'+id).then(function(data) {


                    $scope.item_price_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted',
                        'info'
                    )
                })



            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })

            
                
        }


 
 $scope.load_item_price_per_categories=function (cat) {

            $http.post('/api/load_item_price_per_categories',cat).then(function(data) {
                $scope.item_prices_cats=data.data;

            });
        }
$scope.print_loaded_item_price_per_categories=function () {
    //location.reload();
    var DocumentContainer = document.getElementById('print_p_c_id');
    var WindowObject = window.open("", "PrintWindow",
        "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
    WindowObject.document.title = "printout: prices";
    WindowObject.document.writeln(DocumentContainer.innerHTML);
    WindowObject.document.close();

    setTimeout(function () {
        WindowObject.focus();
        WindowObject.print();
        WindowObject.close();
    });
}







    }

})();