StoreModule.factory('UserService', function ($http) {
var user = {
    isLogged: false,
    userId: -1
};

var signIn = function() {
    user.isLogged = true;
}

var signOut = function() {
    user.isLogged = false;
    user.id = -1;
};

var confirmBeingLogged = function() {
    console.log('uruchamia się confirmBeingLogged');
    $http.get("api/checkauth.php")
    .then(function(response) {
        var isLogged = response.data.code === 1;
        if (isLogged) {
            signIn();
            return true;
        } else {
            signOut();
            return false;
        }
    }, function(response) {
        return false;
    });
}

return {
    user: user,
    signIn : signIn,
    signOut : signOut,
    confirmBeingLogged : confirmBeingLogged
  };
});