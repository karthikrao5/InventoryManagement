angular.module("app").factory("APIService", ["$http", "$httpParamSerializer",
	function($http,$httpParamSerializer, $routeParams) {
		var apiURL = "v1/";

		var config = {};

		return {

			// search with params
			get : function(resource, inputParams, success, error) {
				console.log({params: $httpParamSerializer(inputParams)});
				config["params"] = inputParams;
				$http.get(apiURL+resource+"?", config).then(success).catch(error)
			},

			post : function(resource, data, success, error) {
				console.log(apiURL+resource);
				$http.post(apiURL+resource, data).then(success).catch(error)
			},

			// get all items
			query : function(resource, success, error) {
				// console.log("here");
				$http.get(apiURL + resource).then(success).catch(error)
			},

			delete : function(resource, params, success, error) {
				$http.delete(apiURL+resource, params).then(success).catch(error)
			},

			put : function(resource, data, params, success, error) {
				$http.put(apiURL+resource).then(success).catch(error)
			}
		};
	}
]);