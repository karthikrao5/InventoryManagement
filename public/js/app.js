var app = angular.module("app", ["ngRoute","ngAnimate", "ngMaterial", "ngAria", "app.controllers", "ui.grid", "ui.grid.selection", "app.services"])
.config(function($routeProvider, $httpProvider){

    window.routes = 
    {
        '/' : {
            // main page with all items viewed.
            // admin will have options to add, user will just view their items
            templateUrl: 'templates/home.html',
            controller: 'HomeController',
            // adminOnly: false
        },
        '/equipments/new': {
            templateUrl: 'templates/create-equipment.html',
            controller: 'NewEquipmentController'
            // adminOnly: true
        },
        '/equipments/:departmenttag' : {
            templateUrl: 'templates/edit-equipment.html',
            controller: 'EditEquipmentController'
        },
        '/users/new': {
            templateUrl: 'templates/create-user.html',
            controller: 'NewUserController'
            // adminOnly: true
        },
        '/users' : {
            templateUrl: 'templates/users.html',
            controller: 'UsersController'
        },
        '/users/:username' : {
            templateUrl: 'templates/view-user.html',
            controller: 'ViewUserController'
        },
        '/equipmenttypes' : {
            templateUrl:'templates/equipmenttypes-list.html',
            controller: 'EquipmentTypesController'
        },
        '/equipmenttypes/name/:name' : {
            templateUrl: 'templates/edit-equipmenttype.html',
            controller: 'EditEquipmentTypeController'
        },
        '/equipmenttypes/new' : {
            templateUrl: 'templates/create-equipmenttype.html',
            controller: 'NewEquipmentTypeController'
        },
        '/loans/:username' : {
            templateUrl: 'templates/loans.html',
            controller: 'LoanController'
        },
        '/403': {
            templateUrl:'templates/errorPages/403.html'
            // adminOnly: false
        },
        '/401': {
            templateUrl:'templates/errorPages/401.html'
            // adminOnly: false
        },
        '/500': {
            templateUrl:'templates/errorPages/500.html'
            // adminOnly: false
        }
    };

    for(var path in routes) {
        $routeProvider.when(path, window.routes[path]);
    }
    $routeProvider.otherwise({redirectTo: '/'});

    // $httpProvider.interceptors.push("authInterceptor");
    
});

app.directive('ngConfirmClick', [
    function() {
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmClick || "Are you sure?";
                var clickAction = attr.confirmedClick;
                element.bind('click', function (event) {
                    if (window.confirm(msg)) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
}]);


// TODO: use this to check if authenticated between pages. if not authenticated, redirect to
// auth page

// .run(["$rootScope", "Auth", "$location" ,function($rootScope, Auth, $location) {
//     $rootScope.$on("$locationChangeStart", function(event,next,current) {
//         for(var i in window.routes) {
//             if(next.indexOf(i) != -1) {
//                 if((window.routes[i].adminOnly && !Auth.isAdminOrHook())) {
//                     // alert("You need to be admin to see this page!");
//                     // event.preventDefault();
//                 }
//             }
//         }
//     });
// }]);
