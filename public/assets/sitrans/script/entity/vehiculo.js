var vehiculo = function () {
    var table = null;
    var obj = null;
    var vehiculo_id = null;

    var configurarFormulario = function () {
        $('select#vehiculo_tipocombustible').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#vehiculo_institucion').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#vehiculo_estado').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#vehiculo_responsable').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#vehiculo_chofer').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#vehiculo_tipovehiculo').select2({
            dropdownParent: $("#basicmodal"),
        });

        $("div#basicmodal form").validate({
            rules: {
                'vehiculo[matricula]': {required: true},
                'vehiculo[marca]': {required: true},
                'vehiculo[modelo]': {required: true},
                'vehiculo[tipocombustile]': {required: true},
                'vehiculo[institucion]': {required: true},
                'vehiculo[tipovehiculo]': {required: true},
                'vehiculo[indconsumo]': {required: true, min: 1},
                'vehiculo[kmsxmantenimiento]': {required: true},
                'vehiculo[estado]': {required: true},
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
        table = $("table#vehiculo_table").DataTable(
            {
                responsive: true,
                ajax: Routing.generate('vehiculo_index'),
                "language": {
                    url: datatable_translation
                },
                columns: [
                    {data: "id"}, {data: "matricula"}, {data: "marca"}, {data: "tipocombustible"}, {data: "acciones"}
                ],
                columnDefs: [{
                    targets: -1, title: " ", orderable: !1, render: function (a, e, t, n) {
                        return ' <ul class="m-nav m-nav--inline m--pull-right">' +
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm text-uppercase vehiculo_show" data-href="' + Routing.generate('vehiculo_show', {id: t.id}) + '"><i class="flaticon-eye"></i> Visualizar</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm text-uppercase edicion" data-href="' + Routing.generate('vehiculo_edit', {id: t.id}) + '"><i class="flaticon-edit-1"></i> Editar</a></li>';
                    }
                }]
            });
    }

    var tipovehiculoListener = function () {
        $('div#basicmodal').on('change', 'select#vehiculo_tipovehiculo', function (evento) {
            var institucion = $('select#vehiculo_institucion').val();
            if ($(this).val() > 0 && institucion > 0)
                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: Routing.generate('chofer_findbytipovehiculo', {'id': $(this).val()}),
                    data: {
                        institucion: institucion
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        var cadena = "<option value='-1'></option>";
                        var array = JSON.parse(data);
                        for (var i = 0; i < array.length; i++)
                            cadena += "<option value=" + array[i]['id'] + ">" + array[i]['nombre'] + "</option>";
                        $('select#vehiculo_chofer').html(cadena);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });

        $('div#basicmodal').on('change', 'select#vehiculo_institucion', function (evento) {
            var tipo = $('select#vehiculo_tipovehiculo').val();
            if ($(this).val() > 0) {
                if (tipo > 0)
                    $.ajax({
                        type: 'get',
                        dataType: 'html',
                        url: Routing.generate('chofer_findbytipovehiculo', {'id': tipo}),
                        data: {
                            institucion: $(this).val()
                        },
                        beforeSend: function (data) {
                            mApp.block("div#basicmodal div.modal-body",
                                {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                        },
                        success: function (data) {
                            var cadena = "<option value='-1'></option>";
                            var array = JSON.parse(data);
                            for (var i = 0; i < array.length; i++)
                                cadena += "<option value=" + array[i]['id'] + ">" + array[i]['nombre'] + "</option>";
                            $('select#vehiculo_chofer').html(cadena);
                        },
                        error: function () {
                            base.Error();
                        },
                        complete: function () {
                            mApp.unblock("div#basicmodal div.modal-body")
                        }
                    });

                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: Routing.generate('responsable_findbyinstitucion', {'id': $(this).val()}),
                    data: {
                        vehiculo: vehiculo_id
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        var cadena = "<option value='-1'></option>";
                        var array = JSON.parse(data);
                        for (var i = 0; i < array.length; i++)
                            cadena += "<option value=" + array[i]['id'] + ">" + array[i]['nombre'] + "</option>";
                        $('select#vehiculo_responsable').html(cadena);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });

            }
        });
    }

    var show = function () {
        $('body').on('click', 'a.vehiculo_show', function (evento) {
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
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data.html)) {
                        configurarFormulario();
                        $('div#basicmodal').modal('show');
                        if (data.vehiculo)
                            vehiculo_id = data.vehiculo;
                        else
                            vehiculo_id = null;
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
        $('div#basicmodal').on('submit', 'form#vehiculo_new', function (evento) {
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
                            "matricula": data['matricula'],
                            "marca": data['marca'],
                            "tipocombustible": data['tipocombustible'],
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
        $('div#basicmodal').on('submit', 'form#vehiculo_edit', function (evento) {
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
                        obj.parents('tr').children('td:nth-child(2)').html(data['matricula']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['marca']);
                        obj.parents('tr').children('td:nth-child(4)').html(data['tipocombustible']);
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }
    var eliminar = function () {
        $('div#basicmodal').on('click', 'a.eliminar_vehiculo', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: "Eliminar vehículo",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este vehículo?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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

    return {
        init: function () {
            $().ready(function () {
                    configurarDataTable();
                    newAction();
                    tipovehiculoListener();
                    show();
                    edicion();
                    edicionAction();
                    eliminar();
                }
            );
        }
    }
}();



