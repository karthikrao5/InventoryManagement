angular.module('app.controllers').controller('EquipmentTypesController', ['$scope', 'APIService',
	function($scope, APIService) {

		$scope.title = "Equipment Types";

		function onSuccess(response) {
			$scope.gridOptions.data = response.data.equipment_types;
		}

		APIService.query('equipmenttypes', onSuccess, function(error) {
			console.log(error);
		});

		$scope.columns = [{field: 'name', enableHiding: false},
						  {field: 'comments', enableHiding: true},
						  {name: 'Actions', enableHiding: false, cellTemplate:"<a href=\"#!/equipmenttypes/name/{{row.entity.name}}\">Edit</a>/<button ng-click=\"grid.appScope.deleteEquipmentType(row)\">Delete</button>" }
					];

		$scope.gridOptions = {
			enableSorting: true,
			columnDefs: $scope.columns,
			enableGridMenu: true
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