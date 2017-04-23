angular.module("app.controllers").controller("EditEquipmentTypeController", ["$scope", "$routeParams","$location", "APIService",
	function($scope, $routeParams, $location, APIService) {

		$scope.buttonToggle = false;
		$scope.returnObject = {};

		$scope.originalItem;

		$scope.returnObject["update_equipment_type"] = {};

		$scope.returnObject["update_equipment_type_attributes"] = [];

		$scope.returnObject["add_equipment_type_attributes"] = [];

		$scope.returnObject["remove_equipment_type_attributes"] = [];

		$scope.submitEquipmentTypeEdit = function() {
			var jsonBody = angular.toJson($scope.returnObject, 1);
			console.log(jsonBody);
			APIService.put("equipmenttypes", jsonBody, function(response){
				console.log(response.data);
				alert(response.data);
				$location.path('/equipmenttypes');
			}, function(error) {
				console.log(error.data);
				alert(error.data);
			});
		};

		$scope.addNewAttributeField = function(index) {
			var newAttr = {"name": "", "data_type": "", "regex": null, "help_comment": "", "required": false, "unique": false, "enum": false, "enum_values": []};
			// if($scope.attrList.attributes.length <= index + 1) {
			$scope.returnObject.add_equipment_type_attributes.splice(index+1,0,newAttr);
				// console.log($scope.formObj);
			// }
		};

		$scope.removeNewAttributeField = function($event, key) {
			var index = $scope.returnObject.add_equipment_type_attributes.indexOf(key);
			if($event.which == 1) {
				$scope.returnObject.add_equipment_type_attributes.splice(index,1);
			}
		};

		$scope.addUpdateEnum = function(attribute) {
			var attributeIndex = $scope.returnObject.update_equipment_attributes.indexOf(attribute);
			console.log(attributeIndex);
			$scope.returnObject.update_equipment_attributes[attributeIndex].enum_values.push("");
		};

		$scope.deleteUpdateEnum = function($event, key, index) {
			var value = $scope.returnObject.update_equipment_attributes[index].enum_values.indexOf(key);
			if($event.which == 1) {
				$scope.returnObject.update_equipment_attributes[index].enum_values.splice(value,1);
			}
		};

		$scope.addNewEnum = function(attribute) {
			var attributeIndex = $scope.returnObject.add_equipment_type_attributes.indexOf(attribute);
			console.log(attributeIndex);
			$scope.returnObject.add_equipment_type_attributes[attributeIndex].enum_values.push("");
		};

		$scope.deleteEnum = function($event, key, index) {
			var value = $scope.returnObject.add_equipment_type_attributes[index].enum_values.indexOf(key);
			if($event.which == 1) {
				$scope.returnObject.add_equipment_type_attributes[index].enum_values.splice(value,1);
			}
		};

		$scope.removeAttribute = function(attrToRemove) {
			// if not in remove list, add it, otherwise remove it
			if ($scope.returnObject.remove_equipment_type_attributes.indexOf(attrToRemove["_id"]) === -1) {
				$scope.returnObject.remove_equipment_type_attributes.push(attrToRemove["_id"]);
			} else {
				var index = $scope.returnObject.remove_equipment_type_attributes.indexOf(attrToRemove["_id"]);
				$scope.returnObject.remove_equipment_type_attributes.splice(index, 1);
			}
		};

		$scope.initEnumVals = function(enumVal, newAttr) {
			// var index = $scope.returnObject.add_equipment_type_attributes.indexOf(newAttr);
			// if checked, initialize enumvals with ""
			if(enumVal) {
				newAttr.enum_values = [""];
				// $scope.returnObject.add_equipment_type_attributes[index].enum_values = [""];
			} else {
				newAttr.enum_values = [];
				// $scope.returnObject.add_equipment_type_attributes[index].enum_values = [];
			}
		};

		$scope.initEnumValsUpdate = function(attr) {
			// var index = $scope.returnObject.update_equipment_attributes.indexOf(newAttr);
			// if checked, initialize enumvals with ""
			var enumVal = attr.enum;
			if(enumVal) {
				attr.enum_values = [""];
				// $scope.returnObject.update_equipment_attributes[index].enum_values = [""];
			} else {
				attr.enum_values = [];
				// $scope.returnObject.update_equipment_attributes[index].enum_values = [];
			}
		};

		$scope.addIdToUpdateAttr = function(update_attr, originalAttr) {
			if(!update_attr["_id"]) {
				update_attr["_id"] = originalAttr["_id"]["$id"];
			}
		};




		$scope.paramsArray = {};
		$scope.paramsArray["name"] = $routeParams.name;
		APIService.get("equipmenttypes", $scope.paramsArray, function(response) {
			$scope.originalItem = response.data.equipment_types[0];
			$scope.returnObject["_id"] = $scope.originalItem["_id"]["$id"];
			$scope.returnObject["name"] = $scope.originalItem["name"];
			angular.forEach($scope.originalItem.equipment_type_attributes, function(originalAttr) {
				var temp = {};
				temp["_id"] = originalAttr["_id"]["$id"];
				temp["name"] = originalAttr["name"];
				// $scope.returnObject.update_equipment_type_attributes.push(temp);
			});

			$scope.returnObject.update_equipment_type.name = $scope.originalItem.name;
			$scope.returnObject.update_equipment_type.comments = $scope.originalItem.comments;
		}, function(error) {
			alert(error.data);
			console.log(error.data);
		});
	}
]);