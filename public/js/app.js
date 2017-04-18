var app = angular.module("app", ["ngRoute", "ngResource", "app.controllers", "ui.grid", "app.services"])
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
        .when('/equipments/:id', {
            templateUrl: 'templates/edit-equipment.html',
            controller: 'EditEquipmentController'
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

angular.module("app").constant("APIURL", "https://localhost:8080/v1/");
