StoreModule.controller("SignInController", function($scope, UserService) {
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        $scope.justRegistered = UserService.justRegistered;
    });
    
    $scope.signIn = function() {
        UserService.justRegistered = false;
        $scope.justRegistered = false;
        UserService.signIn($scope.email, $scope.password);
    };
});