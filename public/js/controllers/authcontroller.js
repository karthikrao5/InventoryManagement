angular.module("app.controllers").controller('AuthController', ["$scope", "$http",
	function($scope, $http) {
		var endpoint = 'http://localhost:8080/v1/equipments';

	    $http.get(endpoint).then(function (response) {
	        $scope.equipments = response.data.equipments;
	    });
	}
]);