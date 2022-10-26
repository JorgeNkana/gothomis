/**
 * Created by APPLE on 19/03/2017.
 */
(function () {

    'use strict';

    angular
        .module('authApp').directive('ngFiles', ['$parse', function ($parse) {

        function fn_link(scope, element, attrs) {
            var onChange = $parse(attrs.ngFiles);
            element.on('change', function (event) {
                onChange(scope, { $files: event.target.files });
            });
        };

        return {
            link: fn_link
        }
    } ])


    var app = angular.module('authApp');

    app.controller('queManagementModal',

        ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
            function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {

                //console.log(object);
                $scope.XrayQue = object;
                var user_name=$rootScope.currentUser.id;
                var facility_id=$rootScope.currentUser.facility_id;
                var formdata = new FormData();





                // NOW UPLOAD XRAY FILES.
                $scope.xrayUpload = function (explanation,orders,mrns,patient_ids) {


                    //console.log(explanation);
                    //console.log(orders);
                    //console.log(mrns);
                    //console.log(patient_ids);

                    formdata.append('explanation',explanation.explanation);
                    formdata.append('mrn',mrns);
                    formdata.append('order',orders);
                    formdata.append('patient_id',patient_ids);
                    var request = {
                        method: 'POST',
                        url: '/api/'+'xrayImage',
                        data: formdata,
                        headers: {
                            'Content-Type': undefined
                        }

                    };

                    // SEND THE FILES.
                    $http(request).then(function (data) {
                        //console.log(request);
                        swal({
                            title: '',
                            html: $('<div>')
                                .addClass('some-class')
                                .text(''+data.data+''),
                            animation: false,
                            customClass: 'animated tada'
                        });


                    })
                        .then(function () {
                        });
                }




            }]);





}());