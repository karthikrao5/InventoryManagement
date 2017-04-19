var ctrl = angular.module('app.controllers', []);


ctrl.controller("HomeController", ["$scope", "$location", "$window", 
	function($location, $scope, $window) {
		$scope.go = function(route) {
			$location.path( route );
		};

		$scope.logout = function() {
			$window.localStorage.$reset();
			$location.path("/");
		}
	}
]);



