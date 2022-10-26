(function () {
    'use strict';

    angular.module('authApp', [
        'ui.router',
        'satellizer',
        'ui.bootstrap',
        'ui.bootstrap.modal',
        'angularjs-datetime-picker',
        'angularUtils.directives.dirPagination',
        'chart.js',
        'toastr',
        'ngChatbox',
        'ngMaterial',
        'ngMessages',
        'perfect_scrollbar',
        'ngMdIcons',
        'angAccordion',
        'angular-loading-bar',
        'vAccordion',
        'datatables',
        'md.time.picker',
	    'ngResource',
	    'md.data.table',
    ]);


    angular.module('authApp').config(function ($stateProvider, $urlRouterProvider, $authProvider, $httpProvider, $provide, $mdThemingProvider, $mdIconProvider, $mdDateLocaleProvider, cfpLoadingBarProvider) {
        $mdDateLocaleProvider.formatDate = function (date) {
            return moment(date).format('YYYY-MM-DD');
        };

        cfpLoadingBarProvider.includeSpinner = false;


        $mdThemingProvider.theme('default')
            .primaryPalette('teal')
            .accentPalette('red');
        $mdIconProvider.defaultIconSet("/svg/avatars.svg",
            128)
            //nhif
            .icon("nhif_claim", "/svg/nhif.svg", 512)
            .icon("general_laboratory", "/svg/general_laboratory.svg", 512)
            .icon("nhif_setup", "/svg/nhif.svg", 512)
            //nhif
            .icon("menu", "/svg/menu.svg", 512)
            .icon("dashboard", "/svg/home.svg", 512)
            .icon("logout", "/svg/logout.svg", 512)
            .icon("default_profile", "/svg/default_profile.svg", 512)

            .icon("addRoles", "/svg/addRoles.svg", 512)
            .icon("addModules", "/svg/addModules.svg", 512)
            .icon("normalRegistration", "/svg/normalRegistration.svg", 512)
            .icon("treatmentDepartment", "/svg/treatmentDepartment.svg", 512)
            .icon("system", "/svg/system.svg", 512)
            .icon("radiology", "/svg/radiology.svg", 512)
            .icon("Discount", "/svg/Discount.svg", 512)
            .icon("Dispensing", "/svg/Dispensing.svg", 512)
            .icon("emergency", "/svg/emergency.svg", 512)
            .icon("casualtyRoom", "/svg/casualtyRoom.svg", 512)
            .icon("exemption", "/svg/exemption.svg", 512)
            .icon("facility_list", "/svg/facility.svg", 512)
            .icon("facility", "/svg/facility.svg", 512)
            .icon("Appointment", "/svg/Appointment.svg", 512)
            .icon("payments", "/svg/payments.svg", 512)
            .icon("dental_clinic", "/svg/dental_clinic.svg", 512)
            .icon("eye_clinic", "/svg/eye_clinic.svg", 512)
            .icon("reports", "/svg/reports.svg", 512)
            .icon("doctor_ipd", "/svg/doctor_ipd.svg", 512)
            .icon("doctor_opd", "/svg/doctor_opd.svg", 512)
            .icon("insurance_management", "/svg/insurance_management.svg", 512)
            .icon("icu", "/svg/icu.svg", 512)
            .icon("inventory", "/svg/inventory.svg", 512)
            .icon("item_Price", "/svg/item_Price.svg", 512)
            .icon("item_setup", "/svg/item_Price.svg", 512)
            .icon("finance_controls", "/svg/item_Price.svg", 512)
            .icon("laboratorySetting", "/svg/laboratorySetting.svg", 512)
            .icon("point_of_sale", "/svg/point_of_sale.svg", 512)
            .icon("userRegistration", "/svg/userRegistration.svg", 512)
            .icon("addViews", "/svg/addViews.svg", 512)
            .icon("addUserImage", "/svg/addUserImage.svg", 512)
            .icon("reception", "/svg/reception.svg", 512)
            .icon("emergency", "/svg/custom/emergency.svg", 512)
            .icon("emergencyDepartment", "/svg/emergencyDepartment.svg", 512)
            .icon("AppointmentCardiac", "/svg/custom/AppointmentCardiac.svg", 512)
            .icon("AppointmentDiabetic", "/svg/custom/Appointmentdiabetic.svg", 512)
            .icon("AppointmentPhysio", "/svg/custom/AppointmentPhysio.svg", 512)
            .icon("VitalSign", "/svg/custom/VitalSign.svg", 512)
            .icon("Tb", "/svg/Tb.svg", 512)
            .icon("Pharmacy", "/svg/Pharmacy.svg", 512)
            .icon("MainPharmacy", "/svg/MainPharmacy.svg", 512)
            .icon("Sub_store", "/svg/Sub_store.svg", 512)
            .icon("Anti_natal", "/svg/Anti_natal.svg", 512)
            .icon("Post_natal", "/svg/Post_natal.svg", 512)
            .icon("Labour", "/svg/Labour.svg", 512)
            .icon("Children", "/svg/Children.svg", 512)
            .icon("Family_Planning", "/svg/Family_Planning.svg", 512)
            .icon("exemption_list", "/svg/exemption_list.svg", 512)
            .icon("Temporary_exemption", "/svg/Temporary_exemption.svg", 512)
            .icon("Vct", "/svg/Vct.svg", 512)
            .icon("Pediatric", "/svg/Pediatric.svg", 512)
            .icon("Rch_report", "/svg/Rch_report.svg", 512)
            .icon("Medical_clinic", "/svg/Medical_clinic.svg", 512)
            .icon("psychiatric", "/svg/Medical_clinic.svg", 512)
            .icon("Orthopedic_clinic", "/svg/Orthopedic_clinic.svg", 512)
            .icon("BloodBank", "/svg/BloodBank.svg", 512)
            .icon("radiopatients", "/svg/radiopatients.svg", 512)
            .icon("radiologyDepartment", "/svg/radiologyDepartment.svg", 512)
            .icon("patientRegistration", "/svg/patientRegistration.svg", 512)
            .icon("laboratory", "/svg/laboratory.svg", 512)
            .icon("mortuaryManagement", "/svg/mortuaryManagement.svg", 512)
            .icon("labSetting", "/svg/labSetting.svg", 512)
            .icon("SampleCollection", "/svg/SampleCollection.svg", 512)
            .icon("system_dashboard", "/svg/system_dashboard.svg", 512)
            .icon("staff_perfomance", "/svg/staff_perfomance.svg", 512)
            .icon("shop", "/svg/point_of_sale.svg", 512)
            .icon("topup", "/svg/topup.svg", 512)
            .icon("payment_category", "/svg/payment_category.svg", 512)
            .icon("payment_type", "/svg/payment_type.svg", 512)
            .icon("mtuha_report", "/svg/mtuha_report.svg", 512)
            .icon("theatre_managing_list", "/svg/theatre_managing_list.svg", 512)
            .icon("DoctorctcClinic", "/svg/DoctorctcClinic.svg", 512)
            .icon("ctcClinicSetup", "/svg/ctcClinicSetup.svg", 512)
            .icon("ctcClinic", "/svg/ctcClinic.svg", 512)
            .icon("referral", "/svg/referral.svg", 512)
            .icon("outgoing-referral", "/svg/referral.svg", 512)
            .icon("incoming-referral", "/svg/referral.svg", 512)
            .icon("UsersList", "/svg/UsersList.svg", 512)
            .icon("Environmental", "/svg/Environmental.svg", 512)
            .icon("nutrition", "/svg/nutrition.svg", 512)
            .icon("patient_tracer", "/svg/patient_tracer.svg", 512)
            .icon("theatre_managing_list", "/svg/theatre_managing_list.svg", 512)
            .icon("theatre_doctor", "/svg/ctcClinicSetup.svg", 512)
            .icon("nursing_care", "/svg/nursing_care.svg", 512)
            .icon("ward_management", "/svg/ward_management.svg", 512)
            .icon("physiotherapy", "/svg/physiotherapy.svg", 512)
            .icon("performance_report", "/svg/performance_report.svg", 512)
            .icon("General_Appointment", "/svg/custom/General_Appointment.svg", 512)
            .icon("Partial_payment", "/svg/custom/Partial_payment.svg", 512)
            .icon("Ent_clinic", "/svg/custom/Ent_clinic.svg", 512)
            .icon("Urology_clinic", "/svg/Urology_clinic.svg", 512)
            .icon("notifications", "/svg/notifications.svg", 512)
            .icon("opd_nursing", "/svg/opd_nursing.svg", 512)
            .icon("medi_suply", "/svg/medi_suply.svg", 512)
            .icon("drf", "/svg/drf.svg", 512)
            .icon("drf_sales", "/svg/drf_sales.svg", 512)
            .icon("dermatology", "/svg/dermatology.svg", 512)
            .icon("diabetic", "/svg/diabetic.svg", 512)
            .icon("obgy_clinic", "/svg/obgy_clinic.svg", 512)
            .icon("surgical_clinic", "/svg/surgical_clinic.svg", 512)
            .icon("user_matrix", "/svg/mtuha_report.svg", 512)

            .icon("trauma", "/svg/doctor_opd.svg", 512)
            .icon("trauma-triage", "/svg/casualtyRoom.svg", 512);

        function redirectWhenLoggedOut($q, $injector) {

            return {

                responseError: function (rejection) {

                    // Need to use $injector.get to bring in $state or else we get
                    // a circular dependency error
                    var $state = $injector.get('$state');


                    // Instead of checking for a status code of 400 which might be used
                    // for other reasons in Laravel, we check for the specific rejection
                    // reasons to tell us if we need to redirect to the login state
                    var rejectionReasons = ['token_not_provided', 'token_expired', 'token_absent', 'token_invalid'];

                    // Loop through each rejection reason and redirect to the login
                    // state if one is encountered
                    angular.forEach(rejectionReasons, function (value, key) {

                        if (rejection.data.error === value) {

                            // If we get a rejection corresponding to one of the reasons
                            // in our array, we know we need to authenticate the user so
                            // we can remove the current user from local storage
                            localStorage.removeItem('user');

                            // Send the user to the auth state so they can login
                            $state.go('auth');
                        }
                    });
                    return $q.reject(rejection);
                }
            };
        }

        // Setup for the $httpInterceptor
        $provide.factory('redirectWhenLoggedOut', redirectWhenLoggedOut);

        // Push the new factory onto the $http interceptor array
        $httpProvider.interceptors.push('redirectWhenLoggedOut');

        // Satellizer configuration that specifies which API
        // route the JWT should be retrieved from
        $authProvider.loginUrl = '/api/authenticate';

        // Redirect to the auth state if any other states
        // are requested other than users
        $urlRouterProvider.otherwise('/auth');

        $stateProvider
            //nhif
            .state('nhif_setup', {
                url: '/nhif_setup',
                parent: 'master',
                templateUrl: '/scripts/modules/nhif/views/nhif-setup.html',
                controller: 'NhifController'
            })
            .state('nhif_claim', {
                url: '/nhif_claim',
                parent: 'master',
                templateUrl: '/scripts/modules/nhif/views/nhif-claim.html',
                controller: 'NhifController'
            })
            //nhif
            .state('master', {
                abstract: true,
                controller: 'AuthController as auth',
                views: {
                    '@': {
                        templateUrl: '/views/layouts/master.tmpl.html',
                    }
                }
            })
            .state('auth', {
                url: '/auth',
                templateUrl: '/views/authView.html',
                controller: 'AuthController as auth'
            })
            .state('trauma', {
                url: '/trauma',
                parent: 'master',
                templateUrl: '/scripts/modules/trauma/views/trauma-home.html',
                controller: 'TraumaController'
            })
            .state('trauma-triage', {
                url: '/trauma-triage',
                parent: 'master',
                templateUrl: '/scripts/modules/trauma/views/triage.html',
                controller: 'TriageController'
            })
            .state('dashboard', {
                url: '/dashboard',
                parent: 'master',
                templateUrl: '/views/welcome.html',
                controller: 'UserController as user'
            })
            .state('system_dashboard', {
                url: '/system_dashboard',
                parent: 'master',
                templateUrl: '/views/modules/dashboard/views/index.html',
                controller: 'UserController as user'
            })
            .state('mtuha_report', {
                url: '/mtuha_report',
                parent: 'master',
                templateUrl: '/views/modules/reports/mtuha_vitabu.html',
                controller: 'UserController as user'
            })
            .state('os_labtests', {
                url: '/os_labtests',
                parent: 'master',
                templateUrl: '/views/os_labtests.html',
                controller: 'UserController as user'
            })
            .state('os_drugs', {
                url: '/os_drugs',
                parent: 'master',
                templateUrl: '/views/os_drugs.html',
                controller: 'UserController as user'
            })
            .state('insuarance_report', {
                url: '/insuarance_report',
                parent: 'master',
                templateUrl: '/views/insuarance_report.html',
                controller: 'UserController as user'
            })
            .state('staff_perfomance', {
                url: '/staff_perfomance',
                parent: 'master',
                templateUrl: '/views/staff_perfomance_dashboard.html',
                controller: 'reportsMtuha'
            })
            .state('patientRegistration', {
                url: '/patientRegistration',
                parent: 'master',
                templateUrl: '/views/userView.html',
                controller: 'patientController as patient'
            })
            .state('theatre_doctor', {
                url: '/theatre_doctor',
                parent: 'master',
                templateUrl: '/views/modules/nursing_care/theatre_doctor.html',
                controller: 'theatreController'
            }).state('psychiatric', {
                url: '/psychiatric',
                parent: 'master',
                templateUrl: '/views/modules/clinic/psychiatric/psychiatric_home.html',
                controller: 'psychiatricHomeController'
            })
            .state('reception', {
                url: '/reception',
                parent: 'master',
                templateUrl: '/views/receptionTemplate.html',
                controller: 'patientController as patient'
            })
            .state('system', {
                url: '/system',
                parent: 'master',
                templateUrl: '/views/admin_template.html',
                controller: 'adminController'
            })
            .state('addPermRole', {
                url: '/addPermRole',
                parent: 'master',
                templateUrl: '/views/addPermRole.html',
                controller: 'rolesController as roles'
            })
            .state('nutrition', {
                url: '/nutrition',
                parent: 'master',
                templateUrl: '/views/modules/clinic/Nutrition/Nutrition_master.html',
                controller: 'NutritionController'
            })
            .state('General_Appointment', {
                url: '/General_Appointment',
                parent: 'master',
                templateUrl: '/views/modules/General_Appointment/General_Appointment.html',
                controller: 'General_AppointmentController'
            })
            .state('Partial_payment', {
                url: '/Partial_payment',
                parent: 'master',
                templateUrl: '/views/modules/payments/Partial_payment.html',
                controller: 'paymentsController'
            })
            .state('addViews', {
                url: '/addViews',
                parent: 'master',
                templateUrl: '/views/addViews.html',
                controller: 'rolesController as roles'
            })
            .state('addUserImage', {
                url: '/addUserImage',
                parent: 'master',
                templateUrl: '/views/adduserImage.html',
                controller: 'rolesController as roles'
            })
            .state('addPermUser', {
                url: '/addPermUser',
                parent: 'master',
                templateUrl: '/views/addpermuser.html',
                controller: 'rolesController as roles'
            })
            .state('nursing_care', {
                url: '/nursing_care',
                parent: 'master',
                templateUrl: '/views/nursing_care.html',
                controller: 'nursingCareController as roles'
            })
            .state('ward_management', {
                url: '/ward_management',
                parent: 'master',
                templateUrl: '/views/ward_management.html',
                controller: 'nursingCareController as roles'
            })
            .state('theatre_managing_list', {
                url: '/theatre_managing_list',
                parent: 'master',
                templateUrl: '/views/ward_to_theatre.html',
                controller: 'nursingCareController as roles'
            })
            .state('addRoles', {
                url: '/addRoles',
                parent: 'master',
                templateUrl: '/views/modules/admin/roles.html',
                controller: 'rolesController as roles'
            })
            .state('addModules', {
                url: '/addModules',
                parent: 'master',
                templateUrl: '/views/registerSystemPemissions.html',
                controller: 'rolesController as roles'
            })
            .state('ctcClinic', {
                url: '/ctcClinic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/ctc/ctc-dashboard.html',
                controller: 'ctcController'
            })
            .state('ctcClinicSetup', {
                url: '/ctcClinicSetup',
                parent: 'master',
                templateUrl: '/views/modules/clinic/ctc/ctcSetup-dashboard.html',
                controller: 'ctcSetup'
            })
            .state('doctorctcClinic', {
                url: '/doctorctcClinic',
                parent: 'master',
                templateUrl: '/views/ctcClinic.html',
                controller: 'rolesController as roles'
            })
            .state('payments', {
                url: '/payments',
                parent: 'master',
                templateUrl: '/views/modules/payments/bills.html',
                controller: 'paymentsController as payments'
            })
            .state('point_of_sale', {
                url: '/point_of_sale',
                parent: 'master',
                templateUrl: '/views/modules/payments/point_of_sale.html',
                controller: 'paymentsController as payments'
            }).state('topup', {
                url: '/topup',
                parent: 'master',
                templateUrl: '/views/modules/payments/topup.html',
                controller: 'topupController as payments'
            })
            .state('shop', {
                url: '/shop',
                parent: 'master',
                templateUrl: '/views/modules/payments/shop.html',
                controller: 'shopController as shop'
            })
            .state('receipts', {
                url: '/receipts',
                parent: 'master',
                templateUrl: '/views/modules/payments/receipts.html',
                controller: 'receiptsController as receipts',
                params: {
                    'billsdata': null,
                    'patient': null,
                    'payment_type': null
                }
            })
            .state('finance_controls', {
                url: '/finance_controls',
                parent: 'master',
                templateUrl: '/views/modules/payments/financeControls.html',
                controller: 'financeControlsController'
            })
            .state('reports', {
                url: '/reports',
                parent: 'master',
                templateUrl: '/views/modules/payments/finance.html',
                controller: 'reportsController as reports'
            })
            .state('doctor_opd', {
                url: '/doctor_opd',
                parent: 'master',
                templateUrl: '/views/modules/clinicalServices/patientsQueues.html',
                controller: 'opdQueueController as opd'
            })
            .state('doctor_ipd', {
                url: '/doctor_ipd',
                parent: 'master',
                templateUrl: '/views/modules/clinicalServices/ipd_home.html',
                controller: 'ipdQueueController as ipd'
            })
            .state('icu', {
                url: '/icu',
                parent: 'master',
                templateUrl: '/views/modules/icu/icu.html',
                controller: 'icuController as icu'
            })
            .state('dental_clinic', {
                url: '/dental_clinic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/dental/dental_home.html',
                controller: 'dentalHomeController as dental'
            })
            .state('eye_clinic', {
                url: '/eye_clinic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/eye/eye_home.html',
                controller: 'eyeHomeController as eye'
            })
            .state('obgy_clinic', {
                url: '/obgy_clinic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/obgy/obgy_home.html',
                controller: 'obgyHomeController as obgy'
            })
            .state('surgical_clinic', {
                url: '/surgical_clinic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/surgical/surgical_home.html',
                controller: 'surgicalHomeController as surgical'
            })
            .state('inventory', {
                url: '/inventory',
                parent: 'master',
                templateUrl: '/views/modules/inventory/inventory.html',
                controller: 'inventoryController as inventory'
            })
            .state('insurance_management', {
                url: '/insurance_management',
                parent: 'master',
                templateUrl: '/views/modules/insurance/insurance.html',
                controller: 'insuranceController as insurance'
            })
            .state('referral', {
                url: '/referral',
                parent: 'master',
                templateUrl: '/views/modules/referral/referral.html',
                controller: 'referralController as referral'
            })
            .state('outgoing-referral', {
                url: '/outgoing-referral',
                parent: 'master',
                templateUrl: '/views/modules/referral/outgoing-referral.html',
                controller: 'referralController as referral'
            })
            .state('incoming-referral', {
                url: '/incoming-referral',
                parent: 'master',
                templateUrl: '/views/modules/referral/incoming-referral.html',
                controller: 'referralController as referral'
            })
            .state('Cardiac', {
                url: '/Cardiac',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Cardiac_master.html',
                controller: 'opdController as opd'
            })
            .state('Ctc', {
                url: '/Ctc',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Cardiac_master.html',
                controller: 'opdController as opd'
            })
            .state('Ent_clinic', {
                url: '/Ent_clinic',
                parent: 'master',
                templateUrl: 'views/modules/clinic/Ent/Ent_form.html',
                controller: 'Ent_Controller'
            })
            .state('dermatology', {
                url: '/dermatology',
                parent: 'master',
                templateUrl: 'views/modules/clinic/dermatology/dermatology_home.html',
                controller: 'dermatologyController'
            })
            .state('Urology_clinic', {
                url: '/Urology_clinic',
                parent: 'master',
                templateUrl: 'views/modules/clinic/Urology/Urology_form.html',
                controller: 'Urology_Controller'
            })

            .state('General_surgery', {
                url: '/General_surgery',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/General_surgery_master.html',
                controller: 'opdController as opd'
            })
            .state('Mental_health', {
                url: '/Mental_health',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Mental_health_master.html',
                controller: 'opdController as opd'
            })
            .state('Medical_health', {
                url: '/Medical_health',
                templateUrl: '/views/modules/Clinics/Master/Medical_health_master.html',
                controller: 'opdController as opd'
            })

            .state('Obs_gyn', {
                url: '/Obs_gyn',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Obs_gyn_master.html',
                controller: 'opdController as opd'
            })
            .state('Orthopedic', {
                url: '/Orthopedic',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Orthopedic_master.html',
                controller: 'opdController as opd'
            })
            .state('Physio', {
                url: '/Physio',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Physio_master.html',
                controller: 'opdController as opd'
            })
            .state('Pmtct', {
                url: '/Pmtct',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Pmtct_master.html',
                controller: 'opdController as opd'
            })
            .state('Tb', {
                url: '/Tb',
                parent: 'master',
                templateUrl: '/views/Tb_master.html',
                controller: 'TbController'
            })
            .state('Vct', {
                url: '/Vct',
                parent: 'master',
                templateUrl: '/views/Vct_master.html',
                controller: 'VctController'
            })
            .state('Pediatric', {
                url: '/Pediatric',
                parent: 'master',
                templateUrl: '/views/Pediatric_master.html',
                controller: 'PediatricController'
            })
            .state('Dtc', {
                url: '/Dtc',
                parent: 'master',
                templateUrl: '/views/modules/clinic/Pediatric/Dtc_form.html',
                controller: 'PediatricController'
            }).state('opd_nursing', {
                url: '/opd_nursing',
                parent: 'master',
                templateUrl: '/views/opd_nursing.html',
                controller: 'opdNursingController as roles'
            })
            .state('BloodBank', {
                url: '/BloodBank',
                parent: 'master',
                templateUrl: '/views/BloodBank_master.html',
                controller: 'BloodBankController'
            })
            .state('Medical_clinic', {
                url: '/Medical_clinic',
                parent: 'master',
                templateUrl: '/views/Medical_clinic_master.html',
                controller: 'MedicalController'
            })
            .state('Orthopedic_clinic', {
                url: '/Orthopedic_clinic',
                parent: 'master',
                templateUrl: '/views/Orthopedic_clinic_master.html',
                controller: 'OrthopedicController'
            })
            .state('Urology', {
                url: '/Urology',
                parent: 'master',
                templateUrl: '/views/modules/Clinics/Master/Urology_master.html',
                controller: 'opdController as opd'
            })
            .state('exemption', {
                url: '/exemption',
                parent: 'master',
                templateUrl: '/views/exemption_form.html',
                controller: 'exemptionController'
            })
            .state('userRegistration', {
                url: '/userRegistration',
                parent: 'master',
                templateUrl: '/views/userRegistration.html',
                controller: 'userSettingController as user'
            })
            .state('UsersList', {
                url: '/UsersList',
                parent: 'master',
                templateUrl: '/views/UsersList.html',
                controller: 'userSettingController as user'
            })
            .state('password_resset', {
                url: '/password_resset',
                parent: 'master',
                templateUrl: '/views/password_resset.html',
                controller: 'userSettingController as user'
            })
            .state('regions', {
                url: '/regions',
                parent: 'master',
                templateUrl: '/views/Region_master.html',
                controller: 'regionController'
            })
            .state('facility', {
                url: '/facility',
                parent: 'master',
                templateUrl: '/views/Facility_registration.html',
                controller: 'facilityController'
            })
            .state('facility_list', {
                url: '/facility_list',
                parent: 'master',
                templateUrl: '/views/Facility_list.html',
                controller: 'facilityController'
            })
            .state('Discount', {
                url: '/Discount',
                parent: 'master',
                templateUrl: '/views/Discount_form.html',
                controller: 'discountController'
            })
            .state('payment_type', {
                url: '/payment_config',
                parent: 'master',
                templateUrl: '/modules/configuration/views/paymentType.html',
                controller: 'ItemConfigCtrl'
            })
            .state('payment_category', {
                url: '/payment_category',
                parent: 'master',
                templateUrl: '/views/Payment_category.html',
                controller: 'payment_typeController'
            })
            .state('item_setup', {
                url: '/item_setup',
                parent: 'master',
                templateUrl: '/views/item_setup.html',
                controller: 'itemSetupController'
            })
            .state('item_Price', {
                url: '/item_Price',
                parent: 'master',
                templateUrl: '/views/item_Price.html',
                controller: 'itemPriceController'
            })
            .state('exemption_list', {
                url: '/exemption_list',
                parent: 'master',
                templateUrl: '/views/exemption_list.html',
                controller: 'exemptionController'
            })
            .state('Temporary_exemption', {
                url: '/Temporary_exemption',
                parent: 'master',
                templateUrl: '/views/Temporary_exemption_master.html',
                controller: 'exemptionController'
            })
            .state('Pharmacy', {
                url: '/Pharmacy',
                parent: 'master',
                templateUrl: '/views/Pharmacy_master.html',
                controller: 'PharmacyController'
            })
            .state('MainPharmacy', {
                url: '/MainPharmacy',
                parent: 'master',
                templateUrl: '/views/Main_Pharmacy_master.html',
                controller: 'PharmacyItemsController'
            })
            .state('Sub_store', {
                url: '/Sub_store',
                parent: 'master',
                templateUrl: '/views/Sub_store_master.html',
                controller: 'SubStoreItemsController'
            })
            .state('Dispensing', {
                url: '/Dispensing',
                parent: 'master',
                templateUrl: '/views/Dispensing_master.html',
                controller: 'DispensingController'
            })
            .state('drf', {
                url: '/drf',
                parent: 'master',
                templateUrl: '/views/modules/Drf/Drf.html',
                controller: 'DrfController'
            })
            .state('drf_sales', {
                url: '/drf/sales',
                parent: 'master',
                templateUrl: '/views/modules/Drf/Drf_sales.html',
                controller: 'DrfController'
            })

            .state('medi_suply', {
                url: '/medi_suply',
                parent: 'master',
                templateUrl: '/views/modules/Pharmacy/Medical_stock_control.html',
                controller: 'MediSuplyController'
            })
            .state('Anti_natal', {
                url: '/Anti_natal',
                parent: 'master',
                templateUrl: '/views/Anti_natal_master.html',
                controller: 'Anti_natalController'
            })
            .state('Post_natal', {
                url: '/Post_natal',
                parent: 'master',
                templateUrl: '/views/Post_natal_master.html',
                controller: 'PostnatalController'
            })
            .state('Labour', {
                url: '/Labour',
                parent: 'master',
                templateUrl: '/views/Labour_master.html',
                controller: 'LabourController'
            })
            .state('Children', {
                url: '/Children',
                parent: 'master',
                templateUrl: '/views/Child_form_master.html',
                controller: 'Child_Controller'
            })
            .state('Family_Planning', {
                url: '/Family_Planning',
                parent: 'master',
                templateUrl: '/views/Family_planning_master.html',
                controller: 'Family_planning_Controller'
            })
            .state('Rch_report', {
                url: '/Rch_report',
                parent: 'master',
                templateUrl: '/views/Rch_report_master.html',
                controller: 'Rch_reoprtController'
            })
            .state('patientCard', {
                url: '/patientCard',
                parent: 'master',
                templateUrl: '/views/singlecardPrint.html',
                controller: 'patientController'
            })
            .state('edit', {
                url: '/edit',
                parent: 'master',
                templateUrl: '/views/Edit_user.html',
                controller: 'UserController as user'
            })
            //Radiology State
            .state('radiology', {
                url: '/radiology',
                parent: 'master',
                templateUrl: '/views/modules/radiology/radiology.html',
                controller: 'radiologyController as radiology'
            })
            .state('radiologytest', {
                url: '/radiologytest',
                parent: 'master',
                templateUrl: '/views/modules/radiology/radiologytest.html',
                controller: 'radiologyTestController as radiologytest'
            })
            .state('radiologyDepartment', {
                url: '/radiologyDepartment',
                parent: 'master',
                templateUrl: '/views/modules/radiology/radiology_department.html',
                controller: 'radiologyDepartmentController as radiologyDepartment'
            })
            .state('radiologyView', {
                url: '/radiologyView',
                parent: 'master',
                templateUrl: '/views/modules/radiology/radiology_view.html',
                controller: 'radiologyViewController as radiologyView'
            })
            .state('radiopatients', {
                url: '/radiopatients',
                parent: 'master',
                templateUrl: 'views/modules/radiology/radiopatients.html',
                controller: 'radiopatientsController'
            })
            .state('general_laboratory', {
                url: '/general_laboratory',
                parent: 'master',
                templateUrl: 'scripts/modules/general/views/general_laboratory.html',
                controller: 'generalLabController'
            })
            .state('patient_sample', {
                url: '/sample/patient/:a/:b',
                parent: 'master',
                templateUrl: 'scripts/modules/general/views/sref/sample.html',
                controller: 'sampleSrefController'
            })
            .state('test_sample', {
                url: '/testing/patient/:a/:b',
                parent: 'master',
                templateUrl: 'scripts/modules/general/views/sref/testing.html',
                controller: 'testSrefController'
            })
            //Vital Sign State
            .state('VitalSign', {
                url: '/VitalSign',
                parent: 'master',
                templateUrl: '/views/modules/VitalSign/VitalSigns.html',
                controller: 'VitalSignController as VitalSign'
            })
            //Appointment Cardiac
            .state('AppointmentCardiac', {
                url: '/AppointmentCardiac',
                parent: 'master',
                templateUrl: '/views/modules/clinic/cardiac/cardiac_appointmEnt.html',
                controller: 'cardiacController as cardiac_appointment'
            })
            //Appointment Diabetic
            .state('AppointmentDiabetic', {
                url: '/AppointmentDiabetic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/diabetic/Diabetic.html',
                controller: 'diabeticController as diabetic_appointment'
            })
            .state('appointment', {
                url: '/appointment',
                parent: 'master',
                templateUrl: '/views/appointmEnt_master.html',
                controller: 'UserController as user'
            })
            .state('diabetic', {
                url: '/diabetic',
                parent: 'master',
                templateUrl: '/views/modules/clinic/diabetic/diabetic_clinic.html',
                controller: 'diabeticClinicController'
            })
            //Appointment Physiotherapy
            .state('AppointmentPhysio', {
                url: '/AppointmentPhysio',
                parent: 'master',
                templateUrl: '/views/modules/clinic/physiotherapy/physiotherapy.html',
                controller: 'physioController as physiotherapy_appointment'
            })
            .state('physiotherapy', {
                url: '/physiotherapy',
                parent: 'master',
                templateUrl: '/views/modules/clinic/physiotherapy/physio_home.html',
                controller: 'physiotherapyController as physiotherapy'
            })

            .state('patientEdit', {
                url: '/patientEdit',
                parent: 'master',
                templateUrl: '/views/modules/registration/editPatient.html',
                controller: 'patientController as user'
            })
            .state('laboratory', {
                url: '/laboratory',
                parent: 'master',
                templateUrl: '/views/modules/laboratory/laboratory.html',
                controller: 'labController'
            })
            .state('SampleCollection', {
                url: '/SampleCollection',
                parent: 'master',
                templateUrl: '/views/modules/laboratory/collectSample.html',
                controller: 'labController'
            })
            .state('labSetting', {
                url: '/labSetting',
                parent: 'master',
                templateUrl: '/views/modules/laboratory/labSettingDashboard.html',
                controller: 'labController'
            })
            .state('sampleManagement', {
                url: '/sampleManagement',
                parent: 'master',
                templateUrl: '/views/samples.html',
                controller: 'UserController as user'
            })
            .state('mortuaryManagement', {
                url: '/mortuaryManagement',
                parent: 'master',
                templateUrl: '/views/modules/mortuary/manage_mortuary.html',
                controller: 'mortuaryController'
            })
            .state('emergency', {
                url: '/emergency',
                parent: 'master',
                templateUrl: '/views/EmergencyView.html',
                controller: 'emergencyController as emergency'
            })
            .state('emergencyDepartment', {
                url: '/emergencyDepartment',
                parent: 'master',
                templateUrl: '/views/emergencyDepartment.html',
                controller: 'emergencydepartmentController as emergencyDepartment'
            })
            .state('normalRegistration', {
                url: '/normalRegistration',
                parent: 'master',
                templateUrl: '/views/modules/emergency/normalRegistration.html',
                controller: 'emergencyController as normalRegistration'
            })
            .state('treatmentDepartment', {
                url: '/treatmentDepartment',
                parent: 'master',
                templateUrl: '/views/treatmentDepartment.html',
                controller: 'treatmentDepartmentController as treatmentEmergence'
            })
            .state('casualtyRoom', {
                url: '/casualtyRoom',
                parent: 'master',
                templateUrl: '/views/casualtyRoom.html',
                controller: 'casualtyRoomController as treatmentcasualty'

            })
            .state('patient_tracer', {
                url: '/patient_tracer',
                parent: 'master',
                templateUrl: '/views/modules/patient_tracing/patient_tracer.html',
                controller: 'patient_tracerController'

            })
            .state('Environmental', {
                url: '/Environmental',
                parent: 'master',
                templateUrl: '/views/Environmental_master.html',
                controller: 'EnvironmentalController'
            })
            .state('observationRoom', {
                url: '/observationRoom',
                parent: 'master',
                templateUrl: '/views/observationRoom.html',
                controller: 'observationRoomController as treatmentobservation'
            })

            .state('user_matrix', {
                url: '/user_matrix',
                parent: 'master',
                templateUrl: '/views/UserMatrix.html',
                controller: 'adminController'
            });
    });

    angular.module('authApp').run(function ($rootScope, $http, $state, $location) {
        // $stateChangeStart is fired whenever the state changes. We can use some parameters
        // such as toState to hook into details about the state as it is changing
        $rootScope.$on('$stateChangeStart', function (event, toState) {

            // Grab the user from local storage and parse it to an object
            var user = JSON.parse(localStorage.getItem('user'));
            if (!user && toState.name != "auth") {

                //event.preventDefault();

                // go to the "main" state which in our case is users
                $location.path("/auth");
            }

			/*
			var stored_data = window.localStorage.getItem("stored_residences");
            if(!stored_data) {
				$http
					.post('/api/searchResidences' , {searchKey:"%"})
					.then(function(data) {
						localStorage.setItem('stored_residences', JSON.stringify(data.data));
						
				});
            }
			
			*/



            // If there is any user data in local storage then the user is quite
            // likely authenticated. If their token is expired, or if they are
            // otherwise not actually authenticated, they will be redirected to
            // the auth state because of the rejected request anyway
            if (user) {

                // The user's authenticated state gets flipped to
                // true so we can now show parts of the UI that rely
                // on the user being logged in
                $rootScope.authenticated = true;

                // Putting the user's data on $rootScope allows
                // us to access it anywhere across the app. Here
                // we are grabbing what is in local storage
                $rootScope.currentUser = user;

                // If the user is logged in and we hit the auth route we don't need
                // to stay there and can send the user to the main state
                if (toState.name === "auth") {

                    // Preventing the default behavior allows us to use $state.go
                    // to change states
                    event.preventDefault();

                    // go to the "main" state which in our case is users
                    $state.go('dashboard');
                }

                if (toState.name !== "auth" && $rootScope.currentUser.length === 0) {

                    // Preventing the default behavior allows us to use $state.go
                    // to change states
                    event.preventDefault();

                    // go to the "main" state which in our case is users
                    $state.go('auth');
                }
            }
        });
    });
})();


