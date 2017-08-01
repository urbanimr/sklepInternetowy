StoreModule.controller("CheckAuthController", function($scope, $http, UserService) {
    console.log('checkauth controller');
    
    $scope.$on('$routeChangeSuccess', function () {
        $scope.checkAuthIsLogged = UserService.user.isLogged;
        $scope.checkAuthError = '';
//        $http.get("api/checkauth.php")
//        .then(function(response) {
//            console.log(response.data);
//            var isLogged = response.data.code === 1;
//            $scope.checkAuthIsLogged = isLogged;
//            $scope.checkAuthError = '';
//        }, function(response) {
//            $scope.checkAuthIsLogged = null;
//            $scope.checkAuthError = 'Ajax failed';
//        });
    });
})