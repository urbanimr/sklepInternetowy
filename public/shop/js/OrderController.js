StoreModule.controller("OrderController", function($scope, $http, $routeParams) {
    var orderId = $routeParams.id;
    $scope.order = {};
    
    var loadOrder = function(){
        $http.get("api/load_orders.php?id=" + orderId)
        .then(function(response) {
            var success = typeof response.data.code === 'undefined';
            error = success ? '' : response.data.error;
            if (success) {
                $scope.order = response.data;
            }
        }, function(response) {
            error = 'Ajax failed';
        });
    };
    
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        loadOrder();
    });
});