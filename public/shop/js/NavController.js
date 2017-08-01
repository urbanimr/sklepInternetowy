StoreModule.controller("NavController", function($scope, UserService) {
    $scope.user = UserService.user;
    
//    $scope.$watch('user', function (newVal, oldVal) {
//      $scope.isLogged = $scope.user.isLogged;
//    }, true);

});