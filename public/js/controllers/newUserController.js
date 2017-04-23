angular.module("app.controllers").controller("NewUserController", ["$scope", "$http", "$location", "APIService", 
	function($scope, $http, $location, APIService) {
		$scope.title = "Create user";
		$scope.formObj = {};

		$scope.submitUserCreation = function() {

			var returnThis = angular.toJson($scope.formObj, true);

			function onSuccess(response) {
				console.log(response.data);
				$location.path('/users');
			}

			APIService.post("users", returnThis, onSuccess, function(error) {
				console.log(error);
			});
		};

	}
]);