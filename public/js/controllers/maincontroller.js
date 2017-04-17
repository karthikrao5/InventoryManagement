var ctrl = angular.module('app.controllers', []);


ctrl.controller("HomeController", ["$scope", "$location", 
	function($location, $scope) {
		$scope.go = function(route) {
			$location.path( route );
		};
	}
]);



