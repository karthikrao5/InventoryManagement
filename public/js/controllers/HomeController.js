angular.module("app.controllers").controller("HomeController", ["$scope", "$http", "$location", "$window","APIService", "uiGridConstants","$filter", 
	function($scope, $http, $location, $window, APIService, uiGridConstants) {

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
		

	    $scope.columns = [{field: "department_tag", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
	    				  {field: "gt_tag", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
	    				  {field: "status", enableHiding: false,filter: {
							          type: uiGridConstants.filter.SELECT,
							          selectOptions: [ {value: 'inventory', label: 'inventory'}, { value: 'loaned', label: 'loaned'} ]
				      			}},
	    				  {field: "loaned_to", enableHiding: false},
	    				  {field: "equipment_type_name", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
	    				  {field: "created_on", enableHiding: false},
	    				  {name: "Actions", enableHiding: false, cellTemplate:"<a href=\"#!/equipments/{{row.entity.department_tag}}\">Edit</a>/<a href=\"\" confirmed-click=\"grid.appScope.deleteEquipment(row)\" ng-confirm-click=\"Are you sure you want to delete this Equipment?\">Delete</a>", enableFiltering: false }
	    			];

	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true,
	    	enableFiltering: true
	    };

	    $scope.toggleFiltering = function() {
	    	console.log("Filtering tbd...");
	    };


	    $scope.deleteEquipment = function(row) {

	    	// alert("clicked!");

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