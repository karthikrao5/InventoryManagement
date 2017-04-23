angular.module("app.controllers").controller("UsersController", ["$scope", "$http", "$location", "$window","APIService", "uiGridConstants","$filter", 
	function($scope, $http, $location, $window, APIService) {


		$scope.data;

		function onSuccess(response) {
	    	var local = response.data.equipments;
	    	$scope.gridOptions.data = response.data.users;
	    }

	   // make database call
	    APIService.query("users", onSuccess, function(error) {
	    	console.log(error);
	    });


		$scope.refreshData = function() {
			$scope.gridOptions.data = $filter('filter')($scope.data, $scope.searchText);
		};
		

	    $scope.columns = [{field: "username", enableHiding: false},
	    				  {field: "email", enableHiding: false},
	    				  {name: "Actions", enableHiding: false, cellTemplate:"<a href=\"#!/loans/{{row.entity.username}}\">Loan</a>/<a href=\"#!/users/{{row.entity.username}}\">Return Items</a>" }
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

	}
]);