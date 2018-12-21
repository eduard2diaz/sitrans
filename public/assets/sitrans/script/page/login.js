var login = function () {
    var table = null;
    var obj = null;

    var login=function(){
        $("div#login-form form").validate({
            rules:{
                '_username': {required:true},
                '_password': {required:true},
            }
        });
    }
    return {
        init: function () {
            $().ready(function () {
                login();
                }
            );
        }
    }
}();



