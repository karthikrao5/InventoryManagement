angular.module("app.controllers").controller('EquipmentsController', ["$scope", "$http", "$location", "$window", "Auth", 
	function($scope, $http, $location, $window, Auth) {

		// if token in localstorage is expired, redirect to auth
		if (Auth.isExpired()) {
			$location.path("/auth");
		}

		var endpoint = 'http://localhost:8080/v1/equipments';
		
	    $http.get(endpoint).then(function (response) {
	        $scope.equipments = response.data.equipments;
	        console.log(angular.toJson(response.data.equipments, true));
	    });

	    $scope.go = function(route) {
			$location.path( route );
		};
	}
]);