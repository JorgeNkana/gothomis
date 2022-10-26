/**
 * Created by japhari on 30/03/2017.
 */
/**
 * Created by japhari on 07/03/2017.
 */

(function() {

    'use strict';

    angular
        .module('authApp')

        .controller('treatmentDepartmentController', treatmentDepartmentController);

    function treatmentDepartmentController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$uibModal) {

        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        $scope.isCollapsedHorizontal = false;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var username=$rootScope.currentUser.name;
        var facility_id=$rootScope.currentUser.facility_id;

        var _selected;
        $scope.oneAtATime = true;
        $scope.templates = [
            {   name: 'Resuscitation',
                url: 'resuscitation.html'
            },
            {   name: 'Wards',
                url: 'ward.html'
            },
            {   name: 'Discharge',
                url: 'discharge.html'
            },

            {
                name: 'Deceased',
                url: 'mortuary.html'
            }
        ];



        //TIME PICKER
        $scope.mytime = new Date();

        $scope.hstep = 1;
        $scope.mstep = 2;

        $scope.options = {
            hstep: [1, 2, 3],
            mstep: [1, 5, 10, 15, 25, 30]
        };

        $scope.ismeridian = true;
        $scope.toggleMode = function() {
            $scope.ismeridian = ! $scope.ismeridian;
        };

        $scope.update = function() {
            var d = new Date();
            d.setHours( 14 );
            d.setMinutes( 0 );
            $scope.mytime = d;
        };
        //@END

        $scope.ngModelOptionsSelected = function(value) {
            if (arguments.length) {
                _selected = value;
            } else {
                return _selected;
            }
        };

        $scope.modelOptions = {
            debounce: {
                default: 500,
                blur: 250
            },
            getterSetter: true
        };

        $scope.printUserMenu=function (user_id) {

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
                //console.log($scope.menu);

            });

        };

        $http.get('/api/getUserImage/'+user_name).then(function(data) {
            $scope.photo='/uploads/'+data.data[0].photo_path;
            //console.log($scope.photo);

        });
        //Dispositions
        $http.get('/api/getWards').then(function (data) {
            $scope.wards = data.data;
        });




        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        $scope.printUserMenu(user_id);


