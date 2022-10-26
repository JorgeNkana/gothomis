(function () {
    'use strict';

    angular.module('authApp').factory('Helper', ['$mdToast', '$http', function($mdToast, $http) {
        
		return {
			
            notify: function (message) {
                $mdToast.show($mdToast.simple()
                    .position('top right')
                    .content(message)
                    .hideDelay(3000)
                );
            },
			searchLabTechnologist : function (text) {
                return $http.post('/api/searchLabTechnologists?keyWord=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            alert: function (message) {
                $mdToast.show($mdToast.simple()
                    .position('top left')
                    .content(message)
                    .hideDelay(4000)
                );
            },
			searchItemObservations: function (searchKey) {
				var postData={item_name:searchKey};
                return $http.post('/api/searchItemObserved',postData)
                    .then(function (response) {
                        return response;
                    });
            },
			
			 systemNotification : function (user_id) {
				/*var message='';
				setInterval(function(){ 
				return $http.get('/api/mynotifications/'+ user_id)
						.then(function (data) {	
						if(data.data.length>0){
							message=data.data[0].message;				
						$mdToast.show($mdToast.simple()
						.position('top right')
						.content(message)
						.hideDelay(30000)					
					);
					}

					});
				}, 360000); // Th
                */
            },
			

           //Patients Record Search
            getPatients : function (text) {
                return $http.post('/api/search-patients?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getPatientToEdit : function (text) {
                return $http.post('/api/editable-patients?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getPatientToEncounter : function (text) {
                return $http.post('/api/patient-encounter?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getAllPatient : function (text) {
                return $http.post('/api/get-patient?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },

			searchItemToServiceInWard : function (searchKey,selectedPatient,facility_id) {
				var dataToPost={patient_category_id:selectedPatient.patient_category_id,search:searchKey,facility_id:facility_id};				
                return $http.post('/api/getListItemToServiceInWard',dataToPost)
                    .then(function (response) {
                        return response;
                    });
            },
			
			searchUser : function (email,facility_id) {
				var dataToPost={email:email,facility_id:facility_id};				
                return $http.post('/api/searchUser',dataToPost)
                    .then(function (response) {
                        return response;
                    });
            },
            //Seach Trauma Patients Consultation
            TraumaSeachQueue : function (text,facility_id) {
                return $http.post('/api/trauma-patients',{
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            //Seach Trauma Patients Investigation
            TraumaTreatmentSeachQueue : function (text,facility_id) {
                return $http.post('/api/traumaInv-patients',{
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            Radiopatients : function (text) {
                return $http.post('/api/getAllRadiographics?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getResidence : function (text) {
				return $http.post('/api/residence-patients?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getVitalsUsers : function (text) {
                return $http.post('/api/vitals-patients?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getAllItems : function (text) {
                return $http.post('/api/item-search?name=' + text)
                    .then(function (response) {
                        return response;
                    });
            },
            getAppointmentCardio : function (text,facility_id) {
                return $http.post('/api/cardiac-apointment',{
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            getAppointmentPhysio : function (text,facility_id) {
                return $http.post('/api/physio-apointment',{
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },getRadiologyPatients : function (text,facility_id) {
                return $http.post('/api/xray-patients',{
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            getAppointmentCardioRefer : function (text,facility_id) {
                console.log(facility_id);
                return $http.post('/api/cardiac-refer', {
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },
            getAppointmentPhysioRefer : function (text,facility_id) {
                console.log(facility_id);
                return $http.post('/api/physio-refer', {
                    "name": text,
                    "facility_id": facility_id
                })
                    .then(function (response) {
                        return response;
                    });
            },
			
            getReferringFacilities : function (text) {
                return $http.post('/api/getReferringFacilities', {
                    "key": text
                })
                    .then(function (response) {
                        return response;
                    });
            },
			overlay: function (flag = false) {
				if(flag==true){
					var overlayDiv = jQuery('<div id="overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; padding-top:-10px; background-color: white; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5;  z-index: 10000; text-align:center"><img src="/img/wait.gif" /><div id="counter" style="position:relative; top:-145px;font-weight:bold;font-size:32px"></div></div>');
					overlayDiv.appendTo(document.body);
				}else
					$("#overlay").remove();
			},


			temporaryError: function (target,custom_msg=''){
				return "A temporary error occured in the server while loading <b>"+target+"</b><br />"+(custom_msg != '' ? "<b><i>"+ custom_msg +"<i></b>": "This is usually an <b><i>arbitrary error<i></b>. <b>You may switch back to the dashboard and enter the module or re-attempt the action again</b>.")+"<br />If the error persists, please contact IT support.";
			},
			
			genericError: function (action,custom_msg=''){
				return "A temporary error occured in the server while <b>"+action+"</b><br />"+(custom_msg != '' ? "<b><i>"+ custom_msg +"<i></b>" : "This is usually an <b><i>arbitrary error<i></b>. <b>Your action may not have been effected, Retry</b>.")+"<br />If the error persists, please contact IT support.";
			},
			
			
			sendToDHIS: function(report){
				var self = this;
				var parameters = {
									facility_id: report.facility_id, 
									start_date:report.start_date, 
									end_date:report.end_date, 
									complete:report.complete, 
									mtuha_book:report.mtuha_book
								};
								
				self.overlay(true);
				$http.post('/api/sendToDHIS', parameters).then(function (data) {
					self.overlay(false);
					swal({
						title:'<h3>DHIS2 DATA UPLOAD <span style="color:red">(DEMO)</span></h3>',
						html: data.data.description+(data.data.importCount ? '<br /><pre>Imported: '+data.data.importCount.imported+', Updated: '+data.data.importCount.updated+', Ignored: '+data.data.importCount.ignored+', Deleted: '+data.data.importCount.deleted+'</pre>' : ''),
						type: data.data.status.toLowerCase()
					});
				 }, function(data){self.overlay(false);});
			},
			
			
			setParameters: function(mtuha_book){
				swal({
					  title: 'TAHADHARI',
					  html: '<span style="color:red">HUDUMA HII BADO HAIJARUHUSIWA KUTUMIKA. TAFADHALI CHAPISHA JEDWALI NA KULIINGIZA KWENYE DHIS KWA NJIA YA KAWAIDA',
					  type: 'warning',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#d33',
					  confirmButtonText: 'OK'
					}).then(function () {
						return;
				}, function(){ return;});
								
								
				var self = this;
				var today = new Date();
				var html = '<form class="form-horizontal" role="form" name="myForm" >\
								<br />\
								<div class="row">\
									<div class="form-group">\
										<label class="col-md-3 control-label">DataSet:</label>\
										<div class="col-md-9">\
											<input type="text" disabled class="form-control" value="' +mtuha_book.book_name.toUpperCase() + '"/>\
										</div>\
									</div>\
									<div class="form-group">\
										<label class="col-md-3 control-label">Complete:</label>\
										<div class="col-md-9">\
											<input type="checkbox" id="complete"  title="Tick to tell DHIS that this data is final" class="pull-left swal"  style="margin-top: 5px;width:30px;height:30px;"/>\
										</div>\
									</div>\
									<div class="form-group">\
										<label class="col-md-3 control-label" style="">Period:</label>\
										<div class="col-md-9">\
											<select id = "period" class="form-control" title="Report Month" />\
												<option value = "01"'+(today.getMonth() == 0 ? ' selected ' : '')+(today.getMonth() == 0 ? '' : today.getMonth()-1 == 0 ? '' : ' disabled ')+'>January</option>\
												<option value = "02"'+(today.getMonth() == 1 ? ' selected ' : '')+(today.getMonth() == 1 ? '' : today.getMonth()-1 == 1 ? '' : ' disabled ')+'>February</option>\
												<option value = "03"'+(today.getMonth() == 2 ? ' selected ' : '')+(today.getMonth() == 2 ? '' : today.getMonth()-1 == 2 ? '' : ' disabled ')+'>March</option>\
												<option value = "04"'+(today.getMonth() == 3 ? ' selected ' : '')+(today.getMonth() == 3 ? '' : today.getMonth()-1 == 3 ? '' : ' disabled ')+'>April</option>\
												<option value = "05"'+(today.getMonth() == 4 ? ' selected ' : '')+(today.getMonth() == 4 ? '' : today.getMonth()-1 == 4 ? '' : ' disabled ')+'>May</option>\
												<option value = "06"'+(today.getMonth() == 5 ? ' selected ' : '')+(today.getMonth() == 5 ? '' : today.getMonth()-1 == 5 ? '' : ' disabled ')+'>June</option>\
												<option value = "07"'+(today.getMonth() == 6 ? ' selected ' : '')+(today.getMonth() == 6 ? '' : today.getMonth()-1 == 6 ? '' : ' disabled ')+'>July</option>\
												<option value = "08"'+(today.getMonth() == 7 ? ' selected ' : '')+(today.getMonth() == 7 ? '' : today.getMonth()-1 == 7 ? '' : ' disabled ')+'>August</option>\
												<option value = "09"'+(today.getMonth() == 8 ? ' selected ' : '')+(today.getMonth() == 8 ? '' : today.getMonth()-1 == 8 ? '' : ' disabled ')+'>September</option>\
												<option value = "10"'+(today.getMonth() == 9 ? ' selected ' : '')+(today.getMonth() == 9 ? '' : today.getMonth()-1 == 9 ? '' : ' disabled ')+'>October</option>\
												<option value = "11"'+(today.getMonth() == 10 ? ' selected ' : '')+(today.getMonth() == 10 ? '' : today.getMonth()-1 == 10 ? '' : ' disabled ')+'>November</option>\
												<option value = "12"'+(today.getMonth() == 11 ? ' selected ' : '')+(today.getMonth() == 11 ? '' : today.getMonth()-1 == -1 ? '' : ' disabled ')+'>December</option>\
											</select>\
										</div>\
									</div>\
								</div>\
								<br /><div class="col-md-12 text-center">Proceed?</div>\
							</form>';
				swal({
					  title: 'Select Parameters to proceed',
					  html: html,
					  type: 'info',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#d33',
					  confirmButtonText: 'Yes'
					}).then(function () {
						//validate
						if(!$('#period').val()){
							swal('You must select the reporting time','','info');
							return;
						}
						var report = {
							facility_id:mtuha_book.facility_id,
							mtuha_book:mtuha_book.book_name,
							start_date:((new Date()).getFullYear())+'-'+($('#period').val())+'-01',
							end_date:((new Date()).getFullYear())+'-'+($('#period').val())+'-'+(new Date(((new Date()).getFullYear()), ($('#period').val()), 0)).getDate(),
							complete:$('#complete').prop('checked')
						};
						
						var submitReport = function(){
							if(report.complete){
								swal({
									  title: 'PLEASE CONFIRM',
									  html: '<span style="color:red">You have chosen the report to be submitted as COMPLETE. Note that once the submission is successfully, the corresponsing month will effectivelly be locked on DHIS.</span><br /><hr /><br /> Do you want to proceed?',
									  type: 'warning',
									  showCancelButton: true,
									  confirmButtonColor: '#3085d6',
									  cancelButtonColor: '#d33',
									  confirmButtonText: 'Yes'
									}).then(function () {
										self.sendToDHIS(report);
								}, function(){ return;});
							}else{
								swal({
									  title: 'PLEASE CONFIRM',
									  html: '<span style="color:red">You have chosen the report to be submitted without being marked as COMPLETE. Make sure to resubmit the report with COMPLETE option before the end of the following month.</span><br /><hr /><br /> Do you want to proceed?',
									  type: 'warning',
									  showCancelButton: true,
									  confirmButtonColor: '#3085d6',
									  cancelButtonColor: '#d33',
									  confirmButtonText: 'Yes'
									}).then(function () {
										self.sendToDHIS(report);
								}, function(){ return;});
							}
						}
						
						if(today.getMonth()+1 == $('#period').val()){//report of the current month.
							//must be the end of the month
							if(today.getDate() != report.end_date.split('-')[2]){
								swal({
									  title: 'PLEASE CONFIRM',
									  html: '<span style="color:red">A report submitted before the end of the month must be re-submitted after the month ends.</span><br /><hr /><br /> Do you want to proceed?',
									  type: 'warning',
									  showCancelButton: true,
									  confirmButtonColor: '#3085d6',
									  cancelButtonColor: '#d33',
									  confirmButtonText: 'Yes'
									}).then(function () {
										//make sure it is marked partial before submitting
										report.complete = false;
										submitReport();
								}, function(){ return;});
							}												
						}else
							submitReport();
						
					}, function(){ return;});
			},
			
			printHTML: function(html,facility_id){
				var printer = window.open("", "HMIS SUMMARY");
				printer.document.writeln(html);
				printer.document.close();
				printer.focus();
				printer.print();
				printer.close();
				return;
				/*
				var fileName = "HMIS.pdf";
				var a = document.createElement("a");
				document.body.appendChild(a);
				a.style = "display: none";
				$http.post('/api/pdfPrinting', {html:html, orientation:'landscape',facility_id:facility_id}).then(function (data) {
					var file = new Blob([data.data], {type: 'application/pdf'});
					var fileURL = window.URL.createObjectURL(file);
					a.href = fileURL;
					a.target = '_blank';
					a.download = fileName;
					a.click();
				});
				*/
			},
			
			reportDefaultDates: function(){
				var dates = {
							start_date:((new Date()).getFullYear())+'-'+(((new Date()).getMonth()+1).toString().length == 2 ? ((new Date()).getMonth()+1).toString(): '0'+((new Date()).getMonth()+1).toString())+'-01',
							
							end_date:((new Date()).getFullYear())+'-'+(((new Date()).getMonth()+1).toString().length == 2 ? ((new Date()).getMonth()+1).toString(): '0'+((new Date()).getMonth()+1))+'-'+(new Date(((new Date()).getFullYear()), ((new Date()).getMonth()+1), 0)).getDate(),
				}
				return dates;
			}
			
        }
    }]);
})();