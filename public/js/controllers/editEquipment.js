angular.module("app.controllers").controller("EditEquipmentController", ["$scope", "$http", "$location", "APIService", 
	function($scope, APIService, $routeParams) {

		$scope.test = $routeParams.department_tag;
	}

]);