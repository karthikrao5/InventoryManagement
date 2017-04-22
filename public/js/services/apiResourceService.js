angular.module("app").factory("APIService", ["$http", "$httpParamSerializerJQLike",
	function($http,$httpParamSerializerJQLike, $routeParams) {
		var apiURL = "v1/";

		return {

			// search with params
			get : function(resource, inputParams, success, error) {
				$http.get(apiURL+resource, {params: $httpParamSerializerJQLike(inputParams)}).then(success).catch(error)
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