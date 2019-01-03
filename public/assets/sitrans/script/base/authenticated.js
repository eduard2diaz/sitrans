//CONFIGURACION DE LOS CAMPOS DEL FORMULARIO DE USUARIO
var configurarFormularioUsuario = function () {
    $('select#usuario_idrol').select2({
        dropdownParent: $("#basicmodal"),
        //allowClear: true
    });
}

//VALIDACION DE LOS CAMPOS DE EDICION DE USUARIOS
function validarEditUser(){
    $("div#basicmodal form#usuario_edit").validate({
        rules:{
            'usuario[nombre]': {required:true},
            'usuario[apellido]': {required:true},
            'usuario[usuario]': {required:true},
            'usuario[correo]': {required:true, email:true},
            'usuario[idrol][]': {required:true},
            'usuario[password][second]': {
                equalTo: "#usuario_password_first"
            }
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

function colorear(enfuncionamiento) {
    var badge_class=enfuncionamiento ? 'success' : 'danger';
    var badge_label=enfuncionamiento ? 'SI' : 'NO';
    var badge_object='<span class="m-badge m-badge--'+badge_class+' m--font-boldest m-badge--wide">\n' +badge_label+'</span>';

    return badge_object
}

//dentro de este tipo de funciones se pueden definir variables y otras funciones
var authenticated = function () {
    var obj = null;
    //INTERFAZ DE VISUALIZACION DE DETALLES DEL USUARIO
    var usuarioProfile = function () {
        $('body').on('click', 'a#usuarioshow_ajax', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        $('div#basicmodal').modal('show');
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                }
            });
        });
    }
    //EVENTO DE ESCUCHA DE EDICION DE USUARIO
    var edicionCurrentUser = function () {
        $('div#basicmodal, table#usuario_table').on('click', 'a.editar_usuario', function (evento) {
            evento.preventDefault();
            if ($('table#usuario_table').length)
                obj = $(this);
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                       configurarFormularioUsuario();
                        $('div#basicmodal').modal('show');
                        validarEditUser();
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                }
            });
        });
    }
    //PROCESAMIENTO DEL FORMULARIO DE EDICION DE USUARIOS
    var edicionCurrentUserAction = function () {
        $('div#basicmodal').on('submit', 'form#usuario_edit', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector( '.ladda-button' ) );
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(), //para enviar el formulario hay que serializarlo
                beforeSend: function () {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                complete: function () {
                    l.stop();
                    mApp.unblock("body");
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormularioUsuario();
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);
                        if ($('table#usuario_table').length) {
                            obj.parents('tr').children('td:nth-child(2)').html(data['nombre']);
                            obj.parents('tr').children('td:nth-child(3)').html(data['apellidos']);
                            obj.parents('tr').children('td:nth-child(4)').html(colorear(data['activo']));
                        }
                        $('div#basicmodal').modal('hide');
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }

    var importeTarjeta= function(litros, fecha,tarjeta,importe) {
        $('div#basicmodal').on('change', litros, function (evento) {
            var value = $(this).val();
            if (value >= 1 && $(fecha).val()!="")
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('preciocombustible_findbytarjeta'),
                    data: {
                        litros: value,
                        fecha: $(fecha).val(),
                        tarjeta: $(tarjeta).val()
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $(importe).val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });

        $('div#basicmodal').on('change', fecha, function (evento) {
            var value = $(this).val();
            var litro = $(litros).val();
            if (litro > 0)
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('preciocombustible_findbytarjeta'),
                    data: {
                        litros: litro,
                        fecha: value,
                        tarjeta: $(tarjeta).val()
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $(importe).val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });
    }

    var importeVehiculo= function(litros, fecha,vehiculo,importe) {
        $('div#basicmodal').on('change', litros, function (evento) {
            var value = $(this).val();
            if (value >= 1 && $(fecha).val()!="")
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('preciocombustible_findbyvehiculo'),
                    data: {
                        litros: value,
                        fecha: $(fecha).val(),
                        vehiculo: $(vehiculo).val()
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $(importe).val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });

        $('div#basicmodal').on('change', fecha, function (evento) {
            var value = $(this).val();
            var litro = $(litros).val();
            if (litro > 0)
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('preciocombustible_findbyvehiculo'),
                    data: {
                        litros: litro,
                        fecha: value,
                        vehiculo: $(vehiculo).val()
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $(importe).val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });
    }

    return {
        init: function () {
            $().ready(function(){
                usuarioProfile();
                edicionCurrentUser();
                edicionCurrentUserAction();
            });
        },
        importeTarjeta: function (litros, fecha,tarjeta,importe) {
            importeTarjeta(litros, fecha,tarjeta,importe);
        }
        ,importeVehiculo: function (litros, fecha,vehiculo,importe) {
            importeVehiculo(litros, fecha,vehiculo,importe);
        }
    };
}();