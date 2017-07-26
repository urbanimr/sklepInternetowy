var StoreModule = angular.module("StoreModule", ["ngRoute"]);

StoreModule.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "templates/home.html",
        controller : "HomeController",
        access : "anyone"
    })
    .when("/category/:id", {
        templateUrl : "templates/category.html?ver=41",
        controller : "CategoryController",
        access : "anyone"
    })
    .when("/product/:id", {
        templateUrl : "templates/product.html?ver=4",
        controller : "ProductController",
        access : "anyone"
    })
    .when("/cart", {
        templateUrl : "templates/cart.html?ver=2",
        controller : "CartPageController",
        access : "logged"
    })
    .when("/profile", {
        templateUrl : "templates/profile.html",
        controller : "ProfileController",
        access : "logged"
    })
    .when("/checkauth", {
        templateUrl : "templates/checkauth.html",
        controller : "CheckAuthController",
        access : "anyone"
    })
    .when("/signin", {
        templateUrl : "templates/signin.html",
        controller : "SignInController",
        access : "logged"
    })
    .when("/signout", {
        templateUrl : "templates/signout.html",
        controller : "SignOutController",
        access : "anonymous"
    });
});

//StoreModule.run( function($rootScope, $location, UserService) {
//   $rootScope.$watch(function() { 
//        return $location.path(); 
//    },
//    function(newUrl){  
//        console.log('url has changed: ' + newUrl);
//        UserService.confirmBeingLogged();
//    });
//});
//
//StoreModule.run(function($rootScope, UserService) {
//    console.log('uruchamia się app run');
//    $rootScope.$on("$locationChangeStart", function(event, next, current) { 
//        UserService.confirmBeingLogged();
//        console.log(next);
//        if (next.access == 'logged' && UserService.user.isLogged === false) {
//            alert('Niezalogowany chce wejść!');
//        }
//        if (next.access == 'anonymous' && UserService.user.isLogged === true) {
//            alert('Zalogowany chce wejść!');
//        }
//    });
//});