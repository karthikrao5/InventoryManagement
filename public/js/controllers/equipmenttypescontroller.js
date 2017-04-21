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
						  {field: 'attributes', enableHiding: false}];

		$scope.gridOptions = {
			enableSorting: true,
			columnDefs: $scope.columns,
			enableGridMenu: true
		};


	}
]);