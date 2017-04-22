angular.module("app").factory("APIService", ["$scope", "$window", "$http",
	function($window, $http, $scope) {
		var apiURL = "v1/";

		// var config = {
		// 	paramSerializer: '$httpParamSerializerJQLike'
		// };

		$scope.get = function(resource, params, success, error) {
			$http.get(apiURL+resource, params).then(success).catch(error)
		}

		$scope.post = function(resource, data, success, error) {
			console.log(apiURL+resource);
			$http.post(apiURL+resource, data).then(success).catch(error)
		}

		$scope.query = function(resource, success, error) {
			// console.log("here");
			$http.get(apiURL + resource).then(success).catch(error)
		}

		$scope.delete = function(resource, params, success, error) {
			$http.delete(apiURL+resource, params).then(success).catch(error)
		}

		$scope.put = function(resource, data, params, success, error) {
			$http.put(apiURL+resource).then(success).catch(error)
		}

		return {
			get : function(resource, params, success, error) {
				return this.get(resource, params, success, error);
			},
			post : function(resource, data, success, error) {
				return this.post(resource, data, success, error);
			},
			query : function(resource, success, error) {
				return this.get(resource, success, error);
			},
			delete : function(resource, params, success, error) {
				return this.delete(resource, params, success, error);
			},
			put : function(resource, data, params, success, error) {
				return this.put(resource, data, params, success, error);
			}
		};
	}
]);