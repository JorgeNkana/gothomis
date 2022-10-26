/**
* Created by USER on 2017-02-13.
*/
(function() {

'use strict';

angular
.module('authApp')
.controller('laboratorySettingController', laboratorySettingController);

function laboratorySettingController($http, $auth, $rootScope,$state,$location,$scope,$uibModal) {
$scope.setTab = function(newTab){
$scope.tab = newTab;
};
$scope.isSet = function(tabNum){
return $scope.tab === tabNum;
}

angular.element(document).ready(
function(){
	$scope.setTab(1);
});


//Search Test Unit
var item_units=[];
$scope.showItemtest=function (searchData) {
var searchUnit={'SearchUnit':searchData};
$http.post('/api/getunit',searchUnit).then(function(data) {
item_units=data.data;
});
return item_units;
}

//Search Color
var color=[];
$scope.showColor=function (searchData) {
var searchColor={'SearchColor':searchData};
$http.post('/api/getcolor',searchColor).then(function(data) {
color=data.data;
});
return color;
}

//Search Test Indicator
var item_indicator=[];
$scope.showTestindicator=function (searchData) {
var searchIndicator={'SearchIndicator':searchData};
$http.post('/api/getindicator',searchIndicator).then(function(data) {
item_indicator=data.data;
});
return item_indicator;
}

//Search Sample to Collect
var sample=[];
$scope.showSample=function (searchData) {
var searchSamples={'SearchSample':searchData};
$http.post('/api/getsample',searchSamples).then(function(data) {
sample=data.data;
});
return sample;
}

//Search Sample to Collect
var eqstatus=[];
$scope.showEqstatus=function (searchData) {
var searchEqustatus={'SearchEqstatus':searchData};
$http.post('/api/getequpstatus',searchEqustatus).then(function(data) {
eqstatus=data.data;
});
return eqstatus;
}

//Search Facility
var facilitys=[];
$scope.showFacility=function (searchData) {
var searchFacility={'SearchFacility':searchData};
$http.post('/api/getfacility',searchFacility).then(function(data) {
facilitys=data.data;
});
return facilitys;
}

//Search Sub Department
var sub_department=[];
$scope.showSubdep=function (searchData) {
var searchSubdep={'SearchSubdepartment':searchData};
$http.post('/api/getsubdepartments',searchSubdep).then(function(data) {
sub_department=data.data;
});
return sub_department;
}

//Service Equipment
var equipment=[];
$scope.showequipment=function (searchData) {
var searchEquipment={'SearchEquipment':searchData};
$http.post('/api/getequipements',searchEquipment).then(function(data) {
equipment=data.data;
});
return equipment;
}

//api to auto load department
$http.get('/api/get_department').then(function(data) {
$scope.department=data.data;
});

//api to auto load equipment
$http.get('/api/getequipement_status').then(function(data) {
$scope.equip_status=data.data;
});

//Patient Search Data From the database
var patient_ifo=[];
$scope.showPatient=function (searchData) {
var searchPatient={'SearchPatient':searchData};
$http.post('/api/getpatient',searchPatient).then(function(data) {
patient_ifo =data.data;
});
return patient_ifo;
}	

//Search Service from the database
var service_ifo=[];
$scope.showService=function (searchData) {
var searchService={'SearchService':searchData};
$http.post('/api/getservice',searchService).then(function(data) {
service_ifo=data.data;
});
return service_ifo;
}

//On select capture service selected
var currentUser=$scope.currentUser.id;
$scope.service_selected=[];
$scope.onSelect=function(service){
$scope.service_selected.push(
{
"id":service.service.id,
"Service_Name":service.service.item_name,
"patient_id":service.selectedpatient.id,
"doctor_id":currentUser
}
);
}

$scope.remove=function($index){
$scope.service_selected.splice($index,1);	
}


//send to lab 
$scope.send_to_lab=function()
{
var sendtolab=$scope.service_selected;
//console.log(sendtolab);
$http.post('/api/send_to_lab',sendtolab).then(function (data) {
//lab_service_ifo=data.data;
});
}

//Get Sample Status
$scope.getsample_status=function () {
$http.get('/api/getsample_status').then(function(data) {
$scope.sample_status=data.data;
});
}

//Register Samples
$scope.sample_status_registration=function(sample) {
if (angular.isDefined(sample)==false) {
return sweetAlert("Please Enter Sample Status", "", "error");
}
else{
var sample_name={'status':sample.sample_name};
$http.post('/api/sample_status_registration',sample_name).then(function (data) {
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
sample_name='';
}
});
}
}

//Update Sample status
$scope.sample_status_update=function (samplestatus) {
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
	var samplestatuss={'status':samplestatus.sample_status};
$http.post('/api/sample_status_update',samplestatuss).then(function (data) {
var sending=data.data;
swal(
'Feedback..',
'Updates Success...',
'success'
)
})
}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//delete sample status
$scope.sample_status_delete=function(samplestatus) {
if (angular.isDefined(samplestatus)==false) {
return sweetAlert("Please Enter Sample Status", "", "error");
}
else{
//var samplestatus=samplestatus;
var samplestatus={'status':samplestatus.sample_status};
$http.get('/api/sample_status_delete/'+samplestatus).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
}
});
}
}

