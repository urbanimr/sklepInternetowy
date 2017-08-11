StoreModule.controller("RootController", function($scope, $location, $timeout, UserService, CartService) {
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        UserService.confirmBeingLogged();
        
        if (current.access === 'logged' && $scope.userIsLogged === false) {
            $location.path('/signin');
        }
        
        if (current.access === 'anonymous' && $scope.userIsLogged === true) {
            $location.path('/');
        }
    });
    
    var updateUserInfo = function(){
        $timeout(function() {
            $scope.user = UserService.getUser();
            $scope.userIsLogged = typeof $scope.user.id !== 'undefined';
            if ($scope.userIsLogged) {
                CartService.loadCart();
            } else {
                CartService.cart = {};
            }
        })
    };
    
    updateUserInfo();
    UserService.registerObserverCallback(updateUserInfo);
    
    var updateCartInfo = function(){
        $timeout(function() {
            $scope.cart = CartService.getCart();
        })
    };

    updateCartInfo();
    CartService.registerObserverCallback(updateCartInfo);
});