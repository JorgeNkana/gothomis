/**
 * Created by Mazigo Jr on 2017-02-28.
 */
(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('financeControlsController', financeControlsController);
    function financeControlsController($http, $scope, $rootScope, Helper) {
        var user_id = $rootScope.currentUser.id;
		var facility_id = $rootScope.currentUser.facility_id;
		$scope.totalCancelledValue = 0;
		
        $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
			$scope.cardTitle=cardTitle.data[0];                      
        });
		
		$scope.cancellations = [];
		
		$scope.changePayOption = function(){
			var html = '<form class="form-horizontal" role="form" name="myForm" autocomplete="off" >\
				<br />\
				<div class="row">\
					<div class="form-group">\
						<label class="col-md-3 control-label">User:</label>\
						<div class="col-md-9">\
							<input type="text" disabled class="form-control" value="' +$rootScope.currentUser.name+ '"/>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Action:</label>\
						<div class="col-md-9">\
							<select id="transaction" class="form-control">\
								<option value="">Change of Payment Method in Facility</option>\
							</select>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Change To:</label>\
						<div class="col-md-9">\
							<select id="method" class="form-control">\
								<option value="1">GePG</option>\
								<option value="0">Allow Cash through GoTHOMIS</option>\
							</select>\
						</div>\
					</div>\
				</div>\
				<br /><div class="col-md-12 text-center">Proceed?</div>\
				</form>';
			swal({
					title: 'Changing of Payment Method Mode',
					html: html,
					type: 'info',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes',
					customClass: 'swal-wide',
					allowOutsideClick:false
				}).then(function () {
						Helper.overlay(true);
						$http.post('/gepg/new/changePayOption', {Option: $('#method').val(), UserId: user_id}).then(function(response){
							Helper.overlay(false);
							swal({
									title:'PAYMENT METHOD',
									html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
									type:'info',
									customClass: 'swal-wide',
									allowOutsideClick:false
								});
							}, function(data){Helper.overlay(false);});
					}, function(){ return;});
		}
		

        $scope.getCancelledBills = function (dateRange) {
            $http.post('/api/getCancelledBills', {
                "start": dateRange.start,
                "end": dateRange.end,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cancellations = data.data;
				$scope.totalCancelledValue = 0;
				$scope.cancellations.forEach(function(record){
					$scope.totalCancelledValue += parseFloat(record.amount);
				});
            });
        }
		
        $scope.printReport = function (divtoprint) {
            var DocumentContainer = document.getElementById(divtoprint);
            var WindowObject = window.open("", "PrintWindow");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();
            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });
        };
    }
})();