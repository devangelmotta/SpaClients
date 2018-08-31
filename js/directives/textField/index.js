angularApp.directive('textField', function(){
	return {
		scope:{
			title: "=",
			target: "=",
			prefix: "=",
			icon: "=?",
			disabled: "=",
		},
		controller: 'textField',
		templateUrl: '../js/directives/textField/template.html',
	}
});