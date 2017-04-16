// angular.module("app.controllers").controller('AuthController', ["$scope", "$http", "$window", 
// 	function($scope, $http, $window) {
// 		var endpoint = "http://localhost:8080/v1/auth";

// 		$http.get(endpoint).then(function(response) {
// 			$window.localStorage["jwt"] = response.data.jwt;
// 		});
// 	}
// ]);