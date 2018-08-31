(function(){
	'use strict';
	angular
		.module('support')
		.directive('inputSwitch', inputSwitch);

	//inputSwitch.$inject = [""];

	function inputSwitch(){
		return {
			restrict: 'E',
			scope: {},
			controller: 'inputSwitch',
			controllerAs: 'vm',
			bindToController: {
				bind: "=",
			},
			template: '<div class="switch"><label>Off<input type="checkbox" ng-model="vm.bind"><span class="lever"></span>On</label></div>'
		}
	}
})();