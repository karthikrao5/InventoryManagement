angular.module("app.controllers").controller("LoanController", ["$scope", "$http", "$location", "$window","APIService", "uiGridConstants","$filter", 
	function($scope, $http, $location, $window, APIService) {

		$scope.data;


		function onSuccess(response) {
	    	var local = response.data.equipments;
	    	$scope.gridOptions.data = response.data.equipments;
	    }

	   // make database call
	    APIService.query("equipments", onSuccess, function(error) {
	    	console.log(error);
	    });

		$scope.refreshData = function() {
			$scope.gridOptions.data = $filter('filter')($scope.data, $scope.searchText);
		};
		

	    $scope.columns = [{field: "department_tag", enableHiding: false},
	    				  {field: "gt_tag", enableHiding: false},
	    				  {field: "status", enableHiding: false},
	    				  {field: "loaned_to", enableHiding: false},
	    				  {field: "equipment_type_name", enableHiding: false},
	    				  {field: "created_on", enableHiding: false}
	    			];


	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true,
	    	multiSelect: true

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
	}
]);