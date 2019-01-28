var usuario = function () {
    var table = null;
    var obj = null;

    var configurarFormulario = function () {
        $('select#usuario_idrol').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#usuario_institucion').select2({
            dropdownParent: $("#basicmodal"),
        });
        $("div#basicmodal form#usuario_new").validate({
            rules: {
                'usuario[nombre]': {required: true},
                'usuario[apellidos]': {required: true},
                'usuario[usuario]': {required: true},
                'usuario[correo]': {required: true},
                'usuario[password][first]': {required: true},
                'usuario[password][second]': {equalTo: "#usuario_password_first"},
                'usuario[idrol][]': {required: true},
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
        table = $("table#usuario_table").DataTable(
            {
                responsive: true,
                ajax: Routing.generate('usuario_index'),
                "language": {
                    url: datatable_translation
                },
                columns: [
                    {data: "id"}, {data: "nombre"}, {data: "apellidos"}, {data: "activo"}, {data: "acciones"}
                ],
                columnDefs: [
                    {
                        targets: -2, title: "Activo", orderable: 1, render: function (a, e, t, n) {
                            return colorear(t.activo);
                        }
                    }
                    ,
                    {
                        targets: -1, title: " ", orderable: !1, render: function (a, e, t, n) {
                            return ' <ul class="m-nav m-nav--inline m--pull-right">' +
                                '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm text-uppercase usuario_show" data-href="' + Routing.generate('usuario_show', {id: t.id}) + '"><i class="flaticon-eye"></i> Visualizar</a></li>' +
                                '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm text-uppercase editar_usuario" data-href="' + Routing.generate('usuario_edit', {id: t.id}) + '"><i class="flaticon-edit-1"></i> Editar</a></li></ul>';
                            ;
                        }
                    }]
            });
    }


    var show = function () {
        $('body').on('click', 'a.usuario_show', function (evento) {
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
    var nuevo = function () {
        $('body').on('click', 'a#nuevo_usuario', function (evento) {
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
        $('div#basicmodal').on('submit', 'form#usuario_new', function (evento) {
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
                            "nombre": data['nombre'],
                            "apellidos": data['apellidos'],
                            "activo": data['activo'],
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

    var eliminar = function () {
        $('div#basicmodal').on('click', 'a.eliminar_usuario', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: "Eliminar usuario",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este usuario?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                    show();
                    nuevo();
                    eliminar();
                }
            );
        }
    }
}();



