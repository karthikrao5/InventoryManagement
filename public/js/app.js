var app = angular.module("app", ['ngRoute', 'app.controllers', 'AuthService'])
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

angular.module("app").factory("Auth", ["$http", "$window",
    function($http, $window) {

        return {

            authorize = function() {
                var body = {"isHook": true, "hook_name" : "front-endAngular"};

                $http.post('http://localhost:8080/v1/auth', JSON.stringify(body)).then(function(response) {
                    console.log("Response from /auth" + response.data.jwt);
                    $window.localStorage.setItem("jwt", response.data.jwt);
                    console.log("New jwt: " + $window.localStorage.getItem("jwt"));
                });
            }

            // isAuth = function(token) {
            //     function parseJWT(token) {
            //         var base64Url = token.split('.')[1];
            //         var base64 = base64Url.replace('-', '+').replace('_', '/');
            //         return JSON.parse(window.atob(base64));
            //     }

            //     var expirationEpoch = parseJWT(token["exp"]);

            //     var currTime = new Date().getTime() / 1000;

            //     if (expirationEpoch < currTime) {
            //         console.log("Current time: " + currTime + ". Expired token " + expirationEpoch + ". Redirecting to auth.");
            //         this.authorize;
            //     }
            //     return jwt;
            // }
        };
    }
]);

app.factory('authInterceptor', ["$window", "$q", "$location",
    function ($window, $q, $location, Auth) {
        return {
            // adds an Auth Bearer header to all requests
            request: function (config) {
                console.log("JWT currently: " + $window.localStorage.getItem("jwt"));
                config.headers = config.headers || {};

                if($window,localStorage.getItem("jwt")) {
                    Auth.isAuth();
                    config.headers.Authorization = 'Bearer ' + $window.localStorage.getItem("jwt");
                }

                return config;
            },

            responseError: function(response) {
                if (response.status === 401 || response.status === 403) {
                    //  Redirect user to login page / signup Page.
                    console.log("unauthorized");    
                    Auth.authorize;
                }
                return $q.reject(response);
            }
        };
    }
]);

