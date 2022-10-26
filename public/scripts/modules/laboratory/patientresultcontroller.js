(function () {

'use strict';

var app = angular.module('authApp');

app.controller('patientresultcontroller',
['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
$scope.loadpostresultform=object;
//console.log(object);

//Send patient laboratory results per order
var user_id=$scope.currentUser.id;
$scope.send_lab_result=function(loadpostresultform)
{
if (angular.isDefined(loadpostresultform.results)==false) {
return sweetAlert("You cannot post empty results", "", "error");
}
else{
//console.log(loadpostresultform);
var abc=(
	{
		//"value":loadpostresultform,
		"order_id":loadpostresultform.patientorder[0].laborderid,
		"result":loadpostresultform.results,
		"item_name":loadpostresultform.patientorder[0].item_name,
		"user_id":user_id
	});
//console.log(abc);
$http.post('/api/send_lab_result',abc).then(function (data) {
$scope.patientresults=data.data;
var status=data.data.status;
	var msg=data.data.msg;
	if(status==0){
		swal('Oops!!',msg,'error');
	}
	else{
		swal('success',msg,'success');
	}

});
}
}




}]);


}());