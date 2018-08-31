(function(){
	'use strict';
	angular
		.module('support')
		.factory('Tickets', tickets);

	tickets.$inject = ['$resource', '$localStorage'];

	function tickets($resource, $localStorage){
		return $resource("/php/restapi/tickets.php",{},{
			update:{
				method: 'PUT',
			},
			create:{
				method: 'POST',
			}
		});
	};
})();
