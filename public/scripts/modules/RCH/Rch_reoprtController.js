/**
 * Created by USER on 2017-04-19.
 */
/**
 * Created by USER on 2017-03-27.
 */
/**
 * Created by USER on 2017-02-24.
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
        .controller('Rch_reoprtController', Rch_reoprtController);

    function Rch_reoprtController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            //////console.log($scope.menu);

        });
        var date=new Date();

        var rch_child_attendance={facility_id:facility_id,start_date:'2017-01-01',end_date:'2017-12-01'}
 $http.post('/api/getChildAttendanceReport',rch_child_attendance).then(function(data) {
            $scope.attendances=data.data;

        });

        $http.post('/api/Anti_natl_mtuha',rch_child_attendance).then(function(data) {
            $scope.antinatal=data.data;

        });
        $http.post('/api/getChildfeedingReport',rch_child_attendance).then(function(data) {
            $scope.feeds=data.data;


        }); 
        $http.post('/api/getChilddewormgivenReport',rch_child_attendance).then(function(data) {
            $scope.deworms=data.data;
            
        });
        $http.post('/api/getChildGrowthAttendanceReport',rch_child_attendance).then(function(data) {
            $scope.growth_att=data.data;


        });

        $scope.pdf = {
            src: 'example.pdf',
        };

        $scope.data = null; // this is loaded async

        $http.get("/api/getUsermenu/"+user_id, {
            responseType: 'arraybuffer'
        }).then(function (response) {
            $scope.data = new Uint8Array(response.data);
        });

    }

})();
/**
 * Created by USER on 2017-03-08.
 */