//Get Equipment Status
$scope.getequipement_status=function () {
$http.get('/api/getequipement_status').then(function(data) {
$scope.equip_status=data.data;
});
}


//Register Equipment Status
$scope.equipment_status_registration=function(equipmentnstatus) {
if (angular.isDefined(equipmentnstatus)==false) {
return sweetAlert("Please Enter Equipment Status", "", "error");
}
else{
var equipment_status=equipmentnstatus;
$http.post('/api/equipment_status_registration',equipment_status).then(function (data) {
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
equipmentnstatus='';
}
});
}
}


//Update Equipment status
$scope.equipement_status_update=function (equipstatus) {
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
$http.post('/api/equipement_status_update', equipstatus).then(function (data) {
var sending=data.data;
swal(
'Feedback..',
'Updates Success...',
'success'
)
})
}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//delete Equipment status
$scope.equipement_status_delete=function(equipstatus) {
if (angular.isDefined(equipstatus)==false) {
return sweetAlert("Please Enter Sample Status", "", "error");
}
else{
var equipstatus=equipstatus;
$http.get('/api/equipement_status_delete/'+equipstatus.id).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
equipstatus='';
}
});

}
}

//api to auto load equipment
$http.get('/api/getequipement').then(function(data) {
$scope.equipment=data.data;
});

//Function to Get Equipment 
$scope.getequipement=function () {
$http.get('/api/getequipement').then(function(data) {
$scope.equipment=data.data;
});
}

//Equipment_registration
var facility=$rootScope.currentUser.facility_id;
$scope.equipment_registration=function(equip) {
if (angular.isDefined(equip)==false) {
return sweetAlert("Please Enter Below Details", "", "error");
}
if (angular.isDefined(equip.equipment_name)==false) {
return sweetAlert("Please Enter Equipment Name", "", "error");
}
if (angular.isDefined(equip.reagents)==false) {
return sweetAlert("Please Enter Equipment Reagents", "", "error");
}
if (angular.isDefined(equip.minimum_limit)==false) {
return sweetAlert("Please Enter Equipment Minimum Limit", "", "error");
}
if (angular.isDefined(equip.maximum_limit)==false) {
return sweetAlert("Please Enter Equipment Maximum Limit", "", "error");
}
if (angular.isDefined(equip.item_units)==false) {
return sweetAlert("Please Enter Equipment SI-Unit", "", "error");
}
if (angular.isDefined(equip.id)==false) {
return sweetAlert("Please Select Equipment Status", "", "error");
}
else{
var equipe=(
{
"equipment_name":equip.equipment_name,
"reagents":equip.reagents,
"minimum_limit":equip.minimum_limit,
"maximum_limit":equip.maximum_limit,
"si_unit":equip.item_units.id,
"id":equip.id,
"facility_id":facility
}
);
//console.log(equipe);
$http.post('/api/equipment_registration',equipe).then(function (data) {
$scope.equipmentt=data.data;
$scope.equip="";
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
equip='';
}
});
}
}


