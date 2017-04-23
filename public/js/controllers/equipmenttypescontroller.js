angular.module('app.controllers').controller('EquipmentTypesController', ['$scope', 'APIService',
	function($scope, APIService) {

		$scope.title = "Equipment Types";

		function onSuccess(response) {
			$scope.gridOptions.data = response.data.equipment_types;
		}

		APIService.query('equipmenttypes', onSuccess, function(error) {
			console.log(error);
		});

		$scope.columns = [{field: 'name', enableHiding: false, headerCellClass: $scope.highlightFilteredHeader},
						  {field: 'comments', enableHiding: true, headerCellClass: $scope.highlightFilteredHeader},
						  {name: 'Actions', enableHiding: false, cellTemplate:"<a href=\"#!/equipmenttypes/name/{{row.entity.name}}\">Edit</a>/<a href=\"\" confirmed-click=\"grid.appScope.deleteEquipmentType(row)\" ng-confirm-click=\"Are you sure you want to delete this Equipment Type?\">Delete</a>", enableFiltering: false }
					];

		$scope.gridOptions = {
			enableSorting: true,
			columnDefs: $scope.columns,
			enableGridMenu: true,
			enableFiltering: true
		};

		$scope.deleteEquipmentType = function(row) {
			var deleteThis = {};
	    	deleteThis["name"] = row.entity.name;

	    	var jsonBody = angular.toJson(deleteThis, 1);
	    	console.log(jsonBody);

	    	APIService.delete("equipmenttypes", jsonBody, function(response) {
	    		var index = $scope.gridOptions.data.indexOf(row.entity);
	    		$scope.gridOptions.data.splice(index, 1);
	    		alert("Successfully deleted equipment type!");

	    	}, function(error) {
	    		alert(error.data.msg);
	    		console.log(error.data.msg);
	    	});
		};


	}
]);