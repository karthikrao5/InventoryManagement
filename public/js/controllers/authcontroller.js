angular.module("app.controllers").controller("AuthController", ["$http", "$location", "$scope", "$window", "Auth",
	function($http, $location, $scope, $window, Auth) {

		function successAuth(response) {
			$window.localStorage.setItem("jwt", response.data.jwt);
			console.log($window.localStorage.getItem("jwt"));
			window.location = "/";
		}

		var body = {"isHook": true, "hook_name" : "front-endAngular"};
		Auth.authorize(JSON.stringify(body), successAuth, function() {
			console.log("Some auth error");
		});
	}
]);
