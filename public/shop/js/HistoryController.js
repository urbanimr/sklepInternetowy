StoreModule.controller("HistoryController", function($scope, $http) {
    $scope.orders = {};
    
    var loadOrders = function(){
        $http.get("api/load_orders.php")
        .then(function(response) {
            var success = typeof response.data.code === 'undefined';
            error = success ? '' : response.data.error;
            if (success) {
                $scope.orders = response.data;
            }
        }, function(response) {
            error = 'Ajax failed';
        });
    };
    
    $scope.$on('$routeChangeSuccess', function (e, current, previous) {
        loadOrders();
    });
    
    $scope.getDateSubmitted = function(order){
        for (var i = 0; i < order.statuses.length; i++) {
            if (order.statuses[i].status_id === 1) {
                return order.statuses[i].date;
            }
        }
    };
    
    $scope.getLastStatusName = function(order){
        var statuses = order.statuses;
        var lastStatus = statuses[statuses.length - 1];
        return lastStatus.status_name;
    };
});