//get patients who have paid consultation fee/exempted/insurance
        var patientData =[];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/getTretPatients',{
                "search":searchKey,
                "facility_id":facility_id
            }).then(function(data)
            {
                patientData = data.data;
            });
            return patientData;
        }

        $scope.openDialogForVitals = function (selectedPatient) {
            //console.log(selectedPatient);
            $scope.quick_registration =selectedPatient;


            //console.log($scope.quick_registration);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/emergency/emergencyvitalsModal.html',
                // size: 'lg',
                animation: true,
                controller: 'emergencyModal',
                windowClass: 'app-modal-window',
                resolve:{
                    quick_registration: function () {
                        $scope.cardDetails=$scope.quick_registration;
                        //console.log($scope.cardDetails);
                        return $scope.quick_registration ;
                    }
                }


            });

            modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
                //console.log($scope.quick_reg);
            });
        }

        $scope.states = [
            {
                "name": "Body Weight",
                "unit":"Kg",
                "value":"weight",
                "status": "1"
            },
            {
                "name": "Height/Length",
                "unit":"cm",
                "value":"height",
                "status": "2"
            },{
                "name": "Body Temperature",
                "unit":"℃",
                "value":"temperature",
                "status": "3"
            },{
                "name": "Systolic Pressure",
                "unit":"mmHg",
                "value":"systolic",
                "status": "4"
            },{
                "name": "Diastolic Pressure",
                "unit":"mmHg",
                "value":"diastolic",
                "status": "5"
            },{
                "name": "Respiratory Rate",
                "unit":"Breaths/Min",
                "value":"respiratory",
                "status": "6"
            },{
                "name": "Pulse Rate",
                "unit":"Breaths/Min",
                "value":"pulse",
                "status": "7"
            },{
                "name": "Oxygen Saturation",
                "unit":"Beats/Min",
                "value":"oxygen",
                "status": "8"
            }


        ];






        $scope.VitalSignRegister = function(vitals,id){
            var Body_weight = vitals.Body_weight;
            var height_length = vitals.height_length;
            var Body_temperature = vitals.Body_temperature;
            var Systolic_pressure = vitals.Systolic_pressure;
            var Diastolic_pressure = vitals.Diastolic_pressure;
            var Respiratory_rate = vitals.Respiratory_rate;
            var pulse_rate = vitals.pulse_rate;
            var Oxygen_saturation = vitals.Oxygen_saturation;
            var time_attended = vitals.time_attended;


            var registerVitals={
                'id':id,
                'Body_weight':Body_weight,
                'height_length':height_length,
                'Body_temperature':Body_temperature,
                'Systolic_pressure':Systolic_pressure,
                'Diastolic_pressure':Diastolic_pressure,
                'Respiratory_rate':Respiratory_rate,
                'time_attended':time_attended,
                'Pulse_rate':pulse_rate,
                'facility_id':facility_id,
                'Oxygen_saturation':Oxygen_saturation,
                'registered_by':user_name
            };
            //console.log(registerVitals);
            $http.post('/api/VitalSignRegister',registerVitals).then(function(data) {

                //console.log(data.data);
                swal(
                    'Vital Sign Sent Successfully',
                    'Successfully Registered',
                    'success'
                )
                vitals.Body_weight=null;
                vitals.height_length=null;
                vitals.Body_temperature=null;
                vitals.Systolic_pressure=null;
                vitals.Diastolic_pressure=null;
                vitals.Respiratory_rate=null;
                vitals.pulse_rate=null;
                vitals.Oxygen_saturation=null;
            });
        };









        $scope.VitalRegister = function(vitals,id){

            //console.log(vitals);
            var vitalData = vitals.number;
            var vitalTime = vitals.time;
            var vitalName = vitals.name.value;
            var unit = vitals.name.unit;
            var status = vitals.name.status;
            //console.log(vitalName);
            //console.log(vitalData);
            //console.log(status);


            var VitalData={
                'vital_sign':vitalName,
                'vital_data':vitalData,
                'facility_id':facility_id,
                'status':status,
                'time_attended':vitalTime,
                'unit':unit,
                'registered_by':user_name,
                'patient_id':id
            };

            swal({
                title: username,
                text: "Confirm Vital Signs",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Confirmed!'
            }).then(function () {

                $http.post('/api/VitalRegister',VitalData).then(function(data) {

                    //console.log(data.data);
                    vitals.number=null;
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
                            'Vital Sign Sent',
                            msg,
                            'success'
                        )
                    }

                    // $scope.searchOpt='';
                    // $scope.patients();

                });

            })
        };





        //    VIEW PATIENT VITAL SIGN IN EMERGENCE


        $scope.x_axis=[];
        $scope.y_axis=[];

        $scope.viewVitals = function(id){

            //console.log(id);

            $http.get('/api/viewVitals/'+id).then(function(data) {
                $scope.vitalData=data.data;
        for(var i=0; i<$scope.vitalData.length; i++) {
            $scope.x_axis.push(data.data[i].time_attended);
            $scope.y_axis.push(data.data[i].Body_temperature);

        }
//console.log($scope.y_axis);

                $scope.labels=$scope.x_axis;
                $scope.data=$scope.y_axis;





            });
        };







        $scope.viewVitals = function(id){

            //console.log(id);

            $http.get('/api/viewVitals/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.x_axis=[];
                $scope.y_axis=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.x_axis.push(data.data[i].time_attended);
                    $scope.y_axis.push(data.data[i].Body_temperature);

                }
                //console.log($scope.y_axis);

                $scope.labels=$scope.x_axis;
                $scope.data=$scope.y_axis;





            });
        };

        $scope.viewDiastolicPressure = function(id){

            //console.log(id);

            $http.get('/api/viewDiastolicPressure/'+id).then(function(data) {
                $scope.vitalData=data.data;


                $scope.vitalData=data.data;
                $scope.valuex=[];
                $scope.valuey=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valuex.push(data.data[i].time_attended);
                    $scope.valuey.push(data.data[i].Diastolic_pressure);

                }
                //console.log($scope.valuey);

                $scope.labels=$scope.valuex;
                $scope.data=$scope.valuey;




            });
        };
        $scope.viewTemperature = function(id){

            //console.log(id);

            $http.get('/api/viewTemperature/'+id).then(function(data) {
                $scope.vitalData=data.data;


                $scope.vitalData=data.data;
                $scope.valuex=[];
                $scope.valuey=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valuex.push(data.data[i].time_attended);
                    $scope.valuey.push(data.data[i].Body_temperature);

                }
                //console.log($scope.valuey);

                $scope.labels=$scope.valuex;
                $scope.data=$scope.valuey;




            });
        };
        $scope.viewPulseRate = function(id){

            //console.log(id);

            $http.get('/api/viewPulseRate/'+id).then(function(data) {
                $scope.vitalData=data.data;


                $scope.vitalData=data.data;
                $scope.valuex=[];
                $scope.valuey=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valuex.push(data.data[i].time_attended);
                    $scope.valuey.push(data.data[i].Pulse_rate);

                }
                //console.log($scope.valuey);

                $scope.labels=$scope.valuex;
                $scope.data=$scope.valuey;




            });
        };








        $scope.viewSystolicPressure = function(id){

            //console.log(id);

            $http.get('/api/viewSystolicPressure/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.valueX=[];
                $scope.ValueY=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valueX.push(data.data[i].time_attended);
                    $scope.ValueY.push(data.data[i].Systolic_pressure);

                }
                //console.log($scope.ValueY);

                $scope.labels=$scope.valueX;
                $scope.data=$scope.ValueY;





            });
        };



        //    SEND PATIENT TO RESC

        $scope.Resus = function(id){

            var resusUser={
                'patient_id':id,
                'facility':facility_id
            };
            //console.log(resusUser);
            $http.post('/api/Resus',resusUser).then(function(data) {

                var value=(data.data);
                //console.log(value);

                swal(
                    username,
                    'Patient  Sent Resuscitation Room',
                    'success'
                )


            });
        };


        //Emergency Users

        $scope.patients=function () {


            $http.get('/api/observationUsers/' + facility_id).then(function (data) {
                $scope.observationusers = data.data;
                $scope.selectedPatient='';




            });


        }




        $scope.SaveSummary=function (summy,id) {

            var mode_arrival=summy.mode_arrival;
            var referred=summy.referred;
            var complain=summy.complain;
            var disposition=summy.disposition;
            var condition=summy.condition;
            var acuity=summy.acuity;
            var arrival=summy.arrival;
            var decision=summy.decision;
            var departure=summy.departure;
            var dispoType=summy.dispoType;
            var rm=summy.rm;
            var timeleft=summy.timeleft;
            var triage=summy.triage;
            var visit=summy.visit;


            if (angular.isDefined(mode_arrival)==false) {
                return sweetAlert("Please Fill Mode of Arrival", "", "error");
            }
            else if (angular.isDefined(referred)==false) {
                return sweetAlert("Please Fill Referred Field", "", "error");
            }
            else if (angular.isDefined(complain)==false) {
                return sweetAlert("Please Fill Complain Field", "", "error");
            }
            else if (angular.isDefined(disposition)==false) {
                return sweetAlert("Please Fill Disposition Field", "", "error");
            }
            else if (angular.isDefined(condition)==false) {
                return sweetAlert("Please Fill Condition  Field", "", "error");
            }
            else if (angular.isDefined(acuity)==false) {
                return sweetAlert("Please Fill Acuity  Field", "", "error");
            }
            else if (angular.isDefined(arrival)==false) {
                return sweetAlert("Please Fill Arrival  Field", "", "error");
            }
            else if (angular.isDefined(decision)==false) {
                return sweetAlert("Please Fill Decision  Field", "", "error");
            }
            else if (angular.isDefined(departure)==false) {
                return sweetAlert("Please Fill Departure  Field", "", "error");
            }
            else if (angular.isDefined(dispoType)==false) {
                return sweetAlert("Please Fill Disposition Type  Field", "", "error");
            }
            else if (angular.isDefined(rm)==false) {
                return sweetAlert("Please Fill Rm  Field", "", "error");
            }
            else if (angular.isDefined(timeleft)==false) {
                return sweetAlert("Please Fill Time Left  Field", "", "error");
            }
            else if (angular.isDefined(triage)==false) {
                return sweetAlert("Please Fill Triage  Field", "", "error");
            }
            else if (angular.isDefined(visit)==false) {
                return sweetAlert("Please Fill Visit  Field", "", "error");
            }


            //console.log(mode_arrival);
            //console.log(id);
            var savSummary={
                'patient_id':id,
                'mode_departure':departure,
                'referred_by':referred,
                'chief_complaint':complain,
                'disposition':disposition,
                'condition_dispo':condition,
                'acuity':acuity,
                'arrival':arrival,
                'dispo_decision':decision,
                'emergency_arrival':mode_arrival,
                'emmergency_dispo':dispoType,
                'rm':rm,
                'time_left':timeleft,
                'triage_impression':triage,
                'visit_type':visit,
                'registered_by':user_name,
                'facility_id':facility_id
            };
            //console.log(savSummary);


            $http.post('/api/SaveSummary',savSummary).then(function(data) {

                //console.log(data.data);

                swal(
                    username,
                    'Visit Summary Sent ',
                    'success'
                )


            });

        }


        $scope.SaveAppearance=function (Appearance,patient) {

            //console.log(Appearance);
            //console.log(patient);
            var savSummary={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Appearance',
                "system":'Emergency',
                "facility_id":facility_id
            };
            //console.log(savSummary);

            $http.post('/api/SaveAppearance',savSummary).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }
        $scope.SaveAirway=function (Appearance,patient) {

            //console.log(Appearance);
            //console.log(patient);
            var SaveAirway={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Airway',
                "system":'Emergency',
                "facility_id":facility_id
            };


            $http.post('/api/SaveAirway',SaveAirway).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }
        $scope.SaveBreathing=function (Appearance,patient) {

            //console.log(Appearance);
            //console.log(patient);
            var SaveBreathing={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Breathing',
                "system":'Emergency',
                "facility_id":facility_id
            };


            $http.post('/api/SaveBreathing',SaveBreathing).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }

        $scope.SaveCirculation=function (Appearance,patient) {

            var SaveCirculation={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Circulation',
                "system":'Emergency',
                "facility_id":facility_id
            };


            $http.post('/api/SaveCirculation',SaveCirculation).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }
        $scope.SaveResponsiveness=function (Appearance,patient) {

            var SaveResponsiveness={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Disability',
                "system":'Emergency',
                "facility_id":facility_id
            };


            $http.post('/api/SaveResponsiveness',SaveResponsiveness).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }
        $scope.SaveExposure=function (Appearance,patient) {

            var SaveExposure={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Exposure',
                "system":'Emergency',
                "facility_id":facility_id
            };


            $http.post('/api/SaveExposure',SaveExposure).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }
        $scope.SaveIntervention=function (Appearance,patient) {

            var SaveIntervention={
                "observation":Appearance,
                "admission_id":'',
                "patient_id":patient.patient_id,
                "visit_date_id":patient.account_id,
                "user_id":user_id,
                "category":'Intervention',
                "system":'Emergency',
                "facility_id":facility_id
            };


            $http.post('/api/SaveIntervention',SaveIntervention).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });

        }















        $scope.cancel=function () {


            $scope.patients();

        }

        $scope.patients();



        $scope.patientAdmission = function (item,patient) {
            if(patient == null){
                swal("Oops! something went wrong..","Please search and select Patient then click ward button to admit patient!");
                return;
            }
            var object = angular.extend({},item, patient);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/clinicalServices/admission.html',
                size: 'lg',
                animation: true,
                controller: 'admissionModal',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });
        }


        $scope.getPatientData1=function (data)
        {
            //console.log(data);
            $scope.selectedPatient=data.data;
            $scope.observationusers='';

        };
        $scope.records=[];
        $scope.addSurgical = function(selectedSurgical,patient,item){
            //console.log(selectedSurgical);
            //console.log(patient);
            //console.log(item);

            $scope.records.push(
                {
                    "descriptions":selectedSurgical,
                    "admission_id":'',
                    "patient_id":patient.patient_id,
                    "visit_date_id":patient.account_id,
                    "user_id":user_id,
                    "status":'Surgical',
                    "facility_id":facility_id
                });

//console.log($scope.records);

        };
        $scope.remove=function (index)
        {
            $scope.records.splice(index,1);



        }


        $scope.savePastSurgicalProcedure=function () {

//console.log($scope.records);

            $http.post('/api/savePastSurgicalProcedure',$scope.records).then(function (data) {
                $scope.surgical = data.data;
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )




            });


        }
        //DOCTOR PORTION




        //PREVIOUS PROFILE
        $scope.getPatientData = function (item)
        {

            $http.post('/api/vitalsDate',{"patient_id":item.patient_id}).then(function (data) {
                $scope.vitalsDate = data.data;
            });
            $http.post('/api/prevDiagnosis',{"patient_id":item.patient_id}).then(function (data) {
                $scope.diagnosis = data.data;
            });
            $http.post('/api/prevFamilyHistory',{"patient_id":item.patient_id}).then(function (data) {
                $scope.familyHistory = data.data;
            });
            $http.post('/api/prevBirthHistory',{"patient_id":item.patient_id}).then(function (data) {
                $scope.birthHistory = data.data;
            });
            $http.post('/api/prevHistoryExaminations',{"patient_id":item.patient_id}).then(function (data) {
                $scope.historyExaminations = data.data;
            });
            $http.post('/api/prevObsGyn',{"patient_id":item.patient_id}).then(function (data) {
                $scope.obsHistory = data.data;
            });
            $http.post('/api/getResults',{"patient_id":item.patient_id,"dept_id":3}).then(function (data) {
                $scope.radiology = data.data;
            });
            $http.post('/api/prevRoS',{"patient_id":item.patient_id}).then(function (data) {
                $scope.ros = data.data;
            });
            $http.post('/api/prevPhysicalExaminations',{"patient_id":item.patient_id}).then(function (data) {
                $scope.physicalExaminations = data.data;
            });
            $http.post('/api/getResults',{"patient_id":item.patient_id,"dept_id":2}).then(function (data) {
                $scope.labInvestigations = data.data;
            });
            $scope.selectedPatient=item;

            $scope.observationusers='';
        }

        //chief complaints
        var chiefComplaints = [];
        $scope.complaints = function (search) {
            $http.post('/api/chiefComplaints',{"search":search}).then(function (data) {
                chiefComplaints = data.data;
            });
            return chiefComplaints;
        }
        //review of systems
        var cardiovascular = [];
        $scope.showCardio = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Cardiovascular"}).then(function (data) {
                cardiovascular = data.data;
            });
            return cardiovascular;
        }
        var Respiratory = [];
        $scope.showRespiratory = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Respiratory"}).then(function (data) {
                Respiratory = data.data;
            });
            return Respiratory;
        }
        var gatro = [];
        $scope.showGastro = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Gastrointerstinal"}).then(function (data) {
                gatro = data.data;
            });
            return gatro;
        }
        var musculo = [];
        $scope.showMusculo = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Musculoskeletal"}).then(function (data) {
                musculo = data.data;
            });
            return musculo;
        }
        var genito = [];
        $scope.showGenito = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Genitourinary"}).then(function (data) {
                genito = data.data;
            });
            return genito;
        }
        var cns = [];
        $scope.showCNS = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Central Nervous System"}).then(function (data) {
                cns = data.data;
            });
            return cns;
        }
        var endo = [];
        $scope.showEndo = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Endocrine"}).then(function (data) {
                endo = data.data;
            });
            return endo;
        }
        var allergy = [];
        $scope.showAllergy = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Allergy"}).then(function (data) {
                allergy = data.data;
            });
            return allergy;
        }
        var pastMed = [];
        $scope.showMedication = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Medication"}).then(function (data) {
                pastMed = data.data;
            });
            return pastMed;
        }

        var pastMed = [];
        $scope.showIllness = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Past Medical History"}).then(function (data) {
                pastMed = data.data;
            });
            return pastMed;
        }
        var pastAdm = [];
        $scope.showAdmission = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Admission History"}).then(function (data) {
                pastAdm = data.data;
            });
            return pastAdm;
        }
        var pastImmune = [];
        $scope.showImmune = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Immunisation"}).then(function (data) {
                pastImmune = data.data;
            });
            return pastImmune;
        }
        var pastInsp = [];
        $scope.showInspection = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Inspection"}).then(function (data) {
                pastInsp = data.data;
            });
            return pastInsp;
        }
        var pastPalp = [];
        $scope.showPalpation = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Palpation"}).then(function (data) {
                pastPalp = data.data;
            });
            return pastPalp;
        }
        var pastPerc = [];
        $scope.showPercussion = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Percussion"}).then(function (data) {
                pastPerc = data.data;
            });
            return pastPerc;
        }
        var pastAus = [];
        $scope.showAuscultation = function (search) {
            $http.post('/api/reviewOfSystems',{"search":search,"category":"Auscultation"}).then(function (data) {
                pastAus = data.data;
            });
            return pastAus;
        }
        var diag = [];
        $scope.showDiagnosis = function (search) {
            $http.post('/api/getDiagnosis',{"search":search}).then(function (data) {
                diag = data.data;
            });
            return diag;
        }
        $scope.allergyChecker = function (item) {
            $http.post('/api/getAllergy',{"patient_id":item.patient_id}).then(function (data) {
                swal("This Patient is allergic to "+data.data[0].descriptions,"","info");
            });
        }
        //Investigations
        $scope.getSubDepts = function (item) {
            $http.post('/api/getSubDepts',{"department_id":item}).then(function (data) {
                $scope.subDepartments = data.data;
            });
        }
        $scope.getTests = function (item,category) {
            if(angular.isDefined(category)==false){swal("select Patient first","","error");return;}
            var category_id =category.bill_id;
            if(category.main_category_id == 3){
                category_id = 1;
            }
            $http.post('/api/getPanels',{"patient_category_id":category_id,"sub_dept_id":item,"facility_id":facility_id}).then(function (data) {
                $scope.panels = data.data;
            });
            $http.post('/api/getTests',{"patient_category_id":category_id,"sub_dept_id":item,"facility_id":facility_id}).then(function (data) {
                $scope.labTests = data.data;

            });
        }
        $scope.investigationOrders = [];
        $scope.unavailableOrders = [];
        $scope.orders = function (item,isChecked,patient) {
            var status_id = 1;
            var filter = '';
            if(patient.patient_id == null){
                swal("Ooops!! no Patient selected","Please search and select patient first..");
                return;
            }
            if(isChecked==true){
                for(var i=0;i<$scope.investigationOrders.length;i++)
                    if($scope.investigationOrders[i].item_id == item.item_id){
                        swal("Item already in your order list!");
                        return;
                    }
                if(item.on_off== 1) {
                    if(patient.main_category_id != 1){ filter = patient.bill_id;}
                    $scope.investigationOrders.push({"admission_id":'',"facility_id":facility_id,"item_type_id":item.item_type_id,"item_price_id":item.item_price_id,"status_id":status_id,
                        "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name,
                        "priority":'',"clinical_note":'',"payment_filter":filter});
                    //console.log($scope.investigationOrders);
                }
                else {
                    for(var i=0;i<$scope.unavailableOrders.length;i++)
                        if($scope.unavailableOrders[i].item_id == item.item_id){
                            swal("Item already in your order list!");
                            return;
                        }
                    $scope.unavailableOrders.push({"facility_id":facility_id,"visit_date_id":patient.account_id,
                        "patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name});
                    return;
                }
            }
        }
        $scope.saveInvestigation = function (item) {
            if ($scope.investigationOrders == "" && $scope.unavailableOrders == null) {
                swal("You dont have Items to save!", "Please select Items first!");
                return;
            }
            for (var i = 0; i < $scope.investigationOrders.length; i++) {
                $scope.investigationOrders[i]["priority"] = item.priority;
                $scope.investigationOrders[i]["clinical_note"] = item.clinical_note;
            }
            if($scope.investigationOrders !=""){
                $http.post('/api/postInvestigations', $scope.investigationOrders).then(function (data) {
                });
                $scope.investigationOrders = [];
                $scope.inv == null;
            }
            $http.post('/api/postUnavailableInvestigations',$scope.unavailableOrders).then(function (data) {

            });
            swal({
                title: 'Successfully Registration',
                text: 'Data Saved',
                timer: 2000
            }).then(
                function () {},
                // handling the promise rejection
                function (dismiss) {
                    if (dismiss === 'timer') {

                    }
                }
            )
            $scope.unavailableOrders = [];

        }
        //Investigation results
        $scope.getLabResults = function (item) {
            var results = {"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":item.dept_id};
            $http.post('/api/getInvestigationResults',results).then(function (data) {
                $scope.labResults = data.data;
            });
        }
        $scope.getRadResults = function (item) {
            var results = {"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":item.dept_id};
            $http.post('/api/getInvestigationResults',results).then(function (data) {
                $scope.radResults = data.data;
            });
        }
//posting from history and examinations
        $scope.complaintz = [];
        $scope.addComplaint = function (item,duration,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            for(var i=0;i<$scope.complaintz.length;i++)
                if($scope.complaintz[i].id == item.id){
                    swal("Item already in your wish list!");
                    return;
                }
            $scope.complaintz.push({"admission_id":'',"patient_id":patient.patient_id,"facility_id":facility_id,"visit_date_id":patient.account_id,"user_id":user_id,"description":item.name,
                "id":item.id,"duration":duration.durationqty,"duration_unit":duration.durationunit,"status":'Chief Complaints'});
        }
        $scope.saveComplaints = function (objectData,history) {
            if(objectData == "") {
                swal("Oops Data not saved!", "Please fill all necessary fields, click 'Add' Button then 'Save Complaints' Button to save data..");
                return;
            }
            $http.post('/api/postHistory',objectData).then(function (data) {
                var   id=data.data;
                var hpi={"other_complaints":history.other_complaints,"description": history.hpi,"history_exam_id":id,"status":'HPI'};

                $http.post('/api/historyRecords',hpi).then(function (data) {
                    swal({
                        title: 'Successfully Registration',
                        text: 'Data Saved',
                        timer: 2000
                    }).then(
                        function () {},
                        // handling the promise rejection
                        function (dismiss) {
                            if (dismiss === 'timer') {

                            }
                        }
                    )
                    $scope.complaintz=[];
                });
            });


        }
        $scope.cardiocasucular = []; $scope.respiratory = []; $scope.gastrointestinal = [];
        $scope.musculoskeletal = []; $scope.genitourinary = [];  $scope.cns = []; $scope.endocrine = [];
        $scope.reviewOfSystems = function (item,patient) {

            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.cardiocasucular.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.reviewOfSystems2 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.respiratory.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.reviewOfSystems3 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.gastrointestinal.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.reviewOfSystems4 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.musculoskeletal.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.reviewOfSystems5 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.genitourinary.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.reviewOfSystems6 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.cns.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.reviewOfSystems7 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.endocrine.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.saveRoS = function (objectData) {
            if(objectData == "") {
                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                return;
            }
            $http.post('/api/postRoS',objectData).then(function (data) {

            });
            swal({
                title: 'Successfully Registration',
                text: 'Data Saved',
                timer: 2000
            }).then(
                function () {},
                // handling the promise rejection
                function (dismiss) {
                    if (dismiss === 'timer') {

                    }
                }
            )
            $scope.cardiocasucular = []; $scope.respiratory = []; $scope.gastrointestinal = [];
            $scope.musculoskeletal = []; $scope.genitourinary = [];  $scope.cns = []; $scope.endocrine = [];
        }
        //Past medical history
        $scope.allergy = [];    $scope.medications = [];
        $scope.illness = [];    $scope.admissions = [];
        $scope.immunisation = [];
        $scope.pastMedicals = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.allergy.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.pastMedicals2 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.medications.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.pastMedicals3 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.illness.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.pastMedicals4 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.admissions.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }
        $scope.pastMedicals5 = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.immunisation.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
        }

        $scope.savePastMedical = function (objectData) {
            if(objectData == "") {
                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                return;
            }
            $http.post('/api/postPastMed',objectData).then(function (data) {

            });
            swal({
                title: 'Successfully Registration',
                text: 'Data Saved',
                timer: 2000
            }).then(
                function () {},
                // handling the promise rejection
                function (dismiss) {
                    if (dismiss === 'timer') {

                    }
                }
            )
            $scope.allergy = [];    $scope.medications = [];
            $scope.illness = [];    $scope.admissions = []; $scope.immunisation = [];
        }
        $scope.saveBirthHistory = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            var child = {"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,
                "antenatal":item.antenatal,"natal":item.natal,"post_natal":item.post_natal,"nutrition":item.nutrition,"growth":item.growth,"development":item.development};
            $http.post('/api/birthHistory',child).then(function (data) {
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )
            });
        }
        $scope.saveObsGyn = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            var obs = {"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"menarche":item.menarche,"menopause":item.menopause,"menstrual_cycles":item.menstrual_cycles,"pad_changes":item.pad_changes,
                "recurrent_menstruation":item.recurrent_menstruation,"contraceptives":item.contraceptives,"pregnancy":item.pregnancy,"lnmp":item.lnmp,"gravidity":item.gravidity,"parity":item.parity,"living_children":item.living_children};
            $http.post('/api/postObs',obs).then(function (data) {
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )
            });
        }
        //Physical Examinations
        $scope.removeFromSelection = function(item,objectdata){
            var indexremoveobject = objectdata.indexOf(item);
            objectdata.splice(indexremoveobject,1);
        }
        $scope.physicalMusculoskeletal = [];  $scope.physicalRespiratory = [];
        $scope.physicalCardiovascular = [];  $scope.physicalGastrointestinal = [];
        $scope.physicalGenitourinary = []; $scope.physicalCNS = []; $scope.physicalEndocrine = [];

        $scope.physicalMusculo = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalMusculoskeletal.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.physicalResp = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalRespiratory.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.physicalCardio = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalCardiovascular.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.physicalGastro = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalGastrointestinal.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.physicalGenito = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalGenitourinary.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.physicalCns = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalCNS.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.physicalEndo = function (item,patient,system) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.physicalEndocrine.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
        }
        $scope.savePhysicalExamination = function (objectData) {
            if(objectData == "") {
                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                return;
            }
            $http.post('/api/postPhysical',objectData).then(function (data) {

            });
            swal("Data Succesfully Saved!");
            $scope.physicalMusculoskeletal = []; $scope.physicalRespiratory = [];  $scope.physicalCardiovascular = [];
            $scope.physicalGastrointestinal = [];  $scope.physicalGenitourinary = []; $scope.physicalCNS = []; $scope.physicalEndocrine = [];
        }
        //family and social history
        $scope.saveSocialCommunity = function (item,patient) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            var child = {"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"chronic_illness":item.chronic_illness,"substance_abuse":item.substance_abuse,"adoption":item.adoption,"others":item.others};
            $http.post('/api/familyHistory',child).then(function (data) {
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )
            });
        }
        //Provisional , differential and confirmed diagnosis
        $scope.provisionalDiagnosis =[]; $scope.differentialDiagnosis =[]; $scope.confirmedDiagnosis =[];
        $scope.addProv = function (item,patient,status) {

            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.provisionalDiagnosis.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"diagnosis_description_id":item.id,"description":item.description,"status":status});
        }
        $scope.addDiff = function (item,patient,status) {

            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.differentialDiagnosis.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"diagnosis_description_id":item.id,"description":item.description,"status":status});
        }
        $scope.addConf = function (item,patient,status) {
            if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
            $scope.confirmedDiagnosis.push({"admission_id":'',"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"diagnosis_description_id":item.id,"description":item.description,"status":status});
        }
        $scope.saveDiagnosis = function (objectData) {
            if(objectData == "") {
                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                return;
            }
            $http.post('/api/postDiagnosis',objectData).then(function (data) {
                swal({
                    title: 'Successfully Registration',
                    text: 'Data Saved',
                    timer: 2000
                }).then(
                    function () {},
                    // handling the promise rejection
                    function (dismiss) {
                        if (dismiss === 'timer') {

                        }
                    }
                )
            });
            $scope.provisionalDiagnosis =[]; $scope.differentialDiagnosis =[]; $scope.confirmedDiagnosis =[];
        }
        //Dispositions
        $http.get('/api/getWards').then(function (data) {
            $scope.wards = data.data;
        });
        $scope.patientAdmission = function (item,patient) {
            if(angular.isDefined(patient) == false){
                swal("Oops! something went wrong..","Please search and select Patient then click ward button to admit patient!");
                return;
            }
            var object = angular.extend({},item, patient);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/clinicalServices/admission.html',
                size: 'lg',
                animation: true,
                controller: 'admissionModal',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });
        }
        //Treatments:medication and procedures
        var mediData =[];
        $scope.medicines =[];
        $scope.medicinesOs =[];
        $scope.searchItems = function(searchKey,patient) {
            var pay_id =patient.bill_id;
            if(pay_id==null){
                swal("Please search patient to be prescribed before searching Medicine!");
                return;
            }
            if(patient.main_category_id == 3){
                pay_id = 1;
            }
            $http.post('/api/getMedicine',{"search":searchKey,"facility_id":facility_id,"patient_category_id":pay_id}).then(function(data) {
                mediData = data.data;

            });
            return mediData;

        }
        var balance=[];
        $scope.addMedicine = function (item,patient,dawa) {
            var status_id = 1;
            var filter = '';
            var main_category = patient.main_category_id;

            var quantity = item.dose*item.duration*24/item.frequency;
            if(patient ==null){ swal("Please search and select Patient to prescribe"); return; }
            if(dawa ==null){ swal("Please search and select medicine!"); return;}
            if(item.instructions ==null){ swal("Please Write Instructions and click 'Add to List' Button","","error"); return;}
            for(var i=0;i<$scope.medicines.length;i++)
                if($scope.medicines[i].item_id == dawa.item_id){ swal("Item already in your order list!"); return;}
            if(main_category != 1){ filter = patient.bill_id;}
            if(main_category == 3){  main_category=1;}
            $http.post('/api/balanceCheck',{"main_category_id":main_category,"item_id":dawa.item_id,"facility_id":facility_id, "user_id": user_id}).then(function (data) {
                balance = data.data;
                if(balance[0].balance>=quantity){
                    $scope.medicines.push({"facility_id":facility_id,"item_type_id":dawa.item_type_id,"item_price_id":dawa.price_id,"quantity":quantity,"status_id":status_id,
                        "dose":item.dose,"frequency":item.frequency,"duration":item.duration,"instructions":item.instructions,"out_of_stock":"","payment_filter":filter,
                        "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":dawa.item_id,"item_name":dawa.item_name
                    });
                }
                else if (balance[0].balance<quantity){
                    var conf= confirm("This Item is not available in Store..Do you want to prescribe anyway?");
                    if(conf== true){
                        for(var i=0;i<$scope.medicinesOs.length;i++)
                            if($scope.medicinesOs[i].item_id == dawa.item_id){ swal("Item already in your order list!"); return;}
                        $scope.medicinesOs.push({"facility_id":facility_id,"item_type_id":dawa.item_type_id,"item_price_id":dawa.price_id,"quantity":quantity,"status_id":status_id,
                            "dose":item.dose,"frequency":item.frequency,"duration":item.duration,"instructions":item.instructions,"out_of_stock":"OS",
                            "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":dawa.item_id,"item_name":dawa.item_name
                        });
                        //console.log($scope.medicinesOs);
                        swal("Item added under OS category");
                    }else {
                        swal("canceled","Choose different Item for Prescription");
                        return;
                    }
                }


            });



        }
        $scope.saveMedicine = function () {
            if($scope.medicines == "" && $scope.medicinesOs == ""){
                swal("No Items to Save","","error");
                return;
            }
            if($scope.medicines !=""){
                $http.post('/api/postMedicines',$scope.medicines).then(function (data) {

                });
                $scope.medicines = [];
            }
            $http.post('/api/outOfStockMedicine',$scope.medicinesOs).then(function (data) {

            });
            swal("Patient successfully prescribed!");
            $scope.medicinesOs = [];
        }
        //procedures
        var procedureData =[];
        $scope.procedures =[];
        $scope.searchProcedures = function(searchKey,patient) {
            var pay_id = patient.bill_id;
            if (pay_id == null) {swal("Please search patient before searching procedures!");return;}
            if (patient.main_category_id == 3) {pay_id = 1; }
            $http.post('/api/getPatientProcedures', {"search": searchKey,"facility_id": facility_id,"patient_category_id": pay_id}).then(function (data) {
                procedureData = data.data;
            });
            return procedureData;

        }
        $scope.addProcedure = function (item,patient) {
            var filter = '';
            var status_id = 1;
            if(patient.patient_id ==null){ swal("Please search and select Patient to prescribe"); return; }
            if(item.item_id ==null){ swal("Please search and select Procedure!"); return;}
            for(var i=0;i<$scope.procedures.length;i++)
                if($scope.procedures[i].item_id == item.item_id){ swal("Item already in your order list!"); return;}
            if(patient.main_category_id != 1){ filter = patient.bill_id;}

            $scope.procedures.push({"payment_filter":filter,"admission_id":'',"facility_id":facility_id,"item_type_id":item.item_type_id,"item_price_id":item.price_id,"quantity":1,"status_id":status_id,
                "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name
            });
        }
        $scope.saveProcedures = function (objectData) {
            $http.post('/api/postPatientProcedures',objectData).then(function (data) {

            });
            swal({
                title: 'Successfully Registration',
                text: 'Data Saved',
                timer: 2000
            }).then(
                function () {},
                // handling the promise rejection
                function (dismiss) {
                    if (dismiss === 'timer') {

                    }
                }
            )
            $scope.procedures = [];
        }


        //    DOCTOR PORTION-FINISH

    }



})();