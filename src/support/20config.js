(function(){
	angular
		.module('support')
		.config(httpProvider);

	httpProvider.$inject = ['$httpProvider'];
	
	function httpProvider($httpProvider) {
    	$httpProvider.interceptors.push('preventTemplateCache');
  }
})();