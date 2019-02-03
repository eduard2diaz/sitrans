var lecturareloj = function () {
    var table = null;
    var obj = null;


    var configurarFormulario = function () {

        $('select#lectura_reloj_reloj').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#lectura_reloj_tipolectura').select2({
            dropdownParent: $("#basicmodal"),
        });

        $('input#lectura_reloj_fecha').datetimepicker();

        $("div#basicmodal form").validate({
            rules: {
                'lectura_reloj[reloj]': {required: true},
                'lectura_reloj[fecha]': {required: true},
                'lectura_reloj[lectura]': {required: true, min: 1},
                'lectura_reloj[tipolectura]': {required: true},
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
        table = $("table#lecturareloj_table").DataTable(
            {
                responsive: true,
                ajax: Routing.generate('lecturareloj_index'),
                "language": {
                    url: datatable_translation
                },
                columns: [
                    {data: "id"}, {data: "reloj"}, {data: "area"}, {data: "fecha"}, {data: "lectura"}, {data: "acciones"}
                ],
                columnDefs: [
                    {
                        targets: 3, title: " Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fecha.date).format('DD-MM-YYYY h:mm a');
                        }
                    }
                    , {
                        targets: -1, title: " ", orderable: !1, render: function (a, e, t, n) {
                            return ' <ul class="m-nav m-nav--inline m--pull-right">' +
                                '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm lecturareloj_show" data-href="' + Routing.generate('lecturareloj_show', {id: t.id}) + '"><i class="flaticon-eye"></i> VISUALIZAR</a></li>';
                        }
                    }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.lecturareloj_show', function (evento) {

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
        $('div#basicmodal').on('submit', 'form#lecturareloj_new', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(), //para enviar el formulario hay que serializarlo
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
                            "reloj": data['reloj'],
                            "area": data['area'],
                            "fecha": data['fecha'],
                            "lectura": data['lectura']
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
        $('div#basicmodal').on('click', 'a.eliminar_lecturareloj', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: "Eliminar lectura",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar esta lectura?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                            type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
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
                    edicion();
                    eliminar();
                }
            );
        }
    }
}();



