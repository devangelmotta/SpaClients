(function(){
	'use strict';

	angular
		.module('support')
		.controller('detailTicket', detailTicket);

	detailTicket.$inject = ['Tickets', 'Operators', '$scope', '$rootScope', 'modalInstance'];

	function detailTicket(Tickets, Operators, $scope, $rootScope, modalInstance){
		var dt = this;

		dt.disabled = true;
		dt.operators = Operators.query();
		dt.selectedTicket = Tickets.get({id: modalInstance.data.id});
		dt.leftButton = leftButton;
		dt.rightButton = rightButton;

		function leftButton(){
			if(dt.disabled){
				dt.disabled = false;
			}
			else{
				dt.disabled = true;
				dt.selectedTicket.$update(function success(response){
					console.log(response);
					Materialize.toast('Success', 5000, 'green');
					modalInstance.close(response);
				}, function error(response){
					Materialize.toast('Error', 5000, 'red');
				});
			}
		}

		function rightButton(){
			if(!dt.disabled){
				dt.disabled = true;
				dt.selectedTicket = Tickets.get({id: modalInstance.data.id});
			}
			else{
				dt.disabled = false;
				modalInstance.dismiss("userRegret");
			}

		}
	};
})();