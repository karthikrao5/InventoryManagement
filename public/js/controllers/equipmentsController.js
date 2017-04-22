angular.module("app.controllers").controller('EquipmentsController', ["$scope", "$http", "$location", "$window","APIService", "uiGridConstants",
	function($scope, $http, $location, $window, APIService) {

		function onSuccess(response) {
	    	$scope.gridOptions.data = response.data.equipments;
	    }

	    APIService.query("equipments", onSuccess, function() {
	    	console.log("Error with getting all equipments");
	    });

		// if token in localstorage is expired, redirect to auth
		// if (Auth.isExpired()) {
		// 	$location.path("/auth");
		// }
		// var endpoint = 'http://localhost:8080/v1/equipments';


	    $scope.columns = [{field: "department_tag", enableHiding: false},
	    				  {field: "gt_tag", enableHiding: false},
	    				  {field: "status", enableHiding: false},
	    				  {field: "loaned_to", enableHiding: false},
	    				  {field: "equipment_type_name", enableHiding: false},
	    				  {field: "attributes"},
	    				  {field: "created_on", enableHiding: false}
	    			];

	    $scope.gridOptions = {
	    	enableSorting: true,
	    	columnDefs: $scope.columns,
	    	enableGridMenu: true
	    };

	 //    $scope.remove = function() {
		// 	$scope.columns.splice($scope.columns.length-1, 1);
		// }

		// $scope.add = function() {
		// 	$scope.columns.push({ field: 'company', enableSorting: false });
		// }

	    // $http.get(endpoint).then(function(response) {
	    // 	$scope.gridOptions.data = response.data.equipments;
	    // 	// $scope.equipments = response.data.equipments;
	    //     // console.log(angular.toJson(response.data.equipments, true));
	    // });






	    $scope.go = function(route) {
			$location.path( route );
		};
	}
]);
