var ctrl = angular.module('app.controllers', []);

ctrl.controller('EquipmentsController', ["$scope", "$http", "$location", "$window", 
	function($scope, $http, $location, $window) {

		var endpoint = 'http://localhost:8080/v1/equipments';
		// $scope.token = $window.localStorage.jwt;
		function parseJWT(token) {
			var base64Url = token.split('.')[1];
            var base64 = base64Url.replace('-', '+').replace('_', '/');
            return JSON.parse(window.atob(base64));
		}

		$scope.token = parseJWT($window.localStorage.jwt)["exp"];

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

		$scope.labels = ['department_tag', "gt_tag", "status", "comment", "loaned_to"];

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
		$scope.attrList = {"attributes": [{"key": "","value": ""}]}

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

	}
]);


ctrl.controller("HomeController", ["$http", "$scope", "$location", 
	function($http, $location, $scope) {
		$scope.go = function(route) {
			$location.path( route );
		};
	}
]);

ctrl.controller("AuthController", ["$http", "$location", "$scope", "$window",
	function($http, $location, $scope, $window) {
		$scope.authToken;

		var body = {"isHook": true, "hook_name" : "front-endAngular"};

		$http.post('http://localhost:8080/v1/auth', JSON.stringify(body)).then(function(response) {
			console.log("Response from /auth" + response.data.jwt);
			$scope.authToken = response.data.jwt;
			$window.localStorage.setItem("jwt", response.data.jwt);
			console.log("New jwt: " + $window.localStorage.getItem("jwt"));
		});

		if ($window.localStorage.getItem("jwt") == $scope.authToken) {
			console.log("redirectign to equipments");
			$location.path('/equipments');
		} else {
			alert("No auth token");
		}
	}
]);


