angular.module("app").factory("Auth", ["$window", "$http", function($window, $http) {
    
    function parseJWT(token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse(window.atob(base64));
    }

    function isExpired() {
        var expirationEpoch = parseJWT($window.localStorage.jwt)["exp"];

        var currTime = new Date().getTime() / 1000;

        if (expirationEpoch < currTime) {
            console.log("Expired...Current time: " + currTime + ". Expired token " + expirationEpoch);
            return true;
        }
        return false;
    }

    

    return {
        authorize : function(data, success, error) {
            $http.post('http://localhost:8080/v1/auth', data).then(success).error(error);
        },

        isExpired : function() {
            return isExpired();
        }
    };

}]);
