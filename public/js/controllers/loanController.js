angular.module("app.controllers").controller("LoanController", ["$scope", "$http", "$location", "$window","APIService", "uiGridConstants","$filter", 
	function($scope, $http, $location, $window, APIService) {
		// if(Auth.isExpired()) {
		// 	console.log("redirecting to auth...");
		// 	$location.path('/auth');
		// }

		$scope.data;

		// if (Auth.isAdminOrHook()) {
		// 	$scope.loggedInUser = $scope.user+" admin.";
		// }



		function onSuccess(response) {
	    	var local = response.data.users;
	    	$scope.gridOptions.data = response.data.users;
	    }

	   // make database call
	    APIService.query("users", onSuccess, function(error) {
	    	console.log(error);
	    });

		// if token in localstorage is expired, redirect to auth
		// if (Auth.isExpired()) {
		// 	$location.path("/auth");
		// }
		// var endpoint = 'http://localhost:8080/v1/equipments';


		$scope.refreshData = function() {
			$scope.gridOptions.data = $filter('filter')($scope.data, $scope.searchText);
		};
		

	    $scope.columns = [{field: "username", enableHiding: false},
	    				  {field: "email", enableHiding: false},
	    			];

	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true,
	    	multiSelect: false

	    };

	    $scope.toggleFiltering = function() {
	    	console.log("Filtering tbd...");
	    };

	    $scope.deleteEquipment = function(departmentTag) {
	    	APIService.delete("equipments", [departmentTag], function(response) {
	    		alert("Successfully deleted equipment!");
	    	}, function(error) {
	    		console.log(error.data);
	    	});
	    };

	    // $scope.logout = function() {
	    // 	if(Auth.deleteToken()) {
	    // 		console.log("Logged out!");
	    // 		console.log("Token is now: " + $window.localStorage.getItem("jwt"));
	    // 	}
	    // };

	}
]);