//Update Equipment 
$scope.equipement_update=function (equipment) {
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
if (angular.isDefined(equipment)==false) {
return sweetAlert("Please Enter Below Details", "", "error");
}
if (angular.isDefined(equipment.equipment_name)==false) {
return sweetAlert("Please Enter Equipment Name", "", "error");
}
if (angular.isDefined(equipment.reagents)==false) {
return sweetAlert("Please Enter Equipment Reagents", "", "error");
}
if (angular.isDefined(equipment.minimum_limit)==false) {
return sweetAlert("Please Enter Equipment Minimum Limit", "", "error");
}
if (angular.isDefined(equipment.maximum_limit)==false) {
return sweetAlert("Please Enter Equipment Maximum Limit", "", "error");
}
if (angular.isDefined(equipment.item_unit)==false) {
return sweetAlert("Please Enter Equipment SI-Unit", "", "error");
}
if (angular.isDefined(equipment.equipment_id)==false) {
return sweetAlert("Please Select Equipment Status", "", "error");
}
if (angular.isDefined(equipment.eqstatus)==false) {
return sweetAlert("Please Select Equipment Status", "", "error");
}
if (angular.isDefined(equipment.facilitys)==false) {
return sweetAlert("Please Select Facility", "", "error");
}
else{
var equipementsupdt=(
{
"equipment_name":equipment.equipment_name,
"reagents":equipment.reagents,
"minimum_limit":equipment.minimum_limit,
"maximum_limit":equipment.maximum_limit,
"si_unit":equipment.item_unit.id,
"id":equipment.equipment_id,
"equipment_status_id":equipment.eqstatus.id,
"facility_id":equipment.facilitys.id
}
);
$http.post('/api/equipement_update', equipementsupdt).then(function (data) {
var sending=data.data;
swal(
'Feedback..',
'Updates Success...',
'success'
)
})
}
}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}


//delete Equipment 
$scope.equipement_delete=function(equipment) {
if (angular.isDefined(equipment)==false) {
return sweetAlert("Please Enter Sample Status", "", "error");
}
else{
var equipment=equipment;
$http.get('/api/equipement_delete/'+equipment.id).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
equipment='';
}
});

}
}


//Get Department
$scope.get_department=function () {
$http.get('/api/get_department').then(function(data) {
$scope.department=data.data;
});
}

//Get Sub Department
$scope.getsub_department=function () {
$http.get('/api/getsub_department').then(function(data) {
$scope.subdepartment=data.data;
});
}


//Sub_Department_registration
$scope.sub_department_registration=function(sub) {
if (angular.isDefined(sub)==false) {
return sweetAlert("Please Enter Sub Department and Select Department", "", "error");
}
if (angular.isDefined(sub.sub_department)==false) {
return sweetAlert("Please Enter Sub Department Name", "", "error");
}
if (angular.isDefined(sub.id)==false) {
return sweetAlert("Select Department", "", "error");
}

else{
$http.post('/api/sub_department_registration',sub).then(function (data) {
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
sub='';
}
});
}
}

//Update Sub Department
$scope.sub_department_update=function (deptmnt) {
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
$http.post('/api/sub_department_update', deptmnt).then(function (data) {
var sending=data.data;
swal(
'Feedback..',
'Updates Success...',
'success'
)
})

}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//delete Sub Department
$scope.sub_department_delete=function(deptmnt) {
if (angular.isDefined(deptmnt)==false) {
return sweetAlert("Please Select Sub Department", "", "error");
}
else{
var deptmnts=deptmnt.sub_department_id;
$http.get('/api/sub_department_delete/'+deptmnts).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
}
});

}
}

//Get Item Data From the database
var item_ifo=[];
$scope.showItem=function (searchData) {
var searchItem={'SearchItem':searchData};
$http.post('/api/getitem',searchItem).then(function(data) {
item_ifo=data.data;
});
return item_ifo;
}


//Get lab_test_registration
$scope.get_lab_test=function () {
$http.post('/api/get_lab_test').then(function(data) {
$scope.lab_test=data.data;
});
}

//Lab_Test_registration
$scope.lab_test_registration=function(labtest) { 
if (angular.isDefined(labtest)==false) {
return sweetAlert("Please Select Datas", "", "error");
}
if (angular.isDefined(labtest.item)==false) {
return sweetAlert("Please Select Item", "", "error");
}
if (angular.isDefined(labtest.subdepartmnt)==false) {
return sweetAlert("Please Select Sub Department", "", "error");
}
if (angular.isDefined(labtest.equipmt)==false) {
return sweetAlert("Please Select Equipment", "", "error");
}
else{
var labtests=(
{
"item_id":labtest.id,
"item_name":labtest.item,
"sub_department_id":labtest.subdepartmnt,
"equipment_id":labtest.equipmt
}
);
$http.post('/api/lab_test_registrationlab_test_registration',labtests).then(function (data) {
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
labtest='';
}
});

}
}

