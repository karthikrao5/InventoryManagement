angular.module("app").factory("Auth", ["$window", "$http", function($window, $http) {
    
    function parseJWT(token) {
        if(!token) {
            console.log("no token");
        }
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse(window.atob(base64));
    }

    function isExpired() {
        // if($window.localStorage.getItem("jwt")) {
        //     console.log("Token not found in localStorage");
        //     return true;
        // }
        var expirationEpoch = parseJWT($window.localStorage.jwt)["exp"];

        var currTime = new Date().getTime() / 1000;

        if (expirationEpoch < currTime) {
            console.log("Expired...Current time: " + currTime + ". Expired token " + expirationEpoch);
            return true;
        }
        return false;
    }

    function isAdminOrHook() {
        if(isExpired()) {
            return false;
        }
        var admin = parseJWT($window.localStorage.getItem("jwt"))["data"]["isAdmin"];

        if (admin) {
            console.log("User is an admin.");
            return true;
        } else {
            return false;
        }
    }

    function getUser() {
        // if($window.localStorage.getItem("jwt") === null) {
        //     console.log("Token not found in localStorage");
        //     return false;
        // } else {
        //     console.log("Token from getuser function: " + $window.localStorage.getItem("jwt"));
        // }
        return parseJWT($window.localStorage.getItem("jwt"))["data"]["username"];
    }

    function deleteToken() {
        if(!$window.localStorage.getItem("jwt") === null) {
            $window.localStorage.$reset();
            return true;
        }
        return false;
    }    

    return {
        authorize : function(data, success, error) {
            // console.log("placeholder");
            $http.post('http://localhost:8080/v1/auth', data).then(success).catch(error)
        },

        isExpired : function() {
            return isExpired();
        },

        isAdminOrHook : function() {
            return isAdminOrHook();
        },

        deleteAuth : function() {
            $window.localStorage.removeItem("jwt");
        },
        getUser : function() {
            return getUser();
        },
        deleteToken: function() {
            return deleteToken();
        }
    };

}]);
