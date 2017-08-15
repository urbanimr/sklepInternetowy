StoreModule.factory('UserService', function ($http, CartService, $location) {
    var user = {};
    var getUser = function(){
        return user;
    };
    var justRegistered = false;
    var userError = '';
    
    var addresses = {};
    var getAddresses = function(){
        return addresses;
    };
    
    var observerCallbacks = [];
    var registerObserverCallback = function(callback){
        observerCallbacks.push(callback);
    };
    var notifyObservers = function(){
        angular.forEach(observerCallbacks, function(callback){
            callback();
        });
    };
    
    var register = function(user, billingAddress){
        var data = {
            user: user,
            billingAddress: billingAddress
        };
        
        $http({
            url: "api/register.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isRegistered = response.data.code === 1;
            userError = isRegistered ? '' : response.data.error;
            if (isRegistered) {
                justRegistered = true;
                $location.path('/signin');
            }
        }, function(response) {
            userError = 'Ajax failed';
        });
    };

    var signIn = function(email, password) {
        var data = {
            email: email,
            password: password
        };

        $http({
            url: "api/signin.php",
            method: 'POST',
            data: data
        })
        .then(function(response) {
            var isLogged = typeof response.data.id !== 'undefined';
            userError = isLogged ? '' : response.data.error;
            if (isLogged) {
                user = response.data;
                CartService.loadCart();
                $location.path('/');
            }
            notifyObservers();
        }, function(response) {
            userError = 'Ajax failed';
            notifyObservers();
        });
    };
    
    var signOut = function() {
        $http.get("api/signout.php")
        .then(function(response) {
            var isLoggedOut = response.data.code === 1;
            userError = isLoggedOut ? '' : response.data.error;
            if (isLoggedOut) {
                user = {};
                CartService.cart = {};
                notifyObservers();
                $location.path('/signin');
            }
            notifyObservers();
        }, function(response) {
            userError = 'Ajax failed';
            notifyObservers();
        });
    };
    
    var confirmBeingLogged = function() {
        $http.get("api/checkauth.php")
        .then(function(response) {
            var isLogged = response.data.code === 1;
            if (isLogged) {
                return true;
            } else {
                user = {};
                notifyObservers();
                return false;
            }
        }, function(response) {
            return false;
        });
    };
    
    var loadAddresses = function() {
        $http.get("api/load_addresses.php")
        .then(function(response) {
            var areAddressesLoaded = typeof response.data.billingAddress !== 'undefined'
                && typeof response.data.shippingAddress !== 'undefined';
            addressesError = areAddressesLoaded ? '' : response.data.error;
            if (areAddressesLoaded) {
                addresses = response.data;
                notifyObservers();
            }
        }, function(response) {
            addressesError = 'Ajax failed';
        });
    };

    return {
        user: user,
        getUser: getUser,
        justRegistered: justRegistered,
        userError: userError,
        register: register,
        signIn: signIn,
        signOut: signOut,
        confirmBeingLogged: confirmBeingLogged,
        loadAddresses: loadAddresses,
        getAddresses: getAddresses,
        registerObserverCallback: registerObserverCallback
    };
});