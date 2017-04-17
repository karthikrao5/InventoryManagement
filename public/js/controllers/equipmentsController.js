angular.module("app.controllers").controller('EquipmentsController', ["$scope", "$http", "$location", "$window", "Auth", "uiGridConstants", 
	function($scope, $http, $location, $window, Auth) {

		// if token in localstorage is expired, redirect to auth
		if (Auth.isExpired()) {
			$location.path("/auth");
		}
		var endpoint = 'http://localhost:8080/v1/equipments';
		



	    $scope.columns = [{field: "department_tag", enableHiiding: false},
	    				  {field: "gt_tag", enableHiiding: false},
	    				  {field: "status", enableHiiding: false},
	    				  {field: "loaned_to", enableHiiding: false},
	    				  {field: "equipment_type_name", enableHiiding: false},
	    				  {field: "attributes"},
	    				  {field: "created_on", enableHiiding: false},
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


	    $http.get(endpoint).then(function (response) {
	    	$scope.gridOptions.data = response.data.equipments;
	        // $scope.equipments = response.data.equipments;
	        // console.log(angular.toJson(response.data.equipments, true));
	    });

	    $scope.go = function(route) {
			$location.path( route );
		};
	}
]);