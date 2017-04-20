angular.module("app.controllers").controller("AuthController", ["$http", "$location", "$scope", "$window", "Auth",
	function($http, $location, $scope, $window, Auth) {

		function successAuth(response) {
			$window.localStorage.setItem("jwt", response.data.jwt);
			console.log("jwt from auth: " + $window.localStorage.getItem("jwt"));
			window.location = "/";
		}

		// var body = {"isHook": true, "hook_name" : "front-endAngular"};

		Auth.authorize(null, successAuth, function(error) {
			console.log(error);
		});
	}
]);
