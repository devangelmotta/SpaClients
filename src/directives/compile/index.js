(function(){
  'use strict';

  angular
    .module('support')
    .directive('compile', function($compile){
      return {
        restrict: 'A',
        replace: true,
        link: function (scope, ele, attrs) {
          scope.$watch(attrs.compile, function(html) {
            ele.html(html);
            $compile(ele.contents())(scope);
          });
        }
      };
    });
})();