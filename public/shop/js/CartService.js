StoreModule.factory('CartService', function ($http, UserService) {
    
    var cart = {
//        total_amount: 30.50,
//        shipping_cost: 15.50,
//        products: [1, 2, 3]
    };
    
    var getCart = function(){
        return cart;
    };
    
    var cartError = '';
    
    var observerCallbacks = [];

    var registerObserverCallback = function(callback){
        console.log('działa register callback');
        observerCallbacks.push(callback);
    };

    var notifyObservers = function(){
        console.log('działa notifyObserver');
        angular.forEach(observerCallbacks, function(callback){
            callback();
        });
    };
    
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
            console.log(response.data);
            var success = typeof response.data.code == 'undefined';
            console.log(success);
            cartError = success ? '' : response.data.error;
            if (success) {
                cart = response.data;
                console.log(cart);
                notifyObservers();
            }
        }, function(response) {
            cartError = 'Ajax failed';
        });
    };
    
    return {
        cart: cart,
        cartError: cartError,
        addToCart: addToCart,
        registerObserverCallback: registerObserverCallback,
        getCart: getCart
    };
  
//var user = {
//    isLogged: false,
//    userId: -1
//};
//
//var signIn = function() {
//    user.isLogged = true;
//}
//
//var signOut = function() {
//    user.isLogged = false;
//    user.id = -1;
//};
//
//var confirmBeingLogged = function() {
//    console.log('uruchamia się confirmBeingLogged');
//    $http.get("api/checkauth.php")
//    .then(function(response) {
//        var isLogged = response.data.code === 1;
//        if (isLogged) {
//            signIn();
//            return true;
//        } else {
//            signOut();
//            return false;
//        }
//    }, function(response) {
//        return false;
//    });
//}
//
//return {
//    user: user,
//    signIn : signIn,
//    signOut : signOut,
//    confirmBeingLogged : confirmBeingLogged
//  };
});