(function () {
    angular.module('authApp').directive('verticalTabs', function () {
        return {
            restrict: 'E',
            transclude: true,
            scope: {},
            controller: ['$scope', function MyTabsController($scope) {
                var panes = $scope.panes = [];

                $scope.select = function (pane) {
                    angular.forEach(panes, function (pane) {
                        pane.selected = false;
                    });
                    pane.selected = true;
                };

                this.addPane = function (pane) {
                    if (panes.length === 0) {
                        $scope.select(pane);
                    }
                    panes.push(pane);
                };
            }],
            templateUrl: '/views/layouts/vertical_tabs/vertical-tabs.html',
        };
    })
        .directive('verticalTabPane', function () {
            return {
                require: '^^verticalTabs',
                restrict: 'E',
                transclude: true,
                scope: {
                    title: '@'
                },
                link: function (scope, element, attrs, tabsCtrl) {
                    tabsCtrl.addPane(scope);
                },
                templateUrl: '/views/layouts/vertical_tabs/vertical-tab-pane.html'
            };
        });
})();


(function () {
    angular.module('authApp').directive('zoom', function () {

        return {
            restrict: 'EA',
            replace: true,
            template: '<div class="magnify-container" data-ng-style="getContainerStyle()">' +
                '<div class="magnify-glass" data-ng-style="getGlassStyle()"></div>' +
                '<img class="magnify-image" data-ng-src="{{ imageSrc }}"/>' +
                '</div>',
            scope: {
                imageSrc: '@',
                imageWidth: '=',
                imageHeight: '=',
                glassWidth: '=',
                glassHeight: '='
            },
            link: function (scope, element) {
                var glass = element.find('div'),
                    image = element.find('img'),
                    el, nWidth, nHeight, magnifyCSS;

                // if touch devices, do something
                if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
                    return;
                }
                element.on('mousemove', function (evt) {
                    el = angular.extend(scope.getOffset(element[0]), {
                        width: element[0].offsetWidth,
                        height: element[0].offsetHeight,
                        imageWidth: image[0].offsetWidth,
                        imageHeight: image[0].offsetHeight,
                        glassWidth: glass[0].offsetWidth,
                        glassHeight: glass[0].offsetHeight
                    });
                    magnifyCSS = scope.magnify(evt);

                    if (magnifyCSS) {
                        glass.css(magnifyCSS);
                    }
                })
                    .on('mouseout', function () {
                        glass.on('mouseleave', function () {
                            glass.css({
                                opacity: 0,
                                filter: 'alpha(opacity=0)'
                            });
                        });
                    });

                scope.magnify = function (evt) {
                    var mx, my, rx, ry, px, py, bgp, img;

                    if (!nWidth && !nHeight) {
                        img = new Image();
                        img.onload = function () {
                            nWidth = img.width;
                            nHeight = img.height;
                        };
                        img.src = scope.imageSrc;
                    } else {
                        // IE8 uses evt.x and evt.y
                        mx = (evt.pageX) ? (evt.pageX - el.left) : evt.x;
                        my = (evt.pageY) ? (evt.pageY - el.top) : evt.y;

                        if (mx < el.width && my < el.height && mx > 0 && my > 0) {
                            glass.css({
                                opacity: 1,
                                'z-index': 1,
                                filter: 'alpha(opacity=100)'
                            });
                        } else {
                            glass.css({
                                opacity: 0,
                                'z-index': -1,
                                filter: 'alpha(opacity=0)'
                            });
                            return;
                        }

                        rx = Math.round(mx / el.imageWidth * nWidth - el.glassWidth / 2) * -1;
                        ry = Math.round(my / el.imageHeight * nHeight - el.glassHeight / 2) * -1;
                        bgp = rx + 'px ' + ry + 'px';

                        px = mx - el.glassWidth / 2;
                        py = my - el.glassHeight / 2;

                        return { left: px + 'px', top: py + 'px', backgroundPosition: bgp };
                    }
                    return;
                };

                scope.getOffset = function (_el) {
                    var de = document.documentElement;
                    var box = _el.getBoundingClientRect();
                    var top = box.top + window.pageYOffset - de.clientTop;
                    var left = box.left + window.pageXOffset - de.clientLeft;
                    return { top: top, left: left };
                };

                scope.getContainerStyle = function () {
                    return {
                        width: (scope.imageWidth) ? scope.imageWidth + 'px' : '',
                        height: (scope.imageHeight) ? scope.imageHeight + 'px' : ''
                    };
                };

                scope.getGlassStyle = function () {
                    return {
                        background: 'url("' + scope.imageSrc + '") no-repeat',
                        width: (scope.glassWidth) ? scope.glassWidth + 'px' : '',
                        height: (scope.glassHeight) ? scope.glassHeight + 'px' : ''
                    };
                };
            }
        };

    });
})();