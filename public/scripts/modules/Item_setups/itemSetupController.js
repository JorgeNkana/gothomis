(function() {

    'use strict';
    angular
        .module('authApp')
        .controller('itemSetupController', itemSetupController);

    function itemSetupController($http, $auth, $rootScope,$state,$location,$scope,$timeout, Helper) {

        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        var item_id;
		$scope.regex = /([^a-zA-Z0-9])/g;
        
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });
        $scope.getAllItems = function (text) {
            return Helper.getAllItems(text)
                .then(function (response) {
                    return response.data;
                });
        };

        //item registration CRUD

        $scope.item_exemptions = [];

       $scope.item_exemption = function (item, status,category) {
            if (status == undefined) {
                $('#itm').val('');
                swal('Oops!!', 'Please Choose Setting Option Above Either Exemption or OneTime Or Insurance Before', 'info');

                return;
            }

            // for (var i = 0; i < $scope.item_exemptions.length; i++) {
            //
            //
            //     if ($scope.item_exemptions[i].item_id == item.selectedItem.id) {
            //         swal(item.selectedItem.item_name + " already in your order list  ", "", "info");
            //         return;
            //     }
            // }
           if(category==undefined){
             var   sub_category_id=null;
           }
           else{
               sub_category_id=category.id;
           }

            $scope.item_exemptions.push({
                item_name: item.selectedItem.item_name,
                item_id: item.selectedItem.id,
                facility_id: facility_id,
                sub_category_id:sub_category_id
            });
        }
$scope.item_registrars = [];

        $scope.item_registrar = function (item) {
            $('#itm').val('');
            for (var i = 0; i < $scope.item_registrars.length; i++) {


                if ($scope.item_registrars[i].item_id == item.selectedItem.id) {
                    swal(item.selectedItem.item_name + " already in your order list  ", "", "info");
                    return;
                }
            }

            $scope.item_registrars.push({item_name: item.selectedItem.item_name, item_id: item.selectedItem.id,facility_id:facility_id});

        }

        $scope.item_exemption_set = function (status) {
            if (status == undefined) {
                swal('Oops!!', 'Please Choose Setting Option Above Either Exemption or OneTime Or Insurance Before', 'info');
                return;
            }
            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'SET',
                cancelButtonText: 'CANCEL',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/item_exemption_set', {
                    type: status.conf,
                    items:$scope.item_exemptions,
                    status: 1,
                    facility_id: facility_id
                }).then(function (data) {
                    $scope.item_exemptions = [];
                    var sending = data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )


                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }


        var insurances = [];

        $scope.payment_sub_categories_insurance=function (category,x) {

            for (var i = 0; i < insurances.length; i++) {


                if (insurances[i].sub_category_id == category.id) {
                    return;

                }
            }
            insurances.push({
                "facility_id": facility_id,
                "sub_category_id": category.id,

            });

//console.log(insurances);

    }

 $scope.item_insurance_beneficiary=function (status) {

            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'SET',
                cancelButtonText: 'CANCEL',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/item_exemption_set',{type:status.conf,items:$scope.item_exemptions,status:1,facility_id:facility_id,insurances:insurances}).then(function (data) {
                    insurances=[];
					$scope.item_exemptions = [];
                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )



                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }

