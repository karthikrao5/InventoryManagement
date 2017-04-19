angular.module("app.controllers").controller("UsersController", ["$scope", "Auth", "APIService", "uiGridConstants",
	function($scope, Auth, APIService) {
		
		$scope.users;

		function onSuccess(response) {
			$scope.users = response.data.users;
		}
	}
]);