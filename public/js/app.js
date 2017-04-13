var app = angular.module("app", ['ngRoute', 'app.controllers'])
.config(function($routeProvider){

    $routeProvider.

    when('/equipments', {
        templateUrl: 'templates/equipments-list.html',
        controller: 'EquipmentsController'
    }).
    when('/equipmenttypes', {
    	templateUrl: 'templates/equipmenttypes-list.html',
    	controller: 'EquipmentTypesController'
    });

    $routeProvider.otherwise({redirectTo : '/me'}); 
});


$httpProvider.interceptors.push(['$q', '$location', '$localStorage', function ($q, $location, $localStorage) {
    return {
        'request': function (config) {
            config.headers = config.headers || {};
            if ($localStorage.token) {
               config.headers.Authorization = 'Bearer ' + $localStorage.token;
            }
            return config;
        },
        'responseError': function (response) {
            if (response.status === 401 || response.status === 403) {
                $location.path('/v1/auth');
            }
            return $q.reject(response);
        }
    };
}]);