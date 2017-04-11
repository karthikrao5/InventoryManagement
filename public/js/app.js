var app = angular.module("app", ['ngRoute', 'app.controllers']).config(function($routeProvider){

    $routeProvider.

    when('/equipments', {
        templateUrl: 'templates/equipments-list.html',
        controller: 'EquipmentsController'
    }).
    when('/equipmenttypes', {
    	templateUrl: 'templates/equipmenttypes-list.html',
    	controller: 'EquipmentTypesController'
    });

    $routeProvider.otherwise({redirectTo : '/equipments'}); 
});

// app.controller('EquipmentsController', function ($scope, $http){  
//     var endpoint = 'http://localhost:8080/v1/equipments';
//     // console.log(endpoint);
//     $http.get(endpoint).then(function (response) {
//         $scope.equipments = response.data.equipments;
//     });
// });