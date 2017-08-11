StoreModule.controller("ProductController", function($scope, $http, CartService, $routeParams) {
    var productId = $routeParams.id;
    
    $http.get("api/product.php?id=" + productId)
    .then(function(response) {
        var isProductLoaded = typeof response.data.code === 'undefined';
        $scope.isProductLoaded = isProductLoaded;
        $scope.error = isProductLoaded ? '' : response.data.error;
        $scope.product = isProductLoaded ? response.data : null;
    }, function(response) {
        $scope.isProductLoaded = false;
        $scope.error = 'Ajax failed';
        $scope.product = null;
    });
    
    $scope.addToCart = function(id) {
        CartService.addToCart(productId);
    };
});