angular.module("app.controllers").controller("NewUserController", ["$scope", "$http", "$location", "APIService", 
	function($scope, $http, $location, APIService) {

		$scope.formObj = {};

		$scope.submitUserCreation = function() {

			var returnThis = $scope.formObj;

			function onSuccess(response) {
				console.log(response.data);
			}

			APIService.post("users", returnThis, onSuccess, function() {
				console.log("error posting...");
			});
		};

	}
]);