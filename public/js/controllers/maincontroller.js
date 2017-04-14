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
		$scope.equipmentTypeList = [];
		$scope.formObj = {};

		$http.get('http://localhost:8080/v1/equipmenttypes').then(function(response) {

			$scope.equipmenttypes = response.data.equipment_types;
			var count = 0;
			angular.forEach($scope.equipmenttypes, function(item) {
				// $scope.equipmentTypeList.push(item['name']);
				temp = {};
				temp['name'] = item['name'];
				$scope.equipmentTypeList.push(temp);
			});
		});

		$scope.labels = ['department_tag', "gt_tag", "status", "comment"];

		$scope.submitEquipmentCreation = function() {
			// console.log($scope.attrList.attributes);
			// console.log($scope.formObj);
			var returnThis = angular.extend($scope.formObj, $scope.attrList);
			console.log(angular.toJson(returnThis, true));

			$http.post('http://localhost:8080/v1/equipments', returnThis).then(function(data, status, headers, config) {
				alert(data.msg);
			});


		}
		
 		// $scope.attributes = [];
		$scope.attrList = {

			"attributes": [
				{
					"key": "",
					"value": ""
				}
			]
		}

		$scope.addNewAttribute = function(index) {
			var newAttr = {"key": "", "value": ""};

			// if($scope.attrList.attributes.length <= index + 1) {
				$scope.attrList.attributes.splice(index+1,0,newAttr);
			// }
			
		};

		$scope.removeAttribute = function($event, key) {
			var index = $scope.attrList.attributes.indexOf(key);
			if($event.which == 1) {
				$scope.attrList.attributes.splice(index,1);
			}
		};

	}

]);