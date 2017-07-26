StoreModule.controller("SignInController", function($scope, $http, $location, UserService) {
    
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        UserService.confirmBeingLogged();
        if (UserService.user.isLogged === true) {
            $location.path('/');
        }
    });
    
    $scope.signIn = function() {
        var data = {
            email: $scope.email,
            password: $scope.password
        };
        
        $http({
            url: "api/signin.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isLogged = response.data.code === 1;
    console.log(response.data);
            $scope.signInIsLogged = isLogged;
            $scope.signInError = isLogged ? '' : response.data.error;
            if (isLogged) {
                UserService.signIn();
                console.log('Sign in controller - ajax ok - ' + UserService.user.isLogged)
            }
        }, function(response) {
            $scope.signInIsLogged = false;
            $scope.signInError = 'Ajax failed';
        });
    };

})