//createLabsOrderNo
$scope.createLabsOrderNo=function(laborder)
{
var LabsOrderNo=(
{
"patient_id":laborder.patient_id,
"order_id":laborder.order_id,
"lab_test_id":laborder.lab_test_id,
"facility_id":laborder.facility_id
}
);
$http.post('/api/createLabsOrderNo',LabsOrderNo).then(function (data) {
$scope.order_number=data.data;
});
}

//getpatientlaborder
$scope.getpatientlaborder=function(laborder)
{
var LabsOrderN=(
{
"patient_id":laborder.patient_id,
"order_id":laborder.order_id
});
$http.post('/api/getpatientlaborder',LabsOrderN).then(function (data) {
$scope.patientlaborder=data.data;
});
}

//save patient lab order
$scope.savepatientlaborder=function(order_number,laborder)
{	
var PatientLabsOrder=(
{
"patient_id":laborder.patient_id,
"order_id":laborder.order_id,
"lab_test_id":laborder.lab_test_id,
"facility_id":laborder.facility_id,
"lab_order_id":laborder.tbl_lab_order_id,
"sample_no":order_number
}
);
$http.post('/api/savepatientlaborder',PatientLabsOrder).then(function (data) {
//$scope.patientlaborder=data.data;
var status=data.data.status;
var msg=data.data.msgs;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
PatientLabsOrder='';
}
});
}

//post patient lab order collected
$scope.postpatientlaborder=function(ordercollected)
{
var postpatientLabsOrder=(
{
"patient_id":ordercollected.patient_id,
"order_id":ordercollected.order_id,
"lab_test_id":ordercollected.lab_test_id,
"facility_id":ordercollected.facility_id,
//"lab_order_id":ordercollected.tbl_lab_order_id,
"sample_no":ordercollected.sample_no
}
);
$http.post('/api/postpatientlaborder',postpatientLabsOrder)
.then(function (data) {
$scope.loadpostresultform=data.data;
});
}




//test_unit_registration
$scope.test_unit_registration=function(testunit)
{
if (angular.isDefined(testunit)==false) {
return sweetAlert("Please Enter Test Unit", "", "error");
}
else{
var tstunit=({"unit":testunit});
$http.post('/api/test_unit_registration',tstunit).then(function (data){
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

//get test_unit
$scope.gettest_unit=function () {
$http.get('/api/gettest_unit').then(function(data) {
$scope.gettestunit=data.data;
});	
}

//test_unit_update
$scope.test_unit_update=function (gettestuni) {
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
var gettestunis=
({
"unit":gettestuni.unit,
"id":gettestuni.id,
});
$http.post('/api/test_unit_update',gettestunis).then(function (data) {
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
}
})

}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//test_unit_delete
$scope.test_unit_delete=function(gettestuni) {
if (angular.isDefined(gettestuni)==false) {
return sweetAlert("Please Select value", "", "error");
}
else{
var deleteunit=
{
"unit":gettestuni.unit,
"id":gettestuni.id
};
$http.get('/api/test_unit_delete/'+gettestuni.id+','+gettestuni.unit).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
}
});
}
}

