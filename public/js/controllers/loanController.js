angular.module("app.controllers").controller("LoanController", ["$scope", "$routeParams", "$location", "APIService", "uiGridConstants", 
	function($scope, $routeParams, $location, APIService) {

		$scope.postBody = {};
		$scope.postBody["username"] = $routeParams.username;
		$scope.postBody["equipments"] = [];

	   // make database call
	    APIService.query("equipments", function(response) {
	    	// $scope.gridOptions.data = response.data.equipments;

	    	var filteredData = [];
	    	angular.forEach(response.data.equipments, function(item) {
	    		if(item.status === "inventory") {
	    			filteredData.push(item);
	    		}
	    	});
	    	$scope.gridOptions.data = filteredData;
    	}, function(error) {
	    	console.log(error);
	    });

		// $scope.refreshData = function() {
		// 	$scope.gridOptions.data = $filter('filter')($scope.data, $scope.searchText);
		// };
		

	    $scope.columns = [{field: "department_tag", enableHiding: false},
	    				  {field: "gt_tag", enableHiding: false},
	    				  {field: "status", enableHiding: false},
	    				  {field: "loaned_to", enableHiding: false},
	    				  {field: "equipment_type_name", enableHiding: false},
	    				  {field: "created_on", enableHiding: false}];

	   
	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true,
	    	multiSelect: true
	    };

	    $scope.gridOptions.onRegisterApi = function(gridApi) {
	    	$scope.gridApi = gridApi;
	    	gridApi.selection.on.rowSelectionChanged($scope,function(row){
				var msg = 'row selected ' + row.entity["_id"]["$id"];
				$scope.addToList(row.entity["_id"]["$id"]);
				console.log(msg);
			});

			gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
				var msg = 'rows changed ' + rows.department_tag;
				console.log(msg);
			});
  		};

  		$scope.addToList = function(id) {
  			console.log("addToList: " + id);
  			var index = $scope.postBody.equipments.indexOf(id);

  			// not in the list
  			if (index === -1){
  				console.log("id: " + id+  " not in the list.");
  				$scope.postBody["equipments"].push(id);
  				console.log($scope.postBody.equipments);
  			} else {
  				console.log("id: " + id + " is in index: " + index +".");

  				$scope.postBody["equipments"].splice(index, 1);
  				console.log($scope.postBody.equipments);
  			}
  		};

	    $scope.submitLoan = function() {
	    	$scope.postBody["is_return"] = false;
	    	var jsonBody = angular.toJson($scope.postBody, 1);
	    	console.log(jsonBody);
	    	APIService.post("loans", jsonBody, function(response) {
	    		console.log(response.data);
	    		alert("Successfully created loan for " + $scope.postBody["username"] + "!");
	    		$location.path('/users');
	    	}, function(error) {
	    		console.log(error.data);
	    		alert(error.data);
	    	});
		};


	    // $scope.toggleFiltering = function() {
	    // 	console.log("Filtering tbd...");
	    // };
	}

]);