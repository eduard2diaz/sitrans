var hojaruta = function () {
    var table = null;
    var obj = null;


    var configurarFormulario = function () {
        jQuery.validator.addMethod("greaterThan",
            function (value, element, params) {
                return moment(value) > moment($(params).val());
            }, 'Tiene que ser superior a la fecha de salida');

        $('select#hojaruta_vehiculo').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#hojaruta_tipoactividad').select2({
            dropdownParent: $("#basicmodal"),
        });

        $('input#hojaruta_fechasalida').datetimepicker();
        $('input#hojaruta_fechallegada').datetimepicker();

        $("div#basicmodal form").validate({
            rules: {
                'hojaruta[vehiculo]': {required: true},
                'hojaruta[tipoactividad]': {required: true},
                'hojaruta[descripcion]': {required: true},
                'hojaruta[litrosconsumidos]': {required: true, min: 1},
                'hojaruta[importe]': {required: true, min: 0.1},
                'hojaruta[kmrecorrido]': {required: true, min: 1},
                'hojaruta[codigo]': {required: true},
                'hojaruta[origen]': {required: true},
                'hojaruta[destino]': {required: true},
                'hojaruta[fechasalida]': {required: true},
                'hojaruta[fechallegada]': {required: true, greaterThan: "#hojaruta_fechasalida"},
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
    var configurarDataTable = function () {
        table = $("table#hojaruta_table").DataTable(
            {
                responsive: true,
                ajax: Routing.generate('hojaruta_index'),
                "language": {
                    url: datatable_translation
                },
                columns: [
                    {data: "id"}, {data: "vehiculo"}, {data: "codigo"}, {data: "fechasalida"}, {data: "fechallegada"}, {data: "acciones"}
                ],
                columnDefs: [
                    {
                        targets: 3, title: " Fecha de salida", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fechasalida.date).format('DD-MM-YYYY h:mm a');
                        }
                    },
                    {
                        targets: 4, title: " Fecha de llegada", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fechallegada.date).format('DD-MM-YYYY h:mm a');
                        }
                    }

                    , {
                        targets: -1, title: " ", orderable: !1, render: function (a, e, t, n) {
                            return ' <ul class="m-nav m-nav--inline m--pull-right">' +
                                '<li class="m-nav__item"><a class="btn btn-metal text-uppercase m-btn m-btn--icon btn-sm hojaruta_show" data-href="' + Routing.generate('hojaruta_show', {id: t.id}) + '"><i class="flaticon-eye"></i> Visualizar</a></li></ul>';
                        }
                    }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.hojaruta_show', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
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
                    mApp.unblock("body")
                }
            });
        });
    }

    var edicion = function () {
        $('body').on('click', 'a.edicion', function (evento) {

            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        configurarFormulario();
                        $('div#basicmodal').modal('show');
                    }
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        });
    }

    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#hojaruta_new', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function () {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                },
                complete: function () {
                    l.stop();
                    mApp.unblock("body");
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormulario();
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        objeto = table.row.add({
                            "id": data['id'],
                            "vehiculo": data['vehiculo'],
                            "codigo": data['codigo'],
                            "fechasalida": data['fechasalida'],
                            "fechallegada": data['fechallegada'],
                            "asignacion": data['asignacion']
                        });
                        objeto.draw();
                        table.page(pagina).draw('page');
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }

    var edicionAction = function () {
        $('div#basicmodal').on('submit', 'form#hojaruta_edit', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function () {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                },
                complete: function () {
                    l.stop();
                    mApp.unblock("body");
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormulario();
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        obj.parents('tr').children('td:nth-child(2)').html(data['vehiculo']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['codigo']);
                        obj.parents('tr').children('td:nth-child(4)').html(data['fechasalida']);
                        obj.parents('tr').children('td:nth-child(5)').html(data['fechallegada']);
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }
    var eliminar = function () {
        $('div#basicmodal').on('click', 'a.eliminar_hojaruta', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

                bootbox.confirm({
                    title: "Eliminar hoja de ruta",
                    message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar esta hoja de ruta?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
                    buttons: {
                        confirm: {
                            label: 'Sí, estoy seguro',
                            className: 'btn btn-primary btn-sm'
                        },
                        cancel: {
                            label: 'Cancelar',
                            className: 'btn btn-metal btn-sm'
                        }
                    },
                    callback: function (result) {
                        if (result == true)
                            $.ajax({
                                type: 'get',
                                url: link,
                                beforeSend: function () {
                                    mApp.block("body",
                                        {
                                            overlayColor: "#000000",
                                            type: "loader",
                                            state: "success",
                                            message: "Eliminando..."
                                        });
                                },
                                complete: function () {
                                    mApp.unblock("body")
                                },
                                success: function (data) {
                                    table.row(obj.parents('tr'))
                                        .remove()
                                        .draw('page');
                                    toastr.success(data['mensaje']);
                                },
                                error: function () {
                                    base.Error();
                                }
                            });
                    }
                });


        });
    }

     var importeVehiculo= function() {
       $('div#basicmodal').on('change', 'input#hojaruta_litrosconsumidos', function (evento) {
           var value = $(this).val();
           if (value >= 1 && $('input#hojaruta_fechasalida').val()!="")
               $.ajax({
                   type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                   dataType: 'html',
                   url: Routing.generate('preciocombustible_findbyvehiculo'),
                   data: {
                       litros: value,
                       fecha: $('input#hojaruta_fechasalida').val(),
                       vehiculo: $('select#hojaruta_vehiculo').val()
                   },
                   beforeSend: function (data) {
                       mApp.block("div#basicmodal div.modal-body",
                           {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                   },
                   success: function (data) {
                       $('input#hojaruta_importe').val(data);
                   },
                   error: function () {
                       base.Error();
                   },
                   complete: function () {
                       mApp.unblock("div#basicmodal div.modal-body")
                   }
               });
       });

       $('div#basicmodal').on('change', 'input#hojaruta_fechasalida', function (evento) {
           var value = $(this).val();
           var litro = $('input#hojaruta_litrosconsumidos').val();
           if (litro > 0)
               $.ajax({
                   type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                   dataType: 'html',
                   url: Routing.generate('preciocombustible_findbyvehiculo'),
                   data: {
                       litros: litro,
                       fecha: value,
                       vehiculo: $('select#hojaruta_vehiculo').val()
                   },
                   beforeSend: function (data) {
                       mApp.block("div#basicmodal div.modal-body",
                           {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                   },
                   success: function (data) {
                       $('input#hojaruta_importe').val(data);
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
            $().ready(function () {
                    configurarDataTable();
                    newAction();
                    show();
                    edicion();
                    edicionAction();
                    eliminar();
                    importeVehiculo();
                }
            );
        }
    }
}();



