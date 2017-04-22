angular.module("app.controllers").controller("EditEquipmentController", ["$scope", "$http", "$location", "APIService", 
	function($scope, APIService, $routeParams, $http) {

		$scope.test = $routeParams.department_tag;

		$scope.item;

		$scope.formObj = {};

		$scope.update_equipment = {};
		$scope.update_equipment_attributes = [];
		$scope.add_equipment_attributes = [];
		$scope.remove_equipment_attributes = [];


		// APIService.get('equipments', $routeParams, function(response) {
		// 	$scope.item = response.data.equipments[0];
		// 	$scope.formObj.department_tag = $scope.item.department_tag;
		// 	$scope.formObj.gt_tag = $scope.item.gt_tag;
		// }, function(error) {
		// 	console.log(error.data);
		// });

		$http({
			url: 'v1/equipments',
			method: 'GET',
			params: $routeParams,
			paramSerializer: '$httpParamSerializerJQLike'
		}).then(function(response) {
			$scope.item = response.data.equipments[0];
			$scope.formObj.department_tag = $scope.item.department_tag;
			$scope.formObj.gt_tag = $scope.item.gt_tag;
		}, function(error) {
			console.log(error.data);
		});

		


		$scope.labels = ['department_tag', "gt_tag", "comment"];


		$scope.test = function() {
			console.log($scope.formObj);
		};
	}

]);