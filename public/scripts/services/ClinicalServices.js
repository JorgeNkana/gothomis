(function() {
    'use strict';

    var app = angular.module('authApp');

    app.factory('ClinicalServices', ['$http','$mdToast', function($http,$mdToast) {

        return {
            getAllIpdPatients : function (item) {
                return $http.post('/api/get-ipd-patients',item)
                    .then(function (response) {
                        return response;
                    });
            },
            notifications: function (response) {
                if(response.status ===201){
                    var   message= response.data.message;
                 return   $mdToast.show($mdToast.simple()
                        .position('top right')
                        .content(message)
                        .hideDelay(3000)
                    );
                }
            },
            getPatientCategories : function () {
                return $http.get('/api/get-patient-categories')
                    .then(function (response) {
                        return response;
                    });
            },
            filterByCategories : function (item) {
                return $http.post('/api/filter-by-category',item)
                    .then(function (response) {
                        return response;
                    });
            },
            getOpdPatients : function (item) {
                return $http.post('/api/get-opd-patients',item)
                    .then(function (response) {
                        return response;
                    });
            },
            getInvestigationPatients : function (item) {
                return $http.post('/api/investigation-list',item)
                    .then(function (response) {
                        return response;
                    });
            },
            searchOpdPatients : function (item) {
                return $http.post('/api/search-opd-patients', item)
                    .then(function (response) {
                        return response;
                    });
            },
            searchOpdInvestigationPatients : function (item) {
                return $http.post('/api/search-investigation-patients', item)
                    .then(function (response) {
                        return response;
                    });
            },
            getCorpseList : function (item) {
                return $http.post('/api/get-corpse-list',item)
                    .then(function (response) {
                        return response;
                    });
            },
            checkAttendanceStatus : function (item) {
                return $http.post('/api/check-patient-attendance',item)
                    .then(function (response) {
                        return response;
                    });
            },
            admitPatient : function (item) {
                return $http.post('/api/admit-patient',item)
                    .then(function (response) {
                        return response;
                    });
            },
            getBillsCancellations : function (item) {
                return $http.post('/api/get-bill-list', item)
                    .then(function (response) {
                        return response;
                    });
            },
            cancelBills : function (item) {
                return $http.post('/api/cancel-bill-item',item)
                    .then(function (response) {
                        return response;
                    });
            },
            cancelPatientBills : function (item) {
                return $http.post('/api/cancel-patient-bill', item)
                    .then(function (response) {
                        return response;
                    });
            },

            getWards : function (facility_id) {
                return $http.get('/api/get-wards/'+facility_id)
                    .then(function (response) {
                        return response;
                    });
            },
            getStores : function (facility_id) {
                return $http.get('/api/get-stores/'+facility_id)
                    .then(function (response) {
                        return response;
                    });
            },

            getClinics : function () {
                return $http.get('/api/get-special-clinics/')
                    .then(function (response) {
                        return response;
                    });
            },

            getFacilityInfo : function (user_id) {
                return $http.get('/api/get-facility-info/'+user_id )
                    .then(function(response) {
                    return response;
                });
            },

            getNotes : function (patient_id) {
                return $http.get('/api/get-continuation-notes/'+patient_id )
                    .then(function(response) {
                        return response;
                    });
            },

            postNotes : function (postData) {
                return $http.post('/api/post-notes',postData)
                    .then(function(response) {
                        return response;
                    });
            },
            searchDiagnosis : function (seachKey) {
                return $http.get('/api/search-diagnosis/' +seachKey)
                    .then(function (response) {
                        return response;
                    });
            },
            saveDiagnosis : function (item,patient,facility_id) {
           return $http.post('/api/post-diagnosis',item)
            .then(function (response) {
            var confirmedDiagnoses = [];
            item.forEach(function(disease){
            if(disease.status.toLowerCase() == 'confirmed')
            confirmedDiagnoses.push(disease);
            });
            var TallyRegister = {attempt:0, load: function(){
            if(confirmedDiagnoses.length == 0)
            return;
            TallyRegister.attempt++;
            $http.post('/api/countIPDDiagnosis',{facility_id:facility_id, dob: patient.dob,
                gender: patient.gender,concepts:confirmedDiagnoses})
                .then(function(data){},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
            }}
            TallyRegister.load();
            return response;
                });
            },

            getSubDepartments : function () {
               return $http.get('/api/get-sub-departments')
                   .then(function (response) {
                       return response;
                   }) ;
            },
            getPanels : function (item) {
                return $http.post('/api/get-panels',item).then(function (response) {
                    return response;
                });
            },
            getSingleTests : function (item) {
                return $http.post('/api/get-single-tests',item).then(function (response) {
                    return response;
                });
            },
            getTests : function (item) {
                return $http.post('/api/get-tests',item).then(function (response) {
                    return response;
                });
            },
            saveInvestigationOrders : function (invData) {
               return $http.post('/api/post-investigations', invData)
                   .then(function(response) {
                   return response;
               });
            },
            saveUnavailableInvestigations : function (invData) {
               return $http.post('/api/post-unavailable-investigations', invData)
                   .then(function(response) {
                   return response;
               });
            },
            fetchInvestigationResults : function (results) {
                return $http.post('/api/get-investigation-results', results)
                    .then(function (response) {
                       return response;
                    });
            },
            getResults : function (item) {
                return $http.post('/api/get-results',item)
                    .then(function (response) {
                       return response;
                    });
            },
            searchMedicine : function (item) {
                return $http.post('/api/get-medicine',item)
                    .then(function (response) {
                       return response;
                    });
            },
            checkDosage : function (item) {
                return $http.post('/api/dosage-checker',item).then(function (response) {
                    return response;
                })
            },
            balanceCheck : function (item) {
				return $http.post('/api/balance-check',item).then(function (response) {
                    return response;
                });
            },
            saveMedicine : function (item) {
                return $http.post('/api/post-medicines',item).then(function (response) {
                   return response;
                });
            },
            saveMedicineOS : function (item) {
                return $http.post('/api/out-of-stock-medicine',item).then(function (response) {
                    return response;
                });
            },
            searchMedicineByStore : function (item) {
                return $http.post('/api/get-medicine-by-store',item)
                    .then(function (response) {
                        return response;
                    });
            },
            searchMedicalSupplies : function (item) {
                return $http.post('/api/get-medical-supplies',item)
                    .then(function (response) {
                        return response;
                    });
            },
            saveMedicalSupplies : function (item) {
                return $http.post('/api/post-medical-supplies',item).then(function (response) {
                    return response;
                });
            },
            saveMedicalSuppliesOS : function (item) {
                return $http.post('/api/out-of-stock-medical-supplies',item).then(function (response) {
                    return response;
                })
            },
            searchProcedures : function (item) {
                return $http.post('/api/get-patient-procedures',item).then(function (response) {
                    return response;
                });
            },
            saveProcedures : function (item) {
                return $http.post('/api/post-patient-procedures',item).then(function (response) {
                    return response;
                });
            },
            saveConservatives : function (item) {
                return $http.post('/api/conservatives',item).then(function (response) {
                    return response;
                });
            },
            rejectedPrescription : function (item) {
                return $http.post('/api/rejected-medicines',item).then(function (response) {
                    return response;
                });
            },
            prescribedMedicine : function (item) {
                return $http.get('/api/get-prescribed-medicine/'+item).then(function (response) {
                    return response;
                });
            },
            orderedProcedures : function (item) {
                return $http.get('/api/get-prev-procedures/'+item).then(function (response) {
                   return response;
                });
            },
            savePrescriptionUpdates : function (item) {
                return $http.post('/api/update-medicines',item).then(function (response) {
                    return response;
                })
            },
            orderedInvestigations : function (item) {
                return $http.post('/api/all-ordered-investigations',item).then(function (response) {
                    return response;
                });
            },
            requestBlood : function (item) {
                return $http.post('/api/request-blood', item).then(function (response) {
                   return response;
                });
            },
            dischargedata : function (item) {
                return $http.post('/api/discharge-report',item).then(function (response) {
                   return response;
                });
            },
            dischargePatient : function (item) {
                return $http.post('/api/discharge-patient',item).then(function (response) {
                   return response;
                });
            },
            getConsultation : function (item) {
                return $http.post('/api/get-consultation',item).then(function (response) {
                    return response;
                });
            },
            transferToClinics : function (item) {
                return $http.post('/api/post-to-clinics',item).then(function (response) {
                    return response;
                });
            },
            certifyCorpse : function (item) {
                return $http.post('/api/certify-corpse',item).then(function (response) {
                    return response;
                });
            },
            saveCorpse : function (item) {
                return $http.post('/api/post-deceased',item).then(function (response) {
                    return response;
                });
            },
            searchByWard : function (item) {
                return $http.get('/api/filter-by-wards/'+item).then(function (response) {
                    return response;
                });
            },
            searchIpdPatients : function (item) {
                return $http.post('/api/get-all-ipd-patients',item).then(function (response) {
                    return response;
                });
            },
            searchComplaints : function (item) {
                return $http.get('/api/chief-complaints/'+item).then(function (response) {
                    return response;
                });
            },
            saveComplaints : function (item) {
                return $http.post('/api/post-history', item).then(function (response) {
                    return response;
                });
            },
            saveHPI : function (item) {
                return $http.post('/api/post-hpi', item).then(function (response) {
                    return response;
                });
            },
            searchROS : function (item) {
                return $http.post('/api/review-of-systems',item).then(function (response) {
                    return response;
                });
            },
            saveROS : function (item) {
                return $http.post('/api/post-ros',item).then(function (response) {
                    return response;
                });
            },
            savePastMedical : function (item) {
                return $http.post('/api/post-past-medical',item).then(function (response) {
                    return response;
                });
            },
            saveGeneralPhysical : function (item) {
                return $http.post('/api/post-gen-physical',item).then(function (response) {
                    return response;
                });
            },
            saveLocalPhysical : function (item) {
                return $http.post('/api/post-local-physical',item).then(function (response) {
                    return response;
                });
            },
            savePhysicalSummary : function (item) {
                return $http.post('/api/post-summary-physical', item).then(function (response) {
                    return response;
                });
            },
            savePhysicalExamination : function (item) {
                return $http.post('/api/post-physical', item).then(function (response) {
                    return response;
                });
            },
            saveOtherPhysicalExaminations : function (item) {
                return $http.post('/api/post-other-summary',item).then(function (response) {
                    return response;
                });
            },
            getPatientVisits : function (item) {
                return $http.post('/api/previous-visits',item).then(function (response) {
                    return response;
                });
            },
            getPatientComplaints : function (item) {
                return $http.post('/api/prev-history',item).then(function (response) {
                    return response;
                });
            },
            getPatientDiagnosis : function (item) {
                return $http.post('/api/get-prev-diagnosis',item).then(function (response) {
                    return response;
                });
            },
            getPatientROS : function (item) {
                return $http.post('/api/get-prev-ros',item).then(function (response) {
                    return response;
                });
            },
            getPatientAllergies : function (item) {
                return $http.post('/api/get-allergies',item).then(function (response) {
                    return response;
                });
            },
            getPatientPhysicalExams : function (item) {
                return $http.post('/api/get-prev-physical',item).then(function (response) {
                    return response;
                });
            },
            getPatientInvestigationResults : function (item) {
                return $http.post('/api/prev-investigation-results',item).then(function (response) {
                    return response;
                });
            },
            getPatientPrescriptions : function (item) {
                return $http.post('/api/get-past-medicine',item).then(function (response) {
                    return response;
                });
            },
            getPatientProcedures : function (item) {
                return $http.post('/api/get-past-procedures',item).then(function (response) {
                    return response;
                });
            },
            getTodaysVitals : function (item) {
                return $http.post('/api/vitals-time',item).then(function (response) {
                    return response;
                });
            },
            getPatientVitals : function (item) {
                return $http.post('/api/patient-vitals',item).then(function (response) {
                    return response;
                });
            },
            postExternalReferrals : function (item) {
                return $http.post('/api/post-referral',item).then(function (response) {
                    return response;
                });
            },
			
            
            checkIfServiceRestrictedByNhif : function (item) {
                return $http.post('/api/pre-approval-checkup',item)
                    .then(function (response) {
                        return response;
                    });
            }
        }
    }]);
})();