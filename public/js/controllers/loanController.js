angular.module("app.controllers").controller("LoanController", ["$scope", "$routeParams", "$location", "APIService", "uiGridConstants", 
	function($scope, $routeParams, $location, APIService,uiGridConstants) {

		$scope.dueDate = new Date();

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

		Number.prototype.padLeft = function(base,chr){
			var  len = (String(base || 10).length - String(this).length)+1;
			return len > 0? new Array(len).join(chr || '0')+this : this;
		}

		$scope.dueDateCtrl = function() {
		    var dformat = [($scope.dueDate.getMonth()+1).padLeft(),
		               $scope.dueDate.getDate().padLeft(),
		               $scope.dueDate.getFullYear()].join('-') +' ' +
		              [$scope.dueDate.getHours().padLeft(),
		               $scope.dueDate.getMinutes().padLeft(),
		               $scope.dueDate.getSeconds().padLeft()].join(':');

			$scope.dueDate = dformat;
		};
		

	    $scope.columns = [{field: "department_tag", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
	    				  {field: "gt_tag", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
	    				  {field: "status", enableHiding: false, enableFiltering: false},
	    				  {field: "equipment_type_name", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
	    				  {field: "created_on", enableHiding: false, headerCellClass: $scope.highlightFilteredHeader}];

	   
	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true,
	    	multiSelect: false,
	    	enableFiltering: true
	    };

	    $scope.toggleFiltering = function() {
	    	$scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
			$scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
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

  			if ($scope.postBody.equipments.length < 1) {
  				// remove item
  				$scope.postBody["equipments"].push(id);
  			} else {
  				// remove the only item, and replace with new item
  				$scope.postBody["equipments"].splice(0, 1);
  				$scope.postBody["equipments"].push(id);
  			}

  			// not in the list
  			// if (index === -1){
  			// 	console.log("id: " + id+  " not in the list.");
  			// 	$scope.postBody["equipments"].push(id);
  			// 	console.log($scope.postBody.equipments);
  			// } else {
  			// 	console.log("id: " + id + " is in index: " + index +".");

  			// 	$scope.postBody["equipments"].splice(index, 1);
  			// 	console.log($scope.postBody.equipments);
  			// }
  		};

	    $scope.submitLoan = function() {
	    	$scope.postBody["is_return"] = false;
	    	$scope.postBody["due_date"] = $scope.dueDate;
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