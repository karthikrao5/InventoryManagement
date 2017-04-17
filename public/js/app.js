var app = angular.module("app", ['ngRoute', 'app.controllers'])
.config(function($routeProvider, $httpProvider){

    $routeProvider
        .when('/equipments', {
            templateUrl: 'templates/equipments-list.html',
            controller: 'EquipmentsController'
        })
        .when('/equipments/new', {
            templateUrl: 'templates/create-equipment.html',
            controller: 'NewEquipmentController'
        })
        .when('/', {
            templateUrl: 'templates/home.html',
            controller: "HomeController"
        })
        .when('/auth', {
            templateUrl: 'templates/auth.html',
            controller: "AuthController"
        })
        .otherwise({redirectTo: '/'});

    // .when('/equipmenttypes', {
    // 	templateUrl: 'templates/equipmenttypes-list.html',
    // 	controller: 'EquipmentTypesController'
    // });

    $httpProvider.interceptors.push("authInterceptor");
    
});
