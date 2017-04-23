angular.module("app.controllers").controller("LoanController", ["$scope", "$http", "$location", "$routeParams", "$window", "APIService", "uiGridConstants","$filter", 
	function($scope, $http, $location, $routeParams, $window, APIService) {

		// $scope.originalItem;
		$scope.paramsArray = {};
		$scope.equipmentList["username"] = $routeParams.username;
		$scope.equipmentList = {};
		$scope.equipmentList["equipments"] = [];
		

		// $scope.gridOptions.onRegisterApi = function(gridApi) {
		//    $scope.myGridApi = gridApi;
		//    };

	   // make database call
	    APIService.query("equipments", onSuccess, function(error) {
	    	console.log(error);
	    });

	    function onSuccess(response) {
	    	var local = response.data.equipments;
	    	$scope.gridOptions.data = response.data.equipments;
	    }

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
	    // 	onRegisterApi: function( gridApi ) {
    	// 		$scope.gridApi = gridApi;
  			// };

	    };
	    $scope.gridOptions.onRegisterApi = function( gridApi ) {
    		$scope.gridApi = gridApi;
  		};

	    $scope.submitLoan = function() {
	    	$scope.items = $scope.gridApi.selection.getSelectedRows();
	    	angular.forEach($scope.items, function(value, key) {
			  this.push(value.department_tag);
			}, $scope.equipmentList["equipments"]);
			// console.log($scope.equipmentList["equipments"]);

			$scope.equipmentList["due_date"] = "2017-05-01 23:59:59";

			console.log(angular.toJson($scope.equipmentList));

			function onSuccess(response) {
				alert("Successfully created Loan!");
				console.log(response.data);
			}

			APIService.post("loans", $scope.equipmentList, onSuccess, function(error) {
				alert(error);
			});


			$scope.equipmentList["equipments"] = [];




		};


	    $scope.toggleFiltering = function() {
	    	console.log("Filtering tbd...");
	    };
	}
]);