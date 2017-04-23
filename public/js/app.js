var app = angular.module("app", ["ngRoute", "app.controllers", "ui.grid", "ui.grid.selection", "app.services"])
.config(function($routeProvider, $httpProvider){

    // $routeProvider
    //     .when('/equipments', {
    //         templateUrl: 'templates/equipments-list.html',
    //         controller: 'EquipmentsController'
    //     })
    //     .when('/equipments/new', {
    //         templateUrl: 'templates/create-equipment.html',
    //         controller: 'NewEquipmentController'
    //     })
    //     .when('/equipments/:id', {
    //         templateUrl: 'templates/edit-equipment.html',
    //         controller: 'EditEquipmentController'
    //     })
    //     .when('/', {
    //         templateUrl: 'templates/home.html',
    //         controller: "HomeController"
    //     })
    //     .when('/auth', {
    //         templateUrl: 'templates/auth.html',
    //         controller: "AuthController"
    //     })
    //     .otherwise({redirectTo: '/'});

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
        // '/equipments/DepartmentTag/:departmenttag': {
        //     templateUrl: 'templates/edit-equipment.html',
        //     controller: 'EditEquipmentController'
        // },
        '/users/new': {
            templateUrl: 'templates/create-user.html',
            controller: 'NewUserController'
            // adminOnly: true
        },
        '/equipmenttypes' : {
            templateUrl:'templates/equipmenttypes-list.html',
            controller: 'EquipmentTypesController'
        },
        '/equipmenttypes/new' : {
            templateUrl: 'templates/create-equipmenttype.html',
            controller: 'NewEquipmentTypeController'
        },

        '/equipments/:departmenttag' : {
            templateUrl: 'templates/edit-equipment.html',
            controller: 'EditEquipmentController'
        },
        '/loans/:username' : {
            templateUrl: 'templates/loans.html',
            controller: 'LoanController'
        },
        '/users' : {
            templateUrl: 'templates/users.html',
            controller: 'UsersController'
        },
        '/equipmenttypes/name/:name' : {
            templateUrl: 'templates/edit-equipmenttype.html',
            controller: 'EditEquipmentTypeController'

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

    // $routeProvider
    //     .when('/', {
    //         // main page with all items viewed.
    //         // admin will have options to add, user will just view their items
    //         templateUrl: 'templates/home.html',
    //         controller: 'HomeController'
    //     })
    //     .when('/equipments/new', {
    //         templateUrl: 'templates/create-equipment.html',
    //         controller: 'NewEquipmentController',
    //         adminOnly: true
    //     })
    //     // .when('equipmenttypes/new' {
    //     //     templateUrl: 'templates/create-equipmenttype.html',
    //     //     controller: 'NewEquipmentTypeController'
    //     // })
    //     .when('/users/new', {
    //         templateUrl: 'templates/create-user.html',
    //         controller: 'NewUserController',
    //         adminOnly: true
    //     })
    //     .otherwise({redirectTo: '/'});

    // .when('/equipmenttypes', {
    // 	templateUrl: 'templates/equipmenttypes-list.html',
    // 	controller: 'EquipmentTypesController'
    // });

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
