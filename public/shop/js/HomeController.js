StoreModule.controller("HomeController", function($scope, UserService) {
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        UserService.confirmBeingLogged();
    });
});