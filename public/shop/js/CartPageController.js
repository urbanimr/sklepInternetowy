StoreModule.controller("CartPageController", function($scope, $location, UserService, CartService, $timeout) {
    $scope.cart = CartService.getCart();
    
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        UserService.confirmBeingLogged();
        if (UserService.user.isLogged === false) {
            $location.path('/signin');
        }
    });
    
//    var updateCartInfo = function(){
//        console.log('działa updateCartInfo');
//        $timeout(function() {
//            console.log(CartService.getCart());
//            $scope.cart = CartService.getCart();
//        })
//    };
//
//    CartService.registerObserverCallback(updateCartInfo);
});