
angular.module("app").factory('authInterceptor', ["$window", "$q", "$location",
    function ($window, $q, $location) {
        // return {
        //     // adds an Auth Bearer header to all requests
        //     request: function (config) {
        //         config.headers = config.headers || {};
        //         if ($window.localStorage.getItem("jwt")) {
        //            config.headers.Authorization = 'Bearer ' + $window.localStorage.getItem("jwt");
        //         } else {
        //             console.log("no token in local storage for auth.");
        //         }
        //         return config;
        //     },

        //     responseError: function(response) {
        //         if (response.status === 401 || response.status === 403) {
        //             //  Redirect user to login page / signup Page.
        //             console.log("unauthorized");
        //             $location.path("/auth");
        //         }
        //         return $q.reject(response);
        //     }
        // };
    }
]);