angular.module("app.controllers").controller("HomeController", ["$scope", "$http", "$location", "$window","APIService", "uiGridConstants","$filter", 
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
	    	var local = response.data.equipments;
	    	$scope.gridOptions.data = response.data.equipments;
	    }

	   // make database call
	    APIService.query("equipments", onSuccess, function(error) {
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
		

	    $scope.columns = [{field: "department_tag", enableHiding: false},
	    				  {field: "gt_tag", enableHiding: false},
	    				  {field: "status", enableHiding: false},
	    				  {field: "loaned_to", enableHiding: false},
	    				  {field: "equipment_type_name", enableHiding: false},
	    				  {field: "created_on", enableHiding: false},
	    				  {name: "Actions", enableHiding: false, cellTemplate:"<a href=\"#!/loans/{{row.entity.department_tag}}\">Loan</a>/<a href=\"#!/equipments/{{row.entity.department_tag}}\">Edit</a>/<a href=\"\" ng-confirm-click=\"Are you sure you want to delete this item?\"ng-click=\"deleteEquipment(row.entity.department_tag)\">Delete</a>" }
	    			];

	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true
	    };

	    $scope.toggleFiltering = function() {
	    	console.log("Filtering tbd...");
	    };

	    $scope.deleteEquipment = function(row) {

	    	var deleteThis = {};
	    	deleteThis["department_tag"] = row.entity.department_tag;

	    	var jsonBody = angular.toJson(deleteThis, 1);
	    	console.log(jsonBody);

	    	APIService.delete("equipments", jsonBody, function(response) {
	    		var index = $scope.gridOptions.data.indexOf(row.entity);
	    		$scope.gridOptions.data.splice(index, 1);
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