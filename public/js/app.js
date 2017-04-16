var app = angular.module("app", ['ngRoute', 'app.controllers'])
.config(function($routeProvider, $httpProvider){
    $httpProvider.interceptors.push("authInterceptor");
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
    });

    // .when('/equipmenttypes', {
    // 	templateUrl: 'templates/equipmenttypes-list.html',
    // 	controller: 'EquipmentTypesController'
    // });


    $routeProvider.otherwise({redirectTo : '/'}); 

    
});

app.factory('authInterceptor', ["$window", "$q", "$location",
    function ($window, $q, $location) {
        return {
            request: function (config) {
                config.headers = config.headers || {};

                if($window,localStorage.getItem("jwt")) {
                    function parseJWT(token) {
                        var base64Url = token.split('.')[1];
                        var base64 = base64Url.replace('-', '+').replace('_', '/');
                        return JSON.parse(window.atob(base64));
                    }

                    var expirationEpoch = parseJWT($window.localStorage.jwt)["exp"];

                    var currTime = new Date().getTime() / 1000;

                    if (expirationEpoch < currTime) {
                        console.log("Current time: " + currTime + ". Expired token " + expirationEpoch + ". Redirecting to auth.");
                        $location.path("/auth");
                    }
                    config.headers.Authorization = 'Bearer ' + $window.localStorage.getItem("jwt");
                }
                return config;
            },

            responseError: function(response) {
                if (response.status === 401 || response.status === 403) {
                    //  Redirect user to login page / signup Page.
                    console.log("unauthorized");    
                    $location.path('/auth');
                }
                return $q.reject(response);
            }
        };
    }
]);

