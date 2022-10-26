(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('ctcSetup',

                ['$filter','$scope','$http','$rootScope','$mdDialog', '$mdBottomSheet','Helper',
        function ($filter,$scope,$http,$rootScope,$mdDialog, $mdBottomSheet,Helper) {
            var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;

            angular.element(document).ready(function () {
                
            });

            $scope.saveVisitCode = function(visit){
				if(angular.isDefined(visit)==false){
					sweetAlert("Please write Visit type and Codes", "", "error");
				}
               var visit_type=visit.type;
               var code=visit.code;
			   var dataToPost={	visit_type:visit_type,code:code,codeQuery:1};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            }; 
			
			$scope.saveSymptomCode = function(symptom){
				if(angular.isDefined(symptom)==false){
					sweetAlert("Please write Symptom and Codes", "", "error");
				}
               var signs=symptom.type;
               var code=symptom.code;
			   var dataToPost={signs:signs,code:code,codeQuery:2};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			$scope.saveFunctionalStatusCode = function(functionals){
				if(angular.isDefined(functionals)==false){
					sweetAlert("Please write functional status and Codes", "", "error");
				}
               var functional_status=functionals.type;
               var code=functionals.code;
			   var dataToPost={functional_status:functional_status,code:code,codeQuery:3};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };

           $scope.saveFamilyPlanCode = function(family){
				if(angular.isDefined(family)==false){
					sweetAlert("Please write family plan status and Codes", "", "error");
				}
               var family_plan=family.type;
               var code=family.code;
			   var dataToPost={family_plan:family_plan,code:code,codeQuery:4};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			
          $scope.saveNutritionStatusCode = function(nutrition){
				if(angular.isDefined(nutrition)==false){
					sweetAlert("Please write nutrition status and Codes", "", "error");
				}
               var status_nutrition=nutrition.type;
               var code=nutrition.code;
			   var dataToPost={status_nutrition:status_nutrition,code:code,codeQuery:5};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };

            $scope.saveSupplementStatusCode = function(nutrition){
				if(angular.isDefined(nutrition)==false){
					sweetAlert("Please write nutrition supplement and Codes", "", "error");
				}
               var 	nutritional_supplement=nutrition.type;
               var code=nutrition.code;
			   var dataToPost={	nutritional_supplement:	nutritional_supplement,code:code,codeQuery:6};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            }; 

			$scope.saveRefferalCode = function(refferal){
				if(angular.isDefined(refferal)==false){
					sweetAlert("Please write refferal type and Codes", "", "error");
				}
               var ctc_refferal=refferal.type;
               var code=refferal.code;
			   var dataToPost={ctc_refferal:ctc_refferal,code:code,codeQuery:7};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			
			
            $scope.saveFollowupCode = function(followup){
				if(angular.isDefined(followup)==false){
					sweetAlert("Please write follow up type and Codes", "", "error");
				}
               var status_follow_up=followup.type;
               var code=followup.code;
			   var dataToPost={status_follow_up:status_follow_up,code:code,codeQuery:8};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			
$scope.saveTbScreeningCode = function(tb_screening){
				if(angular.isDefined(tb_screening)==false){
					sweetAlert("Please write TB Screening Diagnosis and Codes", "", "error");
				}
               var tb_screening=tb_screening.type;
               var code=tb_screening.code;
			   var dataToPost={tb_screening:tb_screening,code:code,codeQuery:9};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			
			$scope.saveARVReasonCode = function(arv_reason){
				if(angular.isDefined(arv_reason)==false){
					sweetAlert("Please write ARV reason and Codes", "", "error");
				}
               var arv_reason=arv_reason.type;
               var code=arv_reason.code;
			   var dataToPost={arv_reason:arv_reason,code:code,codeQuery:10};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			$scope.arvCombinationRegimes = function(arv_combination){
				if(angular.isDefined(arv_combination)==false){
					sweetAlert("Please write ARV REGIMES and Codes", "", "error");
				}
               var arv_combination=arv_combination.type;
               var code=arv_combination.code;
			   var dataToPost={arv_combination:arv_combination,code:code,codeQuery:11};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			
			$scope.TBtreatment = function(tb_treatment){
				if(angular.isDefined(tb_treatment)==false){
					sweetAlert("Please write TB Treatments and Codes", "", "error");
				}
               var tb_treatment=tb_treatment.type;
               var code=tb_treatment.code;
			   var dataToPost={tb_treatment:tb_treatment,code:code,codeQuery:12};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };
			
       $scope.SaveARVstatusCode = function(ARVstatus){
				if(angular.isDefined(ARVstatus)==false){
					sweetAlert("Please write ARV STATUS and Codes", "", "error");
				}
               var arv_status=ARVstatus.type;
               var code=ARVstatus.code;
			   var dataToPost={arv_status:arv_status,code:code,codeQuery:13};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };

            $scope.SaveARVAdherense = function(ARVAdherense){
				if(angular.isDefined(ARVAdherense)==false){
					sweetAlert("Please write ARV ADHERENSE and Codes", "", "error");
				}
               var arv_adherense=ARVAdherense.type;
               var code=ARVAdherense.code;
			   var dataToPost={arv_adherense:arv_adherense,code:code,codeQuery:14};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            }; 

			$scope.SaveOITreatment = function(OITreatment){
				if(angular.isDefined(OITreatment)==false){
					sweetAlert("Please write OITreatment and Codes", "", "error");
				}
               var oi_treatment=OITreatment.type;
               var code=OITreatment.code;
			   var dataToPost={oi_treatment:oi_treatment,code:code,codeQuery:15};
			   $http.post('/api/saveCtcCodes',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
						sweetAlert(data.data.data, "", "success");
					}
			   });
            };

           

         

        }]);
		
		
		
		
		
}());