$scope.item_insurance_non_beneficiary=function (status) {

            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'SET',
                cancelButtonText: 'CANCEL',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/item_exemption_set',{type:status.conf,items:$scope.item_exemptions,status:0,facility_id:facility_id, insurances:insurances}).then(function (data) {
                    insurances=[];
					$scope.item_exemptions = [];
                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )



                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }


        $scope.item_registrar_set=function () {

            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'SET',
                cancelButtonText: 'CANCEL',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/item_registrar_set',$scope.item_registrars).then(function (data) {
                    $scope.item_registrars=[];
                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )



                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }



         $scope.item_exemption_reset=function (status) {

            swal({
                title: 'Are you sure?',
                text: " ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'RESET',
                cancelButtonText: 'CANCEL',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {

                $http.post('/api/item_exemption_set',{type:status.conf,items:$scope.item_exemptions,status:0,facility_id:facility_id}).then(function (data) {
                    $scope.item_exemptions=[];
                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )



                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }


        $scope.removeItemArray = function(x){

            $scope.item_exemptions.splice(x,1);


        }
        $scope.removeRegistrarItemArray = function(x){

            $scope.item_registrars.splice(x,1);


        }

       $scope.item_registration=function (item_type_map) {

            if(item_type_map==undefined){
                swal(
                    'Feedback..',
                    'Please Fill All Required Fields',
                    'error'
                )
                return;
            }

            if(item_type_map.item_name==undefined){
                swal(
                    'Feedback..',
                    'Please Enter Item Name',
                    'error'
                )
                return;
            }
            if(item_type_map.dept_id==undefined){
                swal(
                    'Feedback..',
                    'Please Choose Item Department',
                    'error'
                )
                return;
            }

            if(item_type_map.item_category==undefined){
                swal(
                    'Feedback..',
                    'Please Choose Item Category',
                    'error'
                )
                return;
            }
            var itemsDAta={'item_name':item_type_map.item_name,'dept_id':item_type_map.dept_id};

            $http.post('/api/item_registration',itemsDAta).then(function(data) {
                var item_type_mapsDAta={
                    'item_id':data.data.id,'Dose_formulation':item_type_map.Dose_formulation,
                    'dispensing_unit':item_type_map.dispensing_unit,'item_category':item_type_map.item_category,
                    'item_code':item_type_map.item_code,'sub_item_category':item_type_map.sub_item_category,
                    'unit_of_measure':item_type_map.unit_of_measure,'strength':item_type_map.strength,volume:item_type_map.volume
                };
                ////console.log(item_type_map);
                $http.post('/api/item_type_map_registration',item_type_mapsDAta).then(function(data) {



                    $scope.item_list();
                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                });
            });
        }
		
        $scope.diagnosis_registry=function (item) {
            $('#code').val('');
            $('#description').val('');
            $http.post('/api/diagnosis_registry',{code:item.code,description:item.description}).then(function(data) {

var sending=data.data.msg;
 
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

            });
        }
        $scope.getsub_department_list=function () {
            $http.get('/api/getsub_department_list').then(function (data) {
                $scope.sub_depts = data.data;

            });
        }
        $scope.getsub_department_list();
        $scope.Sub_depts_items_list=function () {
            $http.get('/api/Sub_depts_items_list').then(function(data) {
                $scope.item_sub_depts_lists=data.data;

            });
        }

//displaying item when function clicked
        $scope.item_sub_department_registry=function (item) {

            $http.post('/api/item_sub_department_registry',{item_id:item.item_id.id,sub_dept_id:item.dept_id}).then(function(data) {
                $scope.iteems=data.data;
                var sending=data.data.status;
                var msg=data.data.msg;
                $('#item').val('');
                $('#sub').val('');
                if(sending==1){
                    swal(
                        'Feedback..',
                       msg,
                        'success'
                    )
                }
                else{
                    swal(
                        'Feedback..',
                       msg,
                        'error'
                    )
                }

            });
        }

        $scope.item_list=function () {

            $http.get('/api/item_list').then(function(data) {
                $scope.items=data.data;

            });
        }
        
        $scope.change_category=function (item) {
            
var dara={item_category_name:item.id_cat,id:item.selectedItem.id}
            $http.post('/api/change_category',dara).then(function(data) {

                var sending=data.data.status;
                var msg=data.data.msg;
                $('#item').val('');

                if(sending==1){
                    swal(
                        'Feedback..',
                        msg,
                        'success'
                    )
                }
            });
        }
var searhItem=[];

         
//displaying item when browser loading
        $scope.item_searching=function (item) {


            $http.get('/api/item_searching/'+item).then(function (data) {
                searhItem = data.data;

            });
            return searhItem;
        }

//displaying item when browser loading
        $http.get('/api/department_list').then(function(data) {
            $scope.departments=data.data;

        });

        //  update


        $scope.item_update=function (item) {

            var itemsDAta={'id':item.id,'item_name':item.item_name,'dept_id':item.dept_id};

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


                $http.post('/api/item_update',itemsDAta).then(function (data) {


                    $scope.item_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updated....',
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
        $scope.item_delete=function (item,id) {

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


                $http.get('/api/item_delete/'+id).then(function(data) {


                    $scope.item_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted..',
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



        //item type mapping registration CRUD



        $scope.item_type_map_registration=function (item_type_map) {

            var item_type_mapsDAta={
                'item_id':item_type_map.selectedItem.id,'Dose_formulation':item_type_map.Dose_formulation,
                'dispensing_unit':item_type_map.dispensing_unit,'item_category':item_type_map.item_category,
                'item_code':item_type_map.item_code,'sub_item_category':item_type_map.sub_item_category,
                 'unit_of_measure':item_type_map.unit_of_measure,'strength':item_type_map.strength,volume:item_type_map.volume
            };
            ////console.log(item_type_map);
            $http.post('/api/item_type_map_registration',item_type_mapsDAta).then(function(data) {



                $scope.item_type_map_list();
                var sending=data.data.msg;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

            });
        }

//displaying item_type_map when function clicked
        $scope.item_type_map_list=function () {

            $http.get('/api/item_type_map_list').then(function(data) {
                $scope.item_type_maps=data.data;

            });
        }
        $http.get('/api/item_type_map_list').then(function(data) {
            $scope.item_type_maps=data.data;

        });
//displaying item_type_map when browser loading
        $http.get('/api/department_list').then(function(data) {
            $scope.departments=data.data;

        });

        //  update


        $scope.item_type_map_update=function (item_type_map) {

            var item_type_mapsDAta={'item_type_map_name':item_type_map.item_type_map_name,'dept_id':item_type_map.dept_id};
           // ////console.log(item_type_mapsDAta)
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


                $http.post('/api/item_type_map_update',item_type_mapsDAta).then(function (data) {


                    $scope.item_type_map_list();
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
        $scope.item_type_map_delete=function (item_type_map,id) {

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


                $http.get('/api/item_type_map_delete/'+id).then(function(data) {


                    $scope.item_type_map_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted...',
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



//item category registration CRUD



        $scope.item_category_registration=function (item_category) {
             ////console.log(item_category);
            var item_categoriesDAta={'item_category_name':item_category.item_category_name};

            $http.post('/api/item_category_registration',item_categoriesDAta).then(function(data) {

                $scope.item_category_list();
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

            });
        }

//displaying item_category when function clicked
        $scope.item_category_list=function () {

            $http.get('/api/item_category_list').then(function(data) {
                $scope.item_categories=data.data;

            });
        }
        $scope.item_category_list();


        //  update


        $scope.item_category_update=function (item_category) {
            ////console.log(item_category)
            var item_categoriesDAta=item_category;
////console.log(item_categoriesDAta)
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


                $http.post('/api/item_category_update',item_categoriesDAta).then(function (data) {


                    $scope.item_category_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updated....',
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
        $scope.item_category_delete=function (item_category,id) {

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


                $http.get('/api/item_category_delete/'+id).then(function(data) {


                    $scope.item_category_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted..',
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
   $scope.selectedItem = function (item) {
            if (typeof item != 'undefined') {
                 item_id = item.id;
                //console.log(item_id)
            }
                $scope.item = item.id;
        }
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

        $scope.item_price_registration=function (item_price,item) {
            var prices = [];
            var field_id;
            $scope.payment_sub_categories.forEach(function(category) {

                field_id = category.sub_category_name.replace($scope.regex, '_');
                if($("#"+field_id).val() != ''){
                    prices.push({
                        "facility_id":facility_id,
                        "sub_category_id":category.id,
                        "price":$("#"+field_id).val().replace(',',''),
                        "item_id":$scope.item,
                        "startingFinancialYear": item_price.startingFinancialYear,
                        "endingFinancialYear": item_price.endingFinancialYear,

						
                    });
                    $("#"+field_id).val('');
                }
            });




           /* var item_pricesDAta = {
                'sub_category_id': item_price.sub_category_id,
                'price': item_price.price,
                'item_id':$scope.item,
                'facility_id': facility_id,
                'startingFinancialYear': item_price.startingFinancialYear,
                'endingFinancialYear': item_price.endingFinancialYear
            };*/
            //  ////console.log(item_pricesDAta);
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

            });
        }
        $scope.load_item_priced_list_search=function (item_id) {
            $http.post('/api/load_item_priced_list_search',{facility_id:facility_id,item_id:item_id.id}).then(function(data) {
                $scope.item_prices=data.data;

            });
        }
        $scope.load_item_list_search=function (item_id) {
            $http.post('/api/load_item_list_search',{item_id:item_id.id}).then(function(data) {
                $scope.items=data.data;
            });
        }
        $scope.departments_list=function () {
            $http.get('/api/getdepartments').then(function(data) {
                $scope.departments=data.data;
            });
        }

        $scope.load_sub_dept_item_list_search=function (item_id) {
            $http.post('/api/load_sub_dept_item_list_search',{item_id:item_id.id}).then(function(data) {
                $scope.item_sub_depts_lists=data.data;
            });
        }
        $scope.departmentRegistration = function (department) {
            if (department == undefined) {
                swal(
                    'Department is missing',
                    'Register all required field!',
                    'error'
                )
            }
            else if (department.department_name.id == undefined) {
                swal(
                    'Department is missing',
                    'Register Department!',
                    'error'
                )
            } else if (department.name == undefined) {
                swal(
                    'Sub-Department is missing',
                    'Register sub dept!',
                    'error'
                )
            }
            else {
                var departmentData = {
                    'sub_department_name': department.name,
                    'department_id': department.department_name.id,
                    'eraser': 1
                };
                $http.post('/api/departmentRegistration',departmentData).then(function(data) {
                    //console.log(data.data);
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if(status==0){
                        swal(
                            'Error',
                            msg,
                            'error'
                        )
                    }
                    else{
                        swal(
                            'Success Registration',
                            msg,
                            'success'
                        )
                    }

                });
            }
        };

        $scope.sub_item_update=function (item) {

            var itemsDAta={item_id:item.item_id,'id':item.id,'item_name':item.item_name,'sub_dept_id':item.sub_dept_id};

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


                $http.post('/api/sub_item_update',itemsDAta).then(function (data) {
                    $scope.Sub_depts_items_list();
                    var sending=data.data;
                    swal(
                        '',
                        'Updated....',
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



		var gfs_list=[];
			
		$scope.gfs_list_search=function (item) {
			$http.post('/api/gfs_list_search',{'search':item}).then(function(data) {
				gfs_list=data.data;

			});
			return gfs_list;
		}

		 $scope.gfs_mapping_list=function () {

            $http.get('/api/gfs-mappings').then(function(data) {
                $scope.gfs_mappings=data.data;

            });
        }

        $scope.gfs_registration=function (gfs_mapping) {
			$http.post('/api/mapGfsCodes', {item_id: gfs_mapping.item.id, gfs_code_id: gfs_mapping.gfs.id}).then(function (data) {
				if (data.data.status != 200) {
					swal(
						'Error',
						data.data.message,
						'error'
					)
				}
				else {
					swal(
						'Success',
						data.data.message,
						'success'
					)
					$scope.gfs_mapping_list();
				}

			});
        }
		
		$scope.delete_gfs_mapping=function (id) {
            $http.get('/api/delete-gfs-mapping/' + id).then(function(data) {
               swal(
						'Success',
						data.data.message,
						'success'
					)
				$scope.gfs_mapping_list();
            });
        }
    }

})();