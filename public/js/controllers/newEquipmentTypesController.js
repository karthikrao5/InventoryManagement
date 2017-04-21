angular.module("app.controllers").controller('NewEquipmentTypeController', ["$scope","APIService", "uiGridConstants",
	function($scope, APIService) {

		$scope.formObj = {};

		$scope.title = "Create Equipment Type";
		$scope.labels = ['name', 'comments'];

		$scope.booleans = ['required', 'unique', 'enum'];

		$scope.attrList = {};
		$scope.attrList["equipment_type_attributes"] = [];

		$scope.addNewAttribute = function(index) {
			var newAttr = {"name": "", "data_type": "", "regex": null, "help_comment": "", "required": false, "unique": false, "enum": false, "enum_values": []};
			// if($scope.attrList.attributes.length <= index + 1) {
			$scope.attrList.equipment_type_attributes.splice(index+1,0,newAttr);
				// console.log($scope.formObj);
			// }
			
		};

		$scope.removeAttribute = function($event, key) {
			var index = $scope.attrList.equipment_type_attributes.indexOf(key);
			if($event.which == 1) {
				$scope.attrList.equipment_type_attributes.splice(index,1);
			}
		};



		$scope.addNewEnum = function(attribute) {
			var attributeIndex = $scope.attrList.equipment_type_attributes.indexOf(attribute);
			console.log(attributeIndex);
			$scope.attrList.equipment_type_attributes[attributeIndex].enum_values.push("");
		};

		$scope.deleteEnum = function($event, key) {
			var value = $scope.attrList.equipment_type_attributes[index].enum_values.indexOf(key);
			$scope.attrList.equipment_type_attributes[index].enum_values.splice(value,1);
		};

		$scope.submitEquipmentTypeCreation = function() {
			var returnAttr = angular.extend($scope.formObj, $scope.attrList);
			var returnEnum = angular.extend($scope.formObj, $scope.enumVals);

			var postMe = angular.toJson($scope.formObj, true);
			console.log(postMe);
			APIService.post("equipmenttypes", postMe, function(response) {
				alert("Successfully created equipment type!");
			}, function(error) {
				alert(error[data]);
			});
		};
	}

]);
