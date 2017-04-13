// angular.module("app.controllers").controller('EquipmentsController', ["$scope", "$http",
// 	function($scope, $http) {
// 		var endpoint = 'http://localhost:8080/v1/equipments';

// 	    $http.get(endpoint).then(function (response) {
// 	        $scope.equipments = response.data.equipments;
// 	    });

// 	    $http.get('http://localhost:8080/v1/equipmenttypes').then(function(response) {
// 			$scope.equipmentTypeList = response.data.equipment_types;
// 		});

// 	}

		

// 	// $scope.labels = ['Department Tag', "GT Tag", "Status", "Comment"];

// 	// $scope.attributes = [];

// 	// $scope.addNewAttribute = function() {
// 	// 	$scope.attributes.length+1;
// 	// 	$scope.attributes.push("{\"key\" : \" " + $scope.attribute.value + "\"}");
// 	// 	// var newItemNo = $scope.attributes.length+1;
// 	// 	// $scope.choices.push({'id':'choice'+newItemNo});
// 	// };

// 	// $scope.removeAttribute = function() {
// 	// 	// var lastItem = $scope.attributes.length-1;
// 	// 	$scope.attributes.length-1;
// 	// 	$scope.attributes.splice("{\"key\" : \" " + $scope.attribute.value + "\"");
// 	// };

// ]);