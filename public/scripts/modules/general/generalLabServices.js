(function() {
    'use strict';

    var app = angular.module('authApp')

    app.factory('Laboratory', ['$http', function($http) {
        return {
            test_list : function() {
                return $http.get('/api/investigation-test-list')
                    .then(function(response) {
                        return response;
                    });
            },investigation_verify : function() {
                return $http.get('/api/investigation-verify')
                    .then(function(response) {
                        return response;
                    });
            },
            investigations : function (id) {
                return $http.post('/api/investigation-lists',{
                    "facility_id": id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            doct_performances : function (id) {
                console.log(id.start_date);
                return $http.post('/api/doctor-performance',{
                    "facility_id": id.facility_id,
                    "start_date": id.start_date,
                    "end_date": id.end_date,
                })
                    .then(function (response) {
                        return response;
                    });
            },
            sample : function (id) {
                return $http.post('/api/order-lists',{
                    "order_id": id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            test_per_patient : function (id) {
                return $http.post('/api/test-per-patient',{
                    "patient_id": id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            getLabPatients : function (text) {
                return $http.post('/api/labpatients-lists',{
                    "name": text,
                })
                    .then(function (response) {
                        return response;
                    });
            },
            getInvestigationPatients : function (text) {
                return $http.post('/api/get-investigation-lists',{
                    "name": text,
                })
                    .then(function (response) {
                        return response;
                    });
            },


        };
    }]);

})();