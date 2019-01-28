var reparacion = function () {
    var table = null;
    var obj = null;


    var configurarFormulario=function(){
        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return moment(value)> moment($(params).val());
            },'Tiene que ser superior a la fecha de inicio');

        $('select#reparacion_vehiculo').select2({
            dropdownParent: $("#basicmodal"),
        });

        $('input#reparacion_fechainicio').datetimepicker();
        $('input#reparacion_fechafin').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'reparacion[vehiculo]': {required:true},
                'reparacion[fechainicio]': {required:true},
                'reparacion[fechafin]': {required:true, greaterThan: "#reparacion_fechainicio" },
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
        table = $("table#reparacion_table").DataTable(
            {
                responsive:true,
                ajax: Routing.generate('reparacion_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"vehiculo"},{data:"fechasinicio"},{data:"fechafin"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: " Fecha de inicio", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fechainicio.date).format('DD-MM-YYYY h:mm a');
                        }
                    },
                    {
                        targets: 3, title: " Fecha de fin", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fechafin.date).format('DD-MM-YYYY h:mm a');
                        }
                    }

                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm reparacion_show" data-href="'+Routing.generate('reparacion_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('reparacion_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_reparacion" data-href="'+Routing.generate('reparacion_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '}
                }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.reparacion_show', function (evento)
        {

            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                success: function (data) {
                      if ($('div#basicmodal').html(data)) {
                         $('div#basicmodal').modal('show');
                    }
                },
                error: function ()
                {
                    base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        });
    }

    var edicion = function () {
        $('body').on('click', 'a.edicion', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                success: function (data) {
                      if ($('div#basicmodal').html(data)) {
                          configurarFormulario();
                         $('div#basicmodal').modal('show');
                    }
                },
                error: function ()
                {
                   base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        });
    }

    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#reparacion_new', function (evento)
        {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector( '.ladda-button' ) );
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(),
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
                        configurarFormulario();
                    }
                    else
                    {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        objeto = table.row.add({
                            "id": data['id'],
                            "vehiculo": data['vehiculo'],
                            "fechainicio": data['fechainicio'],
                            "fechafin": data['fechafin'],
                        });
                        objeto.draw();
                        table.page(pagina).draw('page');
                    }
                },
                error: function ()
                {
                    base.Error();
                }
            });
        });
    }

    var edicionAction = function () {
        $('div#basicmodal').on('submit', 'form#reparacion_edit', function (evento)
        {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector( '.ladda-button' ) );
            l.start();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(),
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
                        configurarFormulario();
                    }
                    else
                    {
                       if (data['mensaje'])
                           toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        obj.parents('tr').children('td:nth-child(2)').html(data['vehiculo']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['fechainicio']);
                        obj.parents('tr').children('td:nth-child(4)').html(data['fechafin']);
                    }
                },
                error: function ()
                {
                    base.Error();
                }
            });
        });
    }
    var eliminar = function () {
        $('table#reparacion_table').on('click', 'a.eliminar_reparacion', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Eliminar reparación",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar esta reparación?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
                buttons: {
                    confirm: {
                        label: 'Sí, estoy seguro',
                        className: 'btn btn-primary btn-sm'},
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn btn-metal btn-sm'}
                },
                callback: function (result) {
                    if (result == true)
                        $.ajax({
                            type: 'get',
                            url: link,
                            beforeSend: function () {
                                mApp.block("body",
                                    {overlayColor:"#000000",type:"loader",state:"success",message:"Eliminando..."});
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
                            error: function ()
                            {
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
                    edicionAction();
                    eliminar();
                }
            );
        }
    }
}();



