StoreModule.controller("RootController", function($scope, $location, UserService) {
    $scope.user = UserService.user;
});