var login = function () {
    var table = null;
    var obj = null;

    var login=function(){
        $("div#login-form form").validate({
            rules:{
                '_username': {required:true},
                '_password': {required:true},
            },
            highlight: function (element) {
                $(element).parent().parent().addClass('has-danger');
            },
            unhighlight: function (element) {
                $(element).parent().parent().removeClass('has-danger');
                $(element).parent().parent().addClass('has-success');
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



