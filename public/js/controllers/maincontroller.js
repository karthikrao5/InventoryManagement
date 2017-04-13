var ctrl = angular.module('app.controllers', []);

ctrl.controller('EquipmentsController', ["$scope", "$http",
	function($scope, $http) {
		var endpoint = 'http://localhost:8080/v1/equipments';

	    $http.get(endpoint).then(function (response) {
	        $scope.equipments = response.data.equipments;
	    });
	}
]);
ctrl.controller("NewEquipmentController", ["$scope", "$http", 
	function($scope, $http) {

		// $scope.equipmentTypeList = [];
		$scope.equipmentTypeList = [];
		$scope.selectedItem;

		$http.get('http://localhost:8080/v1/equipmenttypes').then(function(response) {

			$scope.equipmenttypes = response.data.equipment_types;
			var count = 0;
			angular.forEach($scope.equipmenttypes, function(item) {
				// $scope.equipmentTypeList.push(item['name']);
				temp = {};
				temp['name'] = item['name'];
				temp['id'] = item['_id']['$id'];
				$scope.equipmentTypeList.push(temp);
			});
		});

		$scope.labels = ['department_tag', "gt_tag", "status", "comment"];

		$scope.values = {};

		$scope.submitEquipmentCreation = function() {
			// console.log("button clicked");
			// console.log($scope.values);
			console.log($scope.selectedItem.name);

		}

 		// $scope.attributes = [];
		$scope.attributes = [];
		$scope.attributeKey;
		$scope.attributeValue;

		$scope.addNewAttribute = function() {
			var attr = {};
			attr['name'] = $scope.attributeKey;
			attr['value'] = $scope.attributeValue;
			$scope.attributes.push(attr);
			console.log($scope.attributes);
			// $scope.attributes.push("{\"key\" : \" " + $scope.attribute.value + "\"}");
			// var newItemNo = $scope.attributes.length+1;
			// $scope.choices.push({'id':'choice'+newItemNo});
		};

		$scope.removeAttribute = function() {
			$scope.attributes.pop();
			console.log($scope.attributes);
			// var lastItem = $scope.attributes.length-1;
			// $scope.attributes.length-1;
			// $scope.attributes.splice("{\"key\" : \" " + $scope.attribute.value + "\"");
		};

	}

]);