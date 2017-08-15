StoreModule.controller("RegisterController", function($scope, UserService) {
    $scope.newUser = {};
    $scope.newBillingAddress = {
        alias: 'my address'
    };
    
    $scope.register = function($event) {
        UserService.register($scope.newUser, $scope.newBillingAddress);
    };
});