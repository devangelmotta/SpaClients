(function(){
	'use strict';

	angular
	.module('support')
	.factory('Modal', modal);

	modal.$inject = ['$http', '$compile', '$rootScope', '$document', '$q', '$controller', '$timeout'];

	function modal($http, $compile, $rootScope, $document, $q, $controller, $timeout) {
		var service = {
			open: open
		};

		function open(options) {
			/// <summary>Opens a modal</summary>
			/// <param name="options" type="Object">
			/// ? title {string} The title of the modal.<br />
			/// ? scope {$scope} The scope to derive from. If not passed, the $rootScope is used<br />
			/// ? params {object} Objects to pass to the controller as $modalInstance.params<br />
			/// ? properties {object} Objects to pass to the controller as openModalOptions
			/// ? template {string} The HTML of the view. Overriden by @templateUrl<br />
			/// ? templateUrl {string} The URL of the view. Overrides @template<br />
			/// ? fixedFooter {boolean} TRUE if the modal should have a fixed footer<br />
			/// ? controller {string||array||function} A controller definition<br />
			/// ? controllerAs {string} the controller alias for the controllerAs sintax. Requires @controller
			/// </param>
			/// <param name="options.title" type="String">The title of the window</param>
			/// <returns type="$.when" />

			var deferred = $q.defer();

			getTemplate(options).then(function (modalBaseTemplate) {
				var modalBase = angular.element(modalBaseTemplate);

				var scope = $rootScope.$new(false, options.scope);
				var modalInstance = {
					params: options.params || {},
					close: function (result) {
						deferred.resolve(result);
						closeModal(modalBase, scope);
					},
					dismiss: function (reason) {
						deferred.reject(reason);
						closeModal(modalBase, scope);
					}
				};

				scope.$close = modalInstance.close;
				scope.$dismiss = modalInstance.dismiss;

				$compile(modalBase)(scope);

				var openModalOptions = options.properties;
				/*var openModalOptions = {
					//ready: function () { alert('Ready'); }, // Callback for Modal open
					complete: function () { modalInstance.dismiss(); } // Callback for Modal close
				};
				*/

				runController(options, modalInstance, scope);
				console.log(openModalOptions);
				modalBase.appendTo('body').modal(openModalOptions);
				$timeout(function(){
					modalBase.modal('open');
				}, 250, true);

			}, function (error) {
				deferred.reject({ templateError: error });
			});

			return deferred.promise;
		}

		function runController(options, modalInstance, scope) {
			/// <param name="option" type="Object"></param>

			if (!options.controller) return;

			angular.extend(modalInstance, {data: options.bindToInstance});
			var controller = $controller(options.controller, {
				$scope: scope,
				modalInstance: modalInstance
			});

			if (angular.isString(options.controllerAs)) {
				scope[options.controllerAs] = controller;
			}
		}

		function getTemplate(options) {
			var deferred = $q.defer();

			if (options.templateUrl) {
				$http.get(options.templateUrl).then(function successCallback(response){
					deferred.resolve(response.data);
				}, function errorCallback(response){
					deferred.reject(response);
				});
			} else {
				deferred.resolve(options.template || '');
			}


			return deferred.promise.then(function (template) {

				//var cssClass = options.fixedFooter ? 'modal modal-fixed-footer' : 'modal';
				var html = [];
				/*html.push('<div class="' + cssClass + '">');
				if (options.title) {
					html.push('<div class="modal-header">');
					html.push(options.title);
					html.push('<a class="grey-text text-darken-2 right" ng-click="$dismiss()">');
					html.push('<i class="fas fa-times" />');
					html.push('</a>');
					html.push('</div>');
				}
				*/
				html.push(template);
				//html.push('</div>');
				
				return html.join('');
			});
		}

		function closeModal(modalBase, scope) {
			/// <param name="modalBase" type="jQuery"></param>
			/// <param name="scope" type="$rootScope.$new"></param>

			modalBase.modal('close');

			$timeout(function () {
				scope.$destroy();
				modalBase.remove();
			}, 2000, true);
		}

		return service;
	}
})();