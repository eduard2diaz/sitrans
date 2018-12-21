//dentro de este tipo de funciones se pueden definir variables y otras funciones
var base = function () {

    var internacionalizar = function () {
        var validator_message={
                    "required": "Este campo es obligatorio.",
                    "remote": "Por favor arregla este campo.",
                    "email": "Por favor, introduce una dirección de correo electrónico válida.",
                    "url": "Por favor introduzca un URL válido.",
                    "date": "Por favor introduzca una fecha válida.",
                    "dateISO": "Por favor introduzca una fecha válida (ISO).",
                    "number": "por favor ingrese un número válido.",
                    "digits": "Por favor ingrese solo dígitos.",
                    "creditcard": "Por favor, introduzca un número de tarjeta de crédito válida.",
                    "equalTo": "Ingresa el mismo valor.",
                    "accept": "Por favor ingrese un valor con una extensión válida.",
                    "checkTime": "Por favor comprueba la hora.",
                    "pattern": "Este valor no es válido",
                    "compareDate": "La fecha de inio debe ser inferior a la de fin.",

                };

        $.extend(jQuery.validator.messages,validator_message)
    }

    var personalizarToastr=function () {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-left",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    }

    return {
        init:function(){
            internacionalizar();
           // personalizarToastr();
        },

        Error: function () {
            bootbox.confirm({
                title: 'Error',
                message: "<p class='m--font-boldest  m--font-danger'><i class='flaticon-danger' style='margin-right: 1%'></i>Lo sentimos, ha ocurrido un error.</h5>",
                buttons: {
                    confirm: {
                        label: 'Refrescar',
                        className: 'btn btn-primary'
                    },
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn btn-metal'
                    },
                },
                callback: function (result) {
                    if (result == true)
                        document.location.reload();
                }
            });
        },
    };
}();