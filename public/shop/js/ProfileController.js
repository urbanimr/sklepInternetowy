StoreModule.controller("ProfileController", function($scope, $location, UserService) {
    
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        UserService.confirmBeingLogged();
        if (UserService.user.isLogged === false) {
            $location.path('/signin');
        }
    });
});