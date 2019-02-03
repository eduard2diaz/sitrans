var cierremestarjeta = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#cierre_mes_tarjeta_tarjeta').select2({
            dropdownParent: $("#basicmodal"),
            allowClear: true,
            placeholder: {
                id: '-1', // the value of the option
                text: 'Selecione una tarjeta'
            }
        });

        $('input#cierre_mes_tarjeta_fecha').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'cierre_mes_tarjeta[tarjeta]': {required:true},
                'cierre_mes_tarjeta[fecha]': {required:true},
                'cierre_mes_tarjeta[combustibleconsumido]': {required:true, min: 0},
                'cierre_mes_tarjeta[efectivoconsumido]': {required:true, min: 0},
                'cierre_mes_tarjeta[restantecombustible]': {required:true, min: 0},
                'cierre_mes_tarjeta[restanteefectivo]': {required:true, min: 0},
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
        table = $("table#cierremestarjeta_table").DataTable(
            {
                responsive:true,
                ajax: Routing.generate('cierremestarjeta_index',{'id':cierremensual}),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"tarjeta"},{data:"fecha"},{data:"combustiblerestante"},{data:"efectivorestante"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: " Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(a.date).format('MM-DD-YYYY h:mm a');

                        }
                    },
                    {targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm cierremestarjeta_show" data-href="'+Routing.generate('cierremestarjeta_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('cierremestarjeta_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_cierremestarjeta" data-href="'+Routing.generate('cierremestarjeta_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '
                }
                }]
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
        $('div#basicmodal').on('submit', 'form#cierremestarjeta_new', function (evento)
        {
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
                            "tarjeta": data['tarjeta'],
                            "fecha": data['fecha'],
                            "combustiblerestante": data['combustiblerestante'],
                            "efectivorestante": data['efectivorestante'],
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
        $('div#basicmodal').on('submit', 'form#cierremestarjeta_edit', function (evento)
        {
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
                        configurarFormulario();
                    }
                    else
                    {
                       if (data['mensaje'])
                           toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        obj.parents('tr').children('td:nth-child(2)').html(data['tarjeta']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['fecha']);
                        obj.parents('tr').children('td:nth-child(4)').html(data['combustiblerestante']);
                        obj.parents('tr').children('td:nth-child(5)').html(data['efectivorestante']);
                    }
                },
                error: function ()
                {
                    base.Error();
                }
            });
        });
    }

    var show = function () {
        $('body').on('click', 'a.cierremestarjeta_show', function (evento)
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

    var ajax = function () {
        $('div#basicmodal').on('change', 'select#cierre_mes_tarjeta_tarjeta', function (evento)
        {
            evento.preventDefault();
            if($(this).val()>0)
            {
            var link = Routing.generate('cierremestarjeta_ajax',{'cierre':cierremensual,'tarjeta':$(this).val(),'_format':'json'});
            $.ajax({
                type: 'get',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                success: function (data) {
                        $('input#cierre_mes_tarjeta_restantecombustible').val(data.restante.litros);
                        $('input#cierre_mes_tarjeta_restanteefectivo').val(data.restante.efectivo);
                        $('input#cierre_mes_tarjeta_combustibleconsumido').val(data.consumido[0].litros);
                        $('input#cierre_mes_tarjeta_efectivoconsumido').val(data.consumido[0].efectivo);

                },
                error: function ()
                {
                     base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        }
        });
    }

    var cierretarjetaAutomatico = function () {
        $('body').on('click', 'a#cierretarjeta_automatico', function (evento)
        {

            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Generando cierre..."});
                },
                success: function (data) {
                    toastr.success(data['mensaje']);
                    document.location.reload();
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


    var eliminar = function () {
        $('table#cierremestarjeta_table').on('click', 'a.eliminar_cierremestarjeta', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Eliminar cierre",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este cierre?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
                buttons: {
                    confirm: {
                        label: 'Sí, estoy seguro',
                        className: 'btn btn-primary btn-sm'},
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
                    show();
                    newAction();
                    edicion();
                    cierretarjetaAutomatico();
                    edicionAction();
                    ajax();
                    eliminar();
                }
            );
        }
    }
}();



