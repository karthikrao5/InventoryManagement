angular.module("app.controllers").controller('NewEquipmentTypeController', ["$scope", "$location", "APIService", "uiGridConstants",
	function($scope, $location, APIService) {

		$scope.returnObject = {};

		$scope.title = "Create Equipment Type";
		$scope.labels = ['name', 'comments'];

		$scope.booleans = ['required', 'unique', 'enum'];

		$scope.returnObject["equipment_type_attributes"] = [];

		$scope.addNewAttribute = function(index) {
			var newAttr = {"name": "", "data_type": "", "regex": null, "help_comment": "", "required": false, "unique": false, "enum": false, "enum_values": []};
			// if($scope.attrList.attributes.length <= index + 1) {
			// $scope.returnObject.equipment_type_attributes.splice(index+1,0,newAttr);
				// console.log($scope.formObj);
			// }
			$scope.returnObject.equipment_type_attributes.push(newAttr);		
		};

		$scope.removeAttribute = function($event, key) {
			var index = $scope.returnObject.equipment_type_attributes.indexOf(key);
			if($event.which == 1) {
				$scope.returnObject.equipment_type_attributes.splice(index,1);
			}
		};

		$scope.initEnumVals = function(enumVal, newAttr) {
			var index = $scope.returnObject.equipment_type_attributes.indexOf(newAttr);
			// if checked, initialize enumvals with ""
			if(enumVal) {
				$scope.returnObject.equipment_type_attributes[index].enum_values = [""];
			} else {
				$scope.returnObject.equipment_type_attributes[index].enum_values = [];
			}
		};


		$scope.addNewEnum = function(attribute) {
			var attributeIndex = $scope.returnObject.equipment_type_attributes.indexOf(attribute);
			console.log(attributeIndex);
			$scope.returnObject.equipment_type_attributes[attributeIndex].enum_values.push("");
		};

		$scope.deleteEnum = function($event, key, index) {
			var value = $scope.returnObject.equipment_type_attributes[index].enum_values.indexOf(key);
			if($event.which == 1) {
				$scope.returnObject.equipment_type_attributes[index].enum_values.splice(value,1);
			}
		};

		$scope.submitEquipmentTypeCreation = function() {
			var postMe = angular.toJson($scope.returnObject, true);
			console.log(postMe);
			APIService.post("equipmenttypes", postMe, function(response) {
				alert("Successfully created equipment type!");
				console.log(response.data);
				$location.path('/equipmenttypes');
			}, function(error) {
				alert(error.data);
			});
		};
	}

]);
