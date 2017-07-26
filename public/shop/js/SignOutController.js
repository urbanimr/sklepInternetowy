StoreModule.controller("SignOutController", function($scope, $http, $location, UserService) {
    console.log('signout controller');

    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        UserService.confirmBeingLogged();
        if (UserService.user.isLogged === false) {
            $location.path('/signin');
        }
    });
    
    $scope.$on('$routeChangeSuccess', function () {
        $http.get("api/signout.php")
        .then(function(response) {
            var isLoggedOut = response.data.code === 1;
    console.log(response.data);
            $scope.signOutIsLoggedOut = isLoggedOut;
            $scope.signOutError = isLoggedOut ? '' : response.data.error;
            if (isLoggedOut) {
                UserService.signOut();
            }
        }, function(response) {
            $scope.signOutIsLoggedOut = false;
            $scope.signOutError = 'Ajax failed';
        });
    });
})