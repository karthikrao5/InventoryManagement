
angular.module("app").factory('authInterceptor', ["$window", "$q", "$location",
    function ($window, $q, $location) {
        return {
            // adds an Auth Bearer header to all requests
            request: function (config) {
                config.headers = config.headers || {};
                if ($window.localStorage.getItem("jwt")) {
                   config.headers.Authorization = 'Bearer ' + $window.localStorage.getItem("jwt");
                } else {
                    console.log("no token in local storage for auth.");
                }
                return config;
            },

            responseError: function(response) {
                console.log("checking response error");
                switch (response.status) {
                    case 403:
                        console.log("Intercepted 403, redirecting to error page.");
                        $location.path('/403');
                        break;
                    case 401:
                        console.log("Intercepted 401, redirecting to error page.");
                        $location.path('/401');
                        break;
                    case 500:
                        $location.path("/500");
                        break;
                    case -1:
                        console.log("Intercepted: " + response.status);
                        // $location.path('/auth');
                }
                return $q.reject(response);
            }
        };
    }
]);