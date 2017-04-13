var app = angular.module("app", ['ngRoute', 'app.controllers'])
.config(function($routeProvider){

    $routeProvider

    .when('/equipments', {
        templateUrl: 'templates/equipments-list.html',
        controller: 'EquipmentsController'
    })
    .when('/equipments/new', {
        templateUrl: 'templates/create-equipment.html',
        controller: 'NewEquipmentController'
    });
    
    // .when('/equipmenttypes', {
    // 	templateUrl: 'templates/equipmenttypes-list.html',
    // 	controller: 'EquipmentTypesController'
    // });


    $routeProvider.otherwise({redirectTo : '/equipments'}); 
});

