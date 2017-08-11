var StoreModule = angular.module("StoreModule", ["ngRoute"]);

StoreModule.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "templates/home.html?ver=95",
        controller : "HomeController",
        access : "anyone"
    })
    .when("/category/:id", {
        templateUrl : "templates/category.html?ver=95",
        controller : "CategoryController",
        access : "anyone"
    })
    .when("/product/:id", {
        templateUrl : "templates/product.html?ver=95",
        controller : "ProductController",
        access : "anyone"
    })
    .when("/cart", {
        templateUrl : "templates/cart.html?ver=95",
        controller : "CartPageController",
        access : "logged"
    })
    .when("/profile", {
        templateUrl : "templates/profile.html?ver=95",
        controller : "ProfileController",
        access : "logged"
    })
    .when("/signin", {
        templateUrl : "templates/signin.html?ver=95",
        controller : "SignInController",
        access : "anonymous"
    })
    .when("/signout", {
        templateUrl : "templates/signout.html?ver=95",
        controller : "SignOutController",
        access : "logged"
    })
    .when("/register", {
        templateUrl : "templates/register.html?ver=95",
        controller : "RegisterController",
        access : "anonymous"
    })
    .when("/submitted", {
        templateUrl : "templates/submitted.html?ver=95",
        controller : "SubmittedController",
        access : "logged"
    });
});