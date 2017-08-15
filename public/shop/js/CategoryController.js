StoreModule.controller("CategoryController", function($scope, $http, CartService, $routeParams) {
    var categoryId = $routeParams.id;
    
    $http.get("api/category.php?id=" + categoryId)
    .then(function(response) {
        var isCategoryLoaded = typeof response.data.code === 'undefined';
        $scope.isCategoryLoaded = isCategoryLoaded;
        $scope.error = isCategoryLoaded ? '' : response.data.error;
        $scope.category = isCategoryLoaded ? response.data : null;
    }, function(response) {
        $scope.isCategoryLoaded = false;
        $scope.error = 'Ajax failed';
        $scope.category = null;
    });
    
    $scope.addToCart = function(id) {
        CartService.addToCart(id);
    };
});