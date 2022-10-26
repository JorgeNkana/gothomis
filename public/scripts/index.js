angular.module('authApp', [
  'ui.router',
  'satellizer',
  'ui.bootstrap',
  'ui.bootstrap.modal',
  'angularjs-datetime-picker',
  'angularUtils.directives.dirPagination',
  'chart.js',
  'toastr',
  'ngChatbox',
  'ngMaterial',
  'ngMessages',
  'perfect_scrollbar',
  'ngMdIcons',
  'angAccordion',
  'angular-loading-bar',
  'vAccordion',
  'datatables',
  'gothomisModels'
]);

var gothomisModels = angular.module('gothomisModels', ['ngResource']);