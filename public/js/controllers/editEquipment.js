angular.module("app.controllers").controller("EditEquipmentController", ["$scope", "$http", "$location", "$routeParams", "APIService", "$httpParamSerializerJQLike",
	function($scope, $http,$location,$routeParams, APIService) {

		$scope.labels = ['department_tag', "gt_tag", "comment"];
		$scope.buttonToggle = false;
		$scope.returnObject = {};
		// // new user to update to
		// $scope.newUser;

		// current equipment being modified
		$scope.originalItem;

		// list of all users
		// $scope.users;


		// field for PUT
		// $scope.update_equipment = {};
		$scope.returnObject["update_equipment"] = {};

		// field for PUT
		// $scope.add_equipment_attributes = [];
		$scope.returnObject["add_equipment_attributes"] = [];

		// field for PUT
		// $scope.update_equipment_attributes = [];
		$scope.returnObject["update_equipment_attributes"] = [];

		// field for PUT
		// $scope.remove_equipment_attributes = [];
		$scope.returnObject["remove_equipment_attributes"] = [];


		// $scope.addNewAttributeField = function(index) {
		// 	var newAttr = {"name": "", "value": ""};
		// 	// if($scope.attrList.attributes.length <= index + 1) {
		// 	$scope.returnObject.add_equipment_attributes.splice(index+1,0,newAttr);
		// 		// console.log($scope.formObj);
		// 	// }
		// };

		// $scope.removeNewAttributeField = function($event, key) {
		// 	var index = $scope.returnObject.add_equipment_attributes.indexOf(key);
		// 	if($event.which == 1) {
		// 		$scope.returnObject.add_equipment_attributes.splice(index,1);
		// 	}
		// };

		$scope.removeAttribute = function(attrToRemove) {
			// if not in remove list, add it, otherwise remove it
			if ($scope.returnObject.remove_equipment_attributes.indexOf(attrToRemove["_id"]) === -1) {
				$scope.returnObject.remove_equipment_attributes.push(attrToRemove["_id"]);
			} else {
				var index = $scope.returnObject.remove_equipment_attributes.indexOf(attrToRemove["_id"]);
				$scope.returnObject.remove_equipment_attributes.splice(index, 1);
			}
			
		};

		$scope.submitEquipmentEdit = function() {
			console.log($scope.returnObject.update_equipment);
			console.log(angular.toJson($scope.returnObject, 1));
		};

		console.log($routeParams.departmenttag);
		$scope.paramsArray = {};
		$scope.paramsArray["department_tag"] = $routeParams.departmenttag;
		console.log($scope.paramsArray);

		APIService.get('equipments', $scope.paramsArray, function(response) {
			$scope.originalItem = response.data.equipments[0];
			angular.forEach($scope.originalItem.attributes, function(originalAttr) {
				var temp = {};
				temp["_id"] = originalAttr["_id"]["$id"];
				temp["name"] = originalAttr["name"];
				temp["value"] = originalAttr["value"];
				$scope.returnObject.update_equipment_attributes.push(temp);
			});
			$scope.returnObject.update_equipment.department_tag = $scope.originalItem.department_tag;
			$scope.returnObject.update_equipment.loaned_to = $scope.originalItem.loaned_to;
			$scope.returnObject.update_equipment.comments = $scope.originalItem.comments;
		}, function(error) {
			console.log(error.data);
		});


		// APIService.query('users', function(response) {
		// 	$scope.users = response.data.users;
		// });
	}

]);