StoreModule.controller("TopCartController", function($scope, CartService, $timeout) {
    $scope.cart = CartService.getCart();
    
    var updateCartInfo = function(){
        console.log('działa updateCartInfo');
        $timeout(function() {
            console.log(CartService.getCart());
            $scope.cart = CartService.getCart();
        })

    };

    CartService.registerObserverCallback(updateCartInfo);
});