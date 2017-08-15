StoreModule.controller("SignOutController", function($scope, UserService) {
    $scope.$on('$routeChangeSuccess', function () {
        UserService.signOut();
    });
});