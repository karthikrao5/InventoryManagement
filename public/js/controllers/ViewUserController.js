angular.module("app.controllers").controller("ViewUserController", ["$scope", "$routeParams", "$location", "APIService", "uiGridConstants", 
	function($scope, $routeParams, $location, APIService) {
		$scope.returnObject = {};

		$scope.returnObject["update_loan"] = {};


		$scope.username = $routeParams.username;

		var params = {};
		params["username"] = $routeParams.username;
		APIService.get("users", params, function(response) {
			console.log(response.data);
			// $scope.gridOptions.data = response.data.users[0]["current_loans"];
			var temp = {"equipments": []};
			// go thru all loans and pull out equipments
			angular.forEach(response.data.users[0]["current_loans"], function(loan) {
				angular.forEach(loan.equipments, function(item) {
					item["loan_id"] = loan["_id"]["$id"];
					temp.equipments.push(item);
				});
				// equipments.push(loan["equipments"])
			});
			console.log(temp.equipments);
			$scope.gridOptions.data = temp.equipments;
		}, function(error) {
			console.log(error.data);
		});

		$scope.columns = [{field: "department_tag", enableHiding: false},
						  {name: "Actions", enableHiding: false}];

		$scope.gridOptions = {
			enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true,
	    	multiSelect: true
		};

		$scope.gridOptions.onRegisterApi = function(gridApi) {
	    	$scope.gridApi = gridApi;
	    	gridApi.selection.on.rowSelectionChanged($scope,function(row){
	    		$scope.returnObject["_id"] = row.entity["loan_id"];
	  			$scope.returnObject["update_loan"]["is_return"] = true;
				// $scope.addToList(row.entity["_id"]["$id"]);
			});
			// gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
			// 	var msg = 'rows changed ' + rows.department_tag;
			// 	console.log(msg);
			// });
  		};

  		// $scope.addToList = function(id) {
  		// 	console.log("addToList: " + id);
  		// 	var index = $scope.returnObject.equipments.indexOf(id);
  		// 	// not in the list
  		// 	if (index === -1){
  		// 		$scope.returnObject["equipments"].push(id);
  		// 		console.log($scope.returnObject.equipments);
  		// 	} else {
  		// 		$scope.returnObject["equipments"].splice(index, 1);
  		// 		console.log($scope.returnObject.equipments);
  		// 	}
  		// };

  		$scope.returnSelectedItems = function() {
  			// if($scope.returnObject["_id"]) {
  			// 	alert("You must select an item to return.");
  			// }
  			console.log($scope.returnObject);
  			var jsonBody = angular.toJson($scope.returnObject, 1);
  			APIService.put("loans", jsonBody, function(response) {
  				console.log(response.data);
  				alert(response.data.msg);
  				$location.path("/users");
  			}, function(error) {
  				console.log(error.data);
  				alert(error.data);
  			})
  		};
	}
]);


// {
//     "_id" : "58fbbbf07f8b9ac357b09638",

//     "update_loan" : {
//         "loaned_date" : "2017-01-01 00:00:00",
//         "due_date" : "2017-01-01 00:00:00",
//         "is_returned" : true
//     },

//     "add_equipments" : [
//         "58fbbbf07f8b9ac357b09638"
//     ],

//     "remove_equipments" : [
//         "58fbbbf07f8b9ac357b09638"
//     ]
// }