//register test_sample 
$scope.test_sample_registration=function(testsample)
{
if (angular.isDefined(testsample)==false) {
return sweetAlert("Please Enter Sample", "", "error");
}
else{
var tstsamples=({"sample_to_collect":testsample});
$http.post('/api/test_sample_registration',tstsamples).then(function (data){
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

//Get test_sample
$scope.get_test_sample=function () {
$http.get('/api/get_test_sample').then(function(data) {
$scope.gettestsample=data.data;
});	
}

//testsample_update
$scope.testsample_update=function (gettestsampl) {
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
var indicators=
({
"sample_to_collect":gettestsampl.sample_to_collect,
"id":gettestsampl.id,
});
$http.post('/api/testsample_update',indicators).then(function (data) {
var status=data.data.status;
var msg=data.data.msg;
if(status==0){
swal('Oops!!',msg,'error');
}
else{
swal('success',msg,'success');
}
})

}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//testsample_update_delete
$scope.testsample_delete=function(gettestsampl) {
if (angular.isDefined(gettestsampl)==false) {
return sweetAlert("Please Select value", "", "error");
}
else{
var deletetestsample=
{
"sample_to_collect":gettestsampl.sample_to_collect,
"id":gettestsampl.id,
};
//console.log(deletetestsample);
$http.get('/api/testsample_delete/'+gettestsampl.id+','+gettestsampl.sample_to_collect).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
}
});
}
}

//test_indicator_registration
$scope.test_indicator_registration=function(testindicator)
{
var tstindicator=(
{
"indicator":testindicator.indicator,
"color_id":testindicator.color.id
}
);
$http.post('/api/test_indicator_registration',tstindicator).then(function (data){
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


//test_panel_registration
$scope.test_panel_registration=function(testindi)
{	
var tstindicator=(
{
"panel_name":testindi.itmpname,
"item_test_range":testindi.itmtstrng,
"item_unit":testindi.item_indicator.id,
"Test_indicator":testindi.item_unit.id
}
);
$http.post('/api/test_panel_registration',tstindicator).then(function (data){
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

//Get test_panel
$scope.gettest_panel=function () {
$http.get('/api/gettest_panel').then(function(data) {
$scope.gettestpanel=data.data;
});	
}

//Test Panel Update
$scope.test_panel_update=function (gettestpan) {
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
var testindic=
({
"panel_name":gettestpan.panel_name,
"id":gettestpan.test_panel_id,
"Item_test_range":gettestpan.Item_test_range,
"Item_unit":gettestpan.item_units.id,
"Test_indicator":gettestpan.item_indicator.id,
});
$http.post('/api/test_panel_update',testindic).then(function (data) {
//var sending=data.data;
swal(
'Feedback..',
'Updates Success...',
'success'
)
})
}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//test_panel_delete
$scope.test_panel_delete=function(gettestpan) {
if (angular.isDefined(gettestpan)==false) {
return sweetAlert("Please Select value", "", "error");
}
else{
var deletetestpanel=
{
"panel_name":gettestpan.panel_name,
"id":gettestpan.test_panel_id,
};
var gettestpans=gettestpan;
$http.get('/api/test_panel_delete/'+gettestpan.test_panel_id+','+gettestpan.panel_name).then(function (data) {
//console.log(data.data);
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
}
});
}
}

//Getlab_test_indicator
$scope.getlab_test_indicator=function () {
$http.get('/api/getlab_test_indicator').then(function(data) {
$scope.gettestindicator=data.data;
});	
}

//test_indicator Update
$scope.test_indicator_update=function (gettestindi) {
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
var indicators=
({
"indicator":gettestindi.indicator,
"color_id":gettestindi.color.id,
"id":gettestindi.id,
});
$http.post('/api/test_indicator_update',indicators).then(function (data) {
swal(
'Feedback..',
'Updates Success...',
'success'
)
})
}, function (dismiss) {
if (dismiss === 'cancel') {
swal(
'Cancelled',
' ',
'error'
)
}
})
}

//test_indicator_delete
$scope.test_indicator_delete=function(gettestindi) {
if (angular.isDefined(gettestindi)==false) {
return sweetAlert("Please Select value", "", "error");
}
else{
var deleteindicator=
{
"indicator":gettestindi.indicator,
"id":gettestindi.id,
};
$http.get('/api/test_indicator_delete/'+gettestindi.id+','+gettestindi.indicator).then(function (data) {
var status=data.data.status;
var msgs=data.data.msgs;
if(status==0){
swal('Oops!!',msgs,'error');
}
else{
swal('success',msgs,'success');
}});
}
}


//lab_test_registration
$scope.lab_test_registration=function(labtest)
{	
var labtests=(
{
"item_name":labtest.item.item_name,
"item_id":labtest.item.id,
"item_test_range":labtest.range,
"unit":labtest.item_units.id,
"item_test_indicator":labtest.item_indicator.id,
"sample_to_collect":labtest.sample.id,
"sub_department_id":labtest.sub_department.id,
"equipment_id":labtest.equipment.id
});
$http.post('/api/lab_test_registration',labtests).then(function (data){
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
$scope.getequipement_status();	
}

})();