var tarifakw = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('input#tarifa_kw_fecha').datepicker();

        $("div#basicmodal form").validate();
    }
    var configurarDataTable = function () {
        table = $("table#tarifakw_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('tarifakw_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"fecha"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets:1, title: "Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fecha.date).format('DD-MM-YYYY');
                        }
                    }
                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm tarifakw_show" data-href="'+Routing.generate('tarifakw_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('tarifakw_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_tarifakw" data-href="'+Routing.generate('tarifakw_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '}
                }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.tarifakw_show', function (evento)
        {

            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
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
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
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
        $('div#basicmodal').on('submit', 'form#tarifakw_new', function (evento)
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
                            "fecha": data['fecha'],
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
        $('div#basicmodal').on('submit', 'form#tarifakw_edit', function (evento)
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
                        obj.parents('tr').children('td:nth-child(2)').html(data['fecha']);
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
        $('table#tarifakw_table').on('click', 'a.eliminar_tarifakw', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Desea eliminar esta tarifa?",
                message: "<p>¿Está seguro que desea eliminar esta tarifa?</p>",
                buttons: {
                    confirm: {
                        label: 'Sí, estoy seguro',
                        className: 'btn btn-primary'},
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn btn-metal'}
                },
                callback: function (result) {
                    if (result == true)
                        $.ajax({
                            type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                            // dataType: 'html', esta url se comenttarifakw porque lo k estamos mandando es un json y no un html plano
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

    var agregarRango = function () {
        $('div#basicmodal').on('click', 'a#adicionar_rango', function (evento)
        {
            evento.preventDefault();
            var datos='<tr>\n' +
                '    <td>\n' +
                '            <input id="tarifa_kw_rangoTarifaKws_'+cantidadrangos+'_inicio" name="tarifa_kw[rangoTarifaKws]['+cantidadrangos+'][inicio]" required="required" class="form-control" aria-describedby="tarifa_kw_rangoTarifaKws_'+cantidadrangos+'_inicio-error" aria-invalid="false" type="number"><div id="tarifa_kw_rangoTarifaKws_'+cantidadrangos+'_inicio-error" class="form-control-feedback"></td>\n' +
                '    <td>    \n' +
                '            <input id="tarifa_kw_rangoTarifaKws_'+cantidadrangos+'_fin" name="tarifa_kw[rangoTarifaKws]['+cantidadrangos+'][fin]" class="form-control" type="number"></td>\n' +
                '    <td>\n' +
                '            <input id="tarifa_kw_rangoTarifaKws_'+cantidadrangos+'_valor" name="tarifa_kw[rangoTarifaKws]['+cantidadrangos+'][valor]" required="required" class="form-control" type="text">' +
                '</td>\n' +
                '    <td>\n' +
                '        <a class="btn btn-danger btn-sm eliminar_rangotarjeta pull-right" data-href=""><i class="flaticon flaticon-delete-1"></i></a>\n' +
                '    </td>\n' +
                '</tr>';
            cantidadrangos++;
            $('div#rangos table').append(datos);
        });
    }

    var eliminarRango = function () {
        $('div#basicmodal').on('click', 'a.eliminar_rangotarjeta', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

            bootbox.confirm({
                title: "Desea eliminar este rango?",
                message: "<p>¿Está seguro que desea eliminar este rango?</p>",
                buttons: {
                    confirm: {
                        label: 'Sí, estoy seguro',
                        className: 'btn btn-primary'},
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn btn-metal'}
                },
                callback: function (result) {
                    if (result == true)
                        if(link)
                        $.ajax({
                            type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                            // dataType: 'html', esta url se comenttarifakw porque lo k estamos mandando es un json y no un html plano
                            url: link,
                            beforeSend: function () {
                                mApp.block("body",
                                    {overlayColor:"#000000",type:"loader",state:"success",message:"Eliminando..."});
                            },
                            complete: function () {
                                mApp.unblock("body")
                            },
                            success: function (data) {
                                toastr.success(data['mensaje']);
                            },
                            error: function ()
                            {
                                base.Error();
                            }
                        });
                             obj.parents('tr').remove();
                }
            });
        });
    }

    return {
        init: function () {
            $().ready(function () {
                    configurarDataTable();
                    newAction();
                    edicion();
                    show();
                    edicionAction();
                    eliminar();
                    agregarRango();
                    eliminarRango();
                }
            );
        }
    }
}();



