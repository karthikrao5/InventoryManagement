var ctrl = angular.module('app.controllers', []);

ctrl.controller('EquipmentsController', ["$scope", "$http", "$location", "$window", "Auth", 
	function($scope, $http, $location, $window, Auth) {

		// if token in localstorage is expired, redirect to auth
		if (Auth.isExpired()) {
			$location.path("/auth");
		}

		var endpoint = 'http://localhost:8080/v1/equipments';
		
	    $http.get(endpoint).then(function (response) {
	        $scope.equipments = response.data.equipments;
	        console.log(angular.toJson(response.data.equipments, true));
	    });

	    $scope.go = function(route) {
			$location.path( route );
		};
	}
]);
ctrl.controller("NewEquipmentController", ["$scope", "$http", "$location", 
	function($scope, $http, $location) {

		$scope.equipmentTypeList = [];
		$scope.formObj = {};
		$scope.defaultEquipmentTypes = {};
		$scope.currEqType;

		$http.get('http://localhost:8080/v1/equipmenttypes').then(function(response) {

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

		});

		$scope.labels = ['department_tag', "gt_tag", "status", "comment", "loaned_to"];

		$scope.submitEquipmentCreation = function() {
			// console.log($scope.attrList.attributes);
			// console.log($scope.formObj);
			var returnThis = angular.extend($scope.formObj, $scope.attrList);
			console.log(angular.toJson(returnThis, true));

			$http.post('http://localhost:8080/v1/equipments', returnThis).then(function(data, status, headers, config) {
				alert(data.msg);
			});


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

		$scope.go = function(route) {
			$location.path( route );
		};

		$scope.updateEqType = function() {
			$scope.attrList.attributes = [];
			var currentSelectedType = $scope.formObj.equipment_type_name;
			var something = $scope.defaultEquipmentTypes[currentSelectedType];

			for(var i = 0; i < something.length; i++) {
				val = {};
				val["key"] = something[i];
				val["value"] = "";
				$scope.attrList.attributes.push(val);
			}
		}

	}
]);


ctrl.controller("HomeController", ["$scope", "$location", 
	function($location, $scope) {
		$scope.go = function(route) {
			$location.path( route );
		};
	}
]);

ctrl.controller("AuthController", ["$http", "$location", "$scope", "$window", "Auth",
	function($http, $location, $scope, $window, Auth) {

		function successAuth(response) {
			$window.localStorage.setItem("jwt", response.data.jwt);
			console.log($window.localStorage.getItem("jwt"));
			// window.location = "/";
		}

		var body = {"isHook": true, "hook_name" : "front-endAngular"};
		Auth.authorize(JSON.stringify(body), successAuth, function() {
			console.log("Some auth error");
		});
	}
]);


