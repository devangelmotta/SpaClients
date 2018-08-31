(function(){
	'use strict';
	angular
		.module('support')
		.factory('Operators', operators);

	operators.$inject = ['$resource', '$localStorage'];

	function operators($resource, $localStorage){
		return $resource("/php/restapi/operators.php",{});
	};
})();
