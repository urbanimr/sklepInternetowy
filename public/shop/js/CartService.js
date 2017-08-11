StoreModule.factory('CartService', function ($http, $route, $location) {
    var cart = {};
    
    var getCart = function(){
        return cart;
    };
    
    var cartError = '';
    
    var observerCallbacks = [];

    var registerObserverCallback = function(callback){
        observerCallbacks.push(callback);
    };

    var notifyObservers = function(){
        angular.forEach(observerCallbacks, function(callback){
            callback();
        });
    };
    
    var loadCart = function() {
        $http.get("api/load_cart.php")
        .then(function(response) {
            var success = typeof response.data.code == 'undefined';
            cartError = success ? '' : response.data.error;
            if (success) {
                cart = response.data;
                notifyObservers();
            }
        }, function(response) {
            cartError = 'Ajax failed';
        });
    }
    
    var addToCart = function(productId) {
        var data = {
            id: productId
        };
        
        $http({
            url: "api/add_to_cart.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var success = typeof response.data.code == 'undefined';
            cartError = success ? '' : response.data.error;
            if (success) {
                cart = response.data;
                notifyObservers();
            }
        }, function(response) {
            cartError = 'Ajax failed';
        });
    };
    
    var updateQty = function(product) {
        var data = product;
        
        $http({
            url: "api/update_qty.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isSuccess = typeof response.data.code === 'undefined';
            cartError = isSuccess ? '' : response.data.error;
            if (isSuccess) {
                cart = response.data;
                notifyObservers();
            } else {
                $route.reload();
            }
        }, function(response) {
            cartError = 'Ajax failed';
            $route.reload();
        });
    }
    
    var remove = function(product) {
        product.quantity = 0;
        var data = product;
        
        $http({
            url: "api/update_qty.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isSuccess = typeof response.data.code === 'undefined';
            cartError = isSuccess ? '' : response.data.error;
            $route.reload();
        }, function(response) {
            cartError = 'Ajax failed';
            $route.reload();
        });
    }
    
    var updateDetails = function(property) {
        var data = {
            property: property,
            value: cart[property]
        };
        
        $http({
            url: "api/update_details.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isSuccess = typeof response.data.code === 'undefined';
            cartError = isSuccess ? '' : response.data.error;
            if (isSuccess) {
                cart = response.data;
                notifyObservers();
            } else {
                $route.reload();
            }
        }, function(response) {
            cartError = 'Ajax failed';
            $route.reload();
        });
    };
    
    var submitOrder = function(){
        var data = {};
        
        $http({
            url: "api/submit_order.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isSuccess = response.data.code === 1;
            cartError = isSuccess ? '' : response.data.error;
            if (isSuccess) {
                cart = {};
                notifyObservers();
                $location.path('/submitted');
            } else {
                $route.reload();
            }
        }, function(response) {
            cartError = 'Ajax failed';
            $route.reload();
        });
    };
    
    return {
        cart: cart,
        cartError: cartError,
        loadCart: loadCart,
        addToCart: addToCart,
        updateQty: updateQty,
        remove: remove,
        updateDetails: updateDetails,
        registerObserverCallback: registerObserverCallback,
        getCart: getCart,
        submitOrder: submitOrder
    };
});