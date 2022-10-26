<!doctype html>
<html ng-app="authApp">

<head>
    <meta charset="utf-8">
    <title>GoT-HoMIS</title>

<!--    <link href="/css/roboto-fontface.css/roboto/roboto-fontface.css" rel="stylesheet">-->
    <link rel="stylesheet" href="/bower_components/sweetalert2/dist/sweetalert2.css">
    <link rel="stylesheet" href="/css/bootstrap.css">

    <link rel="stylesheet" href="/css/design.css">

    <link rel="stylesheet" href="/bower_components/angular-toastr/dist/angular-toastr.css">
    <link rel="stylesheet" href="/bower_components/material-design-lite/material.css">
    <link rel='stylesheet' href='/css-materials/material.css?family=Roboto:400,700,300|Material+Icons'
          type='text/css'>
    <!-- angular material css put last to hopefully overwrite the previous ones -->
    <link rel="stylesheet" href="/bower_components/angular-material/angular-material.min.css">
    <link rel="stylesheet" type="text/css" href="/css/ang-accordion.css">
    <link rel="stylesheet" type="text/css" href="/css/angularjs-datetime-picker.css">

    <link rel='stylesheet' href="/css/loading-bar.min.css" type="text/css" media="all"/>
    <link href="/css/v-accordion.css" rel="stylesheet"/>

    <!-- datatables -->
    <link rel="stylesheet" href="/css/angular-datatables.css">
    <link rel="stylesheet" href="/css/datatable_style.css">
    <link rel="stylesheet" href="/bower_components/angular-material-data-table/dist/md-data-table.min.css">

    <link href="css/zoomer.css" rel="stylesheet" type="text/css">

    <style type="text/css">
        body {
            font-family: 'Roboto', 'Helvetica', 'san-sarif';
        }

        /**
       * hide when angular is not yet loaded and initialized
       */

        [ng\:cloak],
        [ng-cloak],
        [data-ng-cloak],
        [x-ng-cloak],
        .ng-cloak,
        .x-ng-cloak {
            display: none !important;
        }
    </style>
</head>

<body layout="row" ng-controller="AppController" class="dashboard" ng-cloak>
<div id="main-ui-view" ui-view layout="row" flex></div>
<script src="/js/highchart.js"></script>
<script src="/js/export.js"></script>
<script src="/bower_components/MDBootstrap/js/jquery-3.1.1.js"></script>

<!--
    disable scrollbar
    <script src="/js/scrollbar.min.js"></script>
-->

<script src="/bower_components/es6-promise/es6-promise.auto.min.js"></script>
<script src="/bower_components/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="/bower_components/angular/angular.min.js"></script>
<script src="/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
<script src="/bower_components/satellizer/dist/satellizer.js"></script>
<script src="/bower_components/angular-bootstrap/ui-bootstrap.min.js"></script>
<script src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
<script src="/bower_components/pdfobject/pdfobject.js"></script>
<script src="/bower_components/angularUtils-pagination/dirPagination.js"></script>
<script src="/bower_components/chart.js/dist/Chart.min.js"></script>
<script src="/bower_components/angular-animate/angular-animate.js"></script>
<script src="/bower_components/angular-resource/angular-resource.js"></script>
<script src="/bower_components/angular-aria/angular-aria.js"></script>
<script src="/bower_components/angular-messages/angular-messages.min.js"></script>
<script src="/bower_components/angular-perfect-scrollbar/src/angular-perfect-scrollbar.min.js"></script>
<script src="/bower_components/material-design-lite/material.min.js"></script>
<script src="/bower_components/angular-material/angular-material.min.js"></script>
<script src="/bower_components/angular-material-icons/angular-material-icons.js"></script>
<script src="/bower_components/angular-material-data-table/dist/md-data-table.min.js"></script>
<script src="/bower_components/angular-chart.js/dist/angular-chart.min.js"></script>
<script src="/bower_components/angular-toastr/dist/angular-toastr.tpls.js"></script>
<script src="/bower_components/material-design-lite/material.min.js"></script>
<script src="/bower_components/ng-chatbox/build/chatbox.min.js"></script>
<script type="text/javascript" src="/js/ng-accordion.js"></script>
<script type='text/javascript' src='/js/loading-bar.min.js'></script>
<script type="text/javascript" src="/js/moment.js"></script>
<script src="/js/v-accordion.js"></script>

<script type="text/javascript">

	var imgWidth;
	var imgHeight;
	function StartSign()
	 {   
	    var isInstalled = document.documentElement.getAttribute('SigPlusExtLiteExtension-installed');  
	    if (!isInstalled) {
	        alert("SigPlusExtLite extension is either not installed or disabled. Please install or enable extension.");
			return;
	    }	
	    var canvasObj = document.getElementById('cnv');
		canvasObj.getContext('2d').clearRect(0, 0, canvasObj.width, canvasObj.height);
		//document.FORM1.sigStringData.value = "SigString: ";
		//document.FORM1.sigRawData.value = "Base64 String: ";
		imgWidth = canvasObj.width;
		imgHeight = canvasObj.height;
		var message = { "firstName": "", "lastName": "", "eMail": "", "location": "", "imageFormat": 1, "imageX": imgWidth, "imageY": imgHeight, "imageTransparency": false, "imageScaling": false, "maxUpScalePercent": 0.0, "rawDataFormat": "ENC", "minSigPoints": 25 };
			
		top.document.addEventListener('SignResponse', SignResponse, false);
		var messageData = JSON.stringify(message);
		var element = document.createElement("MyExtensionDataElement");
		element.setAttribute("messageAttribute", messageData);
		document.documentElement.appendChild(element);
		var evt = document.createEvent("Events");
		evt.initEvent("SignStartEvent", true, false);				
		element.dispatchEvent(evt);		
    }
	function SignResponse(event)
	{	
		var str = event.target.getAttribute("msgAttribute");
		var obj = JSON.parse(str);
		SetValues(obj, imgWidth, imgHeight);
	}
	function SetValues(objResponse, imageWidth, imageHeight)
	{
        var obj = null;
		if(typeof(objResponse) === 'string'){
			obj = JSON.parse(objResponse);
		} else{
			obj = JSON.parse(JSON.stringify(objResponse));
		}		
		
	    var ctx = document.getElementById('cnv').getContext('2d');

			if (obj.errorMsg != null && obj.errorMsg!="" && obj.errorMsg!="undefined")
			{
                alert(obj.errorMsg);
            }
            else
			{
                if (obj.isSigned)
				{
                    //document.FORM1.sigRawData.value += obj.imageData;
					//document.FORM1.sigStringData.value += obj.sigString;
					var img = new Image();
					img.onload = function () 
					{
						ctx.drawImage(img, 0, 0, imageWidth, imageHeight);
					}
					img.src = "data:image/png;base64," + obj.imageData;
					localStorage.setItem('clientSignature', obj.imageData);
					console.log(obj.imageData);
                }
            }
    }
     
</script> 
<!-- forked version of datetime picker -->

<script src="/js/angularjs-datetime-picker.js"></script>
<!-- databables -->
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/angular-datatables.min.js"></script>


<!--    <script src="/scripts_build/scripts.js"></script>-->
<script src="/bower_components/ng-chatbox/build/chatbox.min.js"></script>
<script src="/scripts/app.js"></script>
<script src="/scripts/modules/reports/reportsMtuha.js"></script>
<script src="/scripts/modules/reports/opdMtuhaController.js"></script>
<script src="/scripts/modules/reports/ipdMtuhaController.js"></script>
<script src="/scripts/modules/reports/dentalMtuhaController.js"></script>
<script src="/scripts/modules/reports/eyeMtuhaController.js"></script>
<script src="/scripts/modules/reports/drugsMtuhaController.js"></script>
<script src="/scripts/modules/reports/labMtuhaController.js"></script>
<script src="/scripts/modules/reports/expiredDrugsMtuhaController.js"></script>
<script src="/scripts/modules/reports/doctorsPerfomacesController.js"></script>
<script src="/scripts/authController.js"></script>
<script src="/scripts/admin/rolesController.js"></script>
<script src="/scripts/userController.js"></script>
<script src="/scripts/AppController.js"></script>
<script src="/scripts/modules/registrations/nhifRegistrationModal.js"></script>
<script src="/scripts/modules/registrations/printCard.js"></script>
<script src="/scripts/modules/registrations/printCardBima.js"></script>
<script src="/scripts/modules/registrations/registrationModalCorpse.js"></script>
<script src="/scripts/modules/registrations/patientController.js"></script>
<script src="/scripts/admin/adminController.js"></script>
<script src="/scripts/modules/nusring_care/nursingCareController.js"></script>
<script src="/scripts/modules/runner/runnerController.js"></script>

<script src="/scripts/modules/nusring_care/nursingCareModal.js"></script>
<script src="/scripts/modules/nusring_care/patientDischargedModal.js"></script>
<script src="/scripts/modules/nusring_care/physicalExaminations.js"></script>
<script src="/scripts/modules/nusring_care/postPatientsToTheatreModal.js"></script>
<script src="/scripts/modules/nusring_care/wardManagementModal.js"></script>
<script src="/scripts/modules/theatre/theatreController.js"></script>

<script src="/scripts/modules/nusring_care/TimePicker.js"></script>
<script src="/scripts/modules/mortuary/mortuaryController.js"></script>
<script src="/scripts/modules/mortuary/mortuaryManagementModal.js"></script>
<script src="/scripts/modules/mortuary/corpseDisposedModal.js"></script>
<script src="/scripts/modules/mortuary/mortuaryCareModal.js"></script>
<script src="/scripts/modules/laboratory/labController.js"></script>
<script src="/scripts/modules/laboratory/equipmentsInfo.js"></script>
<script src="/scripts/modules/laboratory/LabTestRequestPatient.js"></script>
<script src="/scripts/modules/laboratory/printSampleNumberBarcode.js"></script>
<script src="/scripts/modules/regions/regionController.js"></script>
<script src="/scripts/modules/clinic/ctc/ctcController.js"></script>
<script src="/scripts/modules/clinic/ctc/ctcPatientQues.js"></script>
<script src="/scripts/modules/clinic/ctc/ctcSetup.js"></script>
<script src="/scripts/modules/Exemption/exemptionController.js"></script>
<script src="/scripts/modules/Exemption/discountController.js"></script>
<script src="/scripts/admin/userSettingController.js"></script>
<script src="/scripts/modules/regions/facilityController.js"></script>
<script src="/scripts/modules/regions/regionController.js"></script>
<script src="/scripts/modules/Pharmacy/PharmacyController.js"></script>
<script src="/scripts/modules/Pharmacy/PharmacyItemsController.js"></script>
<script src="/scripts/modules/Pharmacy/SubStoreItemsController.js"></script>
<script src="/scripts/modules/Pharmacy/DispensingController.js"></script>
<script src="/scripts/modules/Pharmacy/MediSuplyController.js"></script>
<script src="/scripts/modules/Pharmacy/PrescriptionController.js"></script>
<script src="/scripts/modules/clinic/TB/TbController.js"></script>
<script src="/scripts/modules/clinic/TB/Tb_data.js"></script>
<script src="/scripts/modules/clinic/Pediatric/PediatricController.js"></script>
<script src="/scripts/modules/clinic/VCT/VctController.js"></script>
<script src="/scripts/modules/clinic/VCT/Vct_data.js"></script>
<script src="/scripts/modules/clinic/Medical/MedicalController.js"></script>
<script src="/scripts/modules/clinic/Orthopedic/OrthopedicController.js"></script>
<script src="/scripts/modules/BloodBank/BloodBankController.js"></script>
<script src="/scripts/modules/RCH/Anti_natalController.js"></script>
<script src="/scripts/modules/RCH/PostnatalController.js"></script>
<script src="/scripts/modules/RCH/LabourController.js"></script>
<script src="/scripts/modules/RCH/Child_Controller.js"></script>
<script src="/scripts/modules/RCH/Family_planning_Controller.js"></script>
<script src="/scripts/modules/RCH/Rch_reoprtController.js"></script>
<script src="/scripts/modules/Payment_type/payment_typeController.js"></script>
<script src="/scripts/modules/Item_setups/itemSetupController.js"></script>
<script src="/scripts/modules/Item_setups/itemPriceController.js"></script>
<script src="/scripts/modules/payments/paymentsController.js"></script>

<script src="/scripts/modules/payments/topupController.js"></script>

<script src="/scripts/modules/payments/receiptsController.js"></script>
<script src="/scripts/modules/payments/shopController.js"></script>
<script src="/scripts/modules/payments/printReceipt.js"></script>
<script src="/scripts/modules/payments/posReceipts.js"></script>
<script src="/scripts/modules/payments/reportsController.js"></script>
<script src="/scripts/modules/payments/financeControlsController.js"></script>
<script src="/scripts/modules/clinicalServices/opdController.js"></script>
<script src="/scripts/modules/clinicalServices/opdQueueController.js"></script>
<script src="/scripts/modules/clinicalServices/admissionModal.js"></script>
<script src="/scripts/modules/clinicalServices/ipdController.js"></script>
<script src="/scripts/modules/clinicalServices/ipdQueueController.js"></script>
<script src="/scripts/modules/icu/icuController.js"></script>
<script src="/scripts/modules/icu/icuModals.js"></script>
<script src="/scripts/modules/clinic/dental/dentalHomeController.js"></script>
<script src="/scripts/modules/clinic/dental/dentalController.js"></script>
<script src="/scripts/modules/clinic/eye/eyeHomeController.js"></script>
<script src="/scripts/modules/clinic/eye/eyeController.js"></script>
<script src="/scripts/modules/clinic/surgical/surgicalHomeController.js"></script>
<script src="/scripts/modules/clinic/surgical/surgicalController.js"></script>
<script src="/scripts/modules/clinic/obgy/obgyHomeController.js"></script>
<script src="/scripts/modules/clinic/obgy/obgyController.js"></script>
<script src="/scripts/modules/referral/referralController.js"></script>
<script src="/scripts/modules/insurance/insuranceController.js"></script>
<script src="/scripts/modules/insurance/claimsModal.js"></script>
<script src="/scripts/modules/referral/referralModal.js"></script>
<script src="/scripts/modules/laboratory/laboratorySettingController.js"></script>
<script src="/scripts/modules/laboratory/patientresultcontroller.js"></script>
<!-- Radiology Module   -->
<script src="/scripts/modules/radiology/radiologyController.js"></script>
<script src="/scripts/modules/radiology/radiopatientsController.js"></script>
<script src="/scripts/modules/radiology/queManagementModal.js"></script>
<script src="/scripts/modules/radiology/radiologyDepartmentController.js"></script>
<script src="/scripts/modules/radiology/radiologyViewController.js"></script>
<script src="/scripts/modules/radiology/radiologyTestController.js"></script>
<script src="/scripts/modules/radiology/deviceModal.js"></script>
<!--Emergency Module    -->
<script src="/scripts/modules/emergency/emergencyController.js"></script>
<script src="/scripts/modules/emergency/emergencyModal.js"></script>
<script src="/scripts/modules/emergency/urgencyModal.js"></script>
<script src="/scripts/modules/emergency/emergencyprintCard.js"></script>
<script src="/scripts/modules/emergency/emergencydepartmentController.js"></script>
<script src="/scripts/modules/emergency/normalRegistrationController.js"></script>
<script src="/scripts/modules/emergency/treatmentDepartmentController.js"></script>
<script src="/scripts/modules/emergency/casualtyRoomController.js"></script>
<script src="/scripts/modules/emergency/observationRoomController.js"></script>
<script src="/scripts/modules/patient_tracing/patient_tracerController.js"></script>
<script src="/scripts/modules/Environmental/EnvironmentalController.js"></script>
<script src="/scripts/modules/inventory/inventoryController.js"></script>
<script src="/scripts/modules/inventory/inventoryClientController.js"></script>
<!-- Vital Sign-->
<script src="/scripts/modules/VitalSign/VitalSignController.js"></script>
<script src="/scripts/modules/VitalSign/vitalModal.js"></script>
<!-- Cardiac Clinic   -->
<script src="/scripts/modules/clinic/cardiac/cardiacController.js"></script>
<!-- Diabetic Clinic   -->
<script src="/scripts/modules/clinic/diabetic/diabeticController.js"></script>
<script src="/scripts/modules/clinic/diabetic/diabeticClinicController.js"></script>
<!-- Physiotherapy Controller   -->
<script src="/scripts/modules/clinic/physiotherapy/physioController.js"></script>
<script src="/scripts/modules/clinic/physiotherapy/physiotherapyController.js"></script>
<!-- Diabetic Clinic   -->
<script src="/scripts/modules/clinic/diabetic/diabeticClinicController.js"></script>
<!-- Dermatology Clinic   -->
<script src="/scripts/modules/clinic/dermatology/dermatologyController.js"></script>
<script src="/scripts/modules/clinic/Urology/Urology_Controller.js"></script>
<script src="/scripts/modules/nusring_care/opdNursingController.js"></script>
<script src="/scripts/modules/Drf/DrfController.js"></script>
<!--nutrition -->
<script src="/scripts/modules/clinic/Nutrition/NutritionController.js"></script>
<script src="/scripts/modules/clinic/Ent/Ent_Controller.js"></script>
<script src="/scripts/modules/General_Appointment/General_AppointmentController.js"></script>
<!-- Psychiatric  Controller   -->
<script src="/scripts/modules/clinic/psychiatric/psychiatricHomeController.js"></script>


<!--  Models  -->
<script src="/scripts/services/Helpers.js"></script>
<script src="/scripts/services/ClinicalServices.js"></script>
<script src="/scripts/services/models.js"></script>
<script src="/scripts/directives/hamburger.js"></script>
<script src="/scripts/directives/avatar.js"></script>
<script src="/scripts/modules/general/generalLabController.js"></script>
<script src="/scripts/modules/general/generalLabServices.js"></script>
<script src="/scripts/modules/general/sampleSrefController.js"></script>
<script src="/scripts/modules/general/sampleServices.js"></script>
<script src="/scripts/modules/general/testSrefController.js"></script>
<script src="/scripts/modules/performance/drPerformanceController.js"></script>
<script src="/scripts/modules/performance/doctorServices.js"></script>
<script src="/scripts/modules/reception/receptionController.js"></script>
<script src="/scripts/modules/reception/receptionServices.js"></script>
<script src="/scripts/modules/reception/searchServices.js"></script>

<!-- Trauma -->

<script src="/scripts/modules/trauma/Services.js"></script>
<script src="/scripts/modules/trauma/controllers/TraumaController.js"></script>
<script src="/scripts/modules/trauma/controllers/TriageController.js"></script>

<!--NHIF -->
<script src="/scripts/md-time-picker.js"></script>
<script src="/scripts/modules/nhif/controllers/NhifController.js"></script>
<!--NHIF -->
</body>

</html>