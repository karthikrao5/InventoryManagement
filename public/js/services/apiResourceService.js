angular.module("app").factory("APIService", ["$http", "$httpParamSerializer",
	function($http,$httpParamSerializer, $routeParams) {
		var apiURL = "v1/";

		var config = {};

		return {

			// search with params
			get : function(resource, inputParams, success, error) {
				$http({
					method: 'GET',
					url: apiURL+resource+"?",
					params: inputParams,
					headers: {
						"Content-type": "application/json"
					}
				}).then(success, error)

				// config["params"] = inputParams;
				// $http.get(apiURL+resource+"?", config).then(success).catch(error)
			},

			post : function(resource, data, success, error) {
				$http({
					method: 'POST',
					url: apiURL+resource,
					data: data,
					headers: {
						"Content-type": "application/json"
					}
				}).then(success, error)

				// console.log(apiURL+resource);
				// $http.post(apiURL+resource, data).then(success).catch(error)
			},

			// get all items
			query : function(resource, success, error) {
				$http({
					method: 'GET',
					url: apiURL+resource,
					headers: {
						"Content-type": "application/json"
					}
				}).then(success, error)
				// $http.get(apiURL + resource).then(success).catch(error)
			},

			delete : function(resource, inputData, success, error) {
				console.log(apiURL+resource);
				$http({
					method: "DELETE",
					url: apiURL+resource,
					data: inputData,
					headers: {
						"Content-type": "application/json"
					}
				}).then(success, error)
				// console.log(data);
				// $http.delete(apiURL+resource, {"data": inputData}).then(success).catch(error)
			},

			put : function(resource, inputData, success, error) {
				$http({
					method: "PUT",
					url: apiURL+resource,
					data: inputData,
					headers: {
						"Content-type": "application/json"
					}
				}).then(success, error)

				// $http.put(apiURL+resource, inputData).then(success).catch(error)
			}
		};
	}
]);