angular.module("app.controllers").controller("NewEquipmentController", ["$scope", "$http", "$location", "APIService",
	function($scope, $http, $location, APIService) {

		$scope.equipmentTypeList = [];
		$scope.formObj = {};
		$scope.defaultEquipmentTypes = {};
		$scope.currEqType;

		APIService.query("equipmenttypes", onEquipmentTypeSuccess, function() {
			console.log("error with getting equipmenttypes");
		});

		function onEquipmentTypeSuccess(response) {
			$scope.equipmenttypes = response.data.equipment_types;

			angular.forEach($scope.equipmenttypes, function(item) {
				// $scope.equipmentTypeList.push(item['name']);
				temp = {};
				temp['name'] = item['name'];
				$scope.equipmentTypeList.push(temp);

				//===============================
				// create default attribute list to populate keys
				$scope.defaultEquipmentTypes[item["name"]] = []
				angular.forEach(item.equipment_type_attributes, function(attr) {
					$scope.defaultEquipmentTypes[item["name"]].push(attr["name"]);
				});

			});
			// console.log($scope.defaultEquipmentTypes);
		}


		// $http.get('http://localhost:8080/v1/equipmenttypes').then(function(response) {

		// });

		$scope.labels = ['department_tag', "gt_tag", "comment"];

		$scope.submitEquipmentCreation = function() {
			// console.log($scope.attrList.attributes);
			// console.log($scope.formObj);
			var returnThis = angular.extend($scope.formObj, $scope.attrList);
			console.log(angular.toJson(returnThis, true));

			function onSuccess(response) {
				alert("Successfully created Equipment!");
				console.log(response.data);
				$location.path('/');
			}

			APIService.post("equipments", returnThis, onSuccess, function(error) {
				alert(error);
			});
			// $http.post('http://localhost:8080/v1/equipments', returnThis).then(function(data, status, headers, config) {
			// 	alert(data.msg);
			// });




		};

		// $scope.attrList = {
		// 	"attributes": [
		// 		{
		// 			"key": "",
		// 			"value": ""
		// 		}
		// 	]
		// }

		$scope.attrList = {};
		$scope.attrList["attributes"] = [];

		$scope.addNewAttribute = function(index) {
			var newAttr = {"name": "", "value": ""};
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

		$scope.go = function(route) {
			$location.path( route );
		};

		$scope.updateEqType = function() {
			$scope.attrList.attributes = [];
			var currentSelectedType = $scope.formObj.equipment_type_name;
			var something = $scope.defaultEquipmentTypes[currentSelectedType];

			for(var i = 0; i < something.length; i++) {
				val = {};
				val["name"] = something[i];
				val["value"] = "";
				$scope.attrList.attributes.push(val);
			}
		}

	}
]);
