var tarifakw = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('input#tarifa_kw_fecha').datepicker();

        $("div#basicmodal form").validate({
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
        table = $("table#tarifakw_table").DataTable(
            {
                responsive:true,
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
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm tarifakw_show" data-href="'+Routing.generate('tarifakw_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li></ul>'
                    }
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
            var padre="body";
            if($(this).parent('div#basicmodal'))
                padre='div#basicmodal';
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block(padre,
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
                    mApp.unblock(padre)
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

    var eliminar = function () {
        $('div#basicmodal').on('click', 'a.eliminar_tarifakw', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

           bootbox.confirm({
                title: "Eliminar tarifa de kilowatts",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar esta tarifa?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                '        <a class="btn btn-danger btn-sm eliminar_rangotarifa pull-right"><i class="flaticon flaticon-delete-1"></i></a>\n' +
                '    </td>\n' +
                '</tr>';
            cantidadrangos++;
            $('div#rangos table').append(datos);
        });
    }

    var eliminarRango = function () {
        $('div#basicmodal').on('click', 'a.eliminar_rangotarifa', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            obj.parents('tr').remove();
        });
    }

    return {
        init: function () {
            $().ready(function () {
                    configurarDataTable();
                    newAction();
                    edicion();
                    show();
                    eliminar();
                    agregarRango();
                    eliminarRango();
                }
            );
        }
    }
}();



