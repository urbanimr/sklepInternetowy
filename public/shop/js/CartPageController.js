StoreModule.controller("CartPageController", function($scope, $http, $route, $location, UserService, CartService, $timeout) {
    $scope.carriers = {};
    $scope.payments = {};
    $scope.addresses = {};
    
    var loadCarriers = function(){
        $http.get("api/load_carriers.php")
        .then(function(response) {
            var success = typeof response.data.code === 'undefined';
            error = success ? '' : response.data.error;
            if (success) {
                $scope.carriers = response.data;
            }
        }, function(response) {
            error = 'Ajax failed';
        });
    };
    
    var loadPayments = function(){
        $http.get("api/load_payments.php")
        .then(function(response) {
            var success = typeof response.data.code === 'undefined';
            error = success ? '' : response.data.error;
            if (success) {
                $scope.payments = response.data;
            }
        }, function(response) {
            error = 'Ajax failed';
        });
    };
    
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        loadCarriers();
        loadPayments();
    });
    
    var updateAddresses = function(){
        $timeout(function() {
            $scope.addresses = UserService.getAddresses();
        });
    };
    UserService.registerObserverCallback(updateAddresses);
    UserService.loadAddresses();
    
    $scope.updateQty = function(product){
        CartService.updateQty(product);
    };
    
    $scope.remove = function(product){
        CartService.remove(product);
    };
    
    $scope.updateDetails = function(property){
        CartService.updateDetails(property);
    };
    
    $scope.submitOrder = function(){
        CartService.submitOrder();
    };
});