var hojaruta = function () {
    var table = null;
    var obj = null;


    var configurarFormulario=function(){
        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return moment(value)> moment($(params).val());
            },'Tiene que ser superior a la fecha de salida');

        $('select#hojaruta_vehiculo').select2({
      //      dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#hojaruta_tipoactividad').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });

        $('input#hojaruta_fechasalida').datetimepicker();
        $('input#hojaruta_fechallegada').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'hojaruta[vehiculo]': {required:true},
                'hojaruta[tipoactividad]': {required:true},
                'hojaruta[descripcion]': {required:true},
                'hojaruta[litrosconsumidos]': {required:true, min: 1},
                'hojaruta[importe]': {required:true, min: 0.1},
                'hojaruta[kmrecorrido]': {required:true, min: 1},
                'hojaruta[codigo]': {required:true},
                'hojaruta[origen]': {required:true},
                'hojaruta[destino]': {required:true},
                'hojaruta[fechasalida]': {required:true},
                'hojaruta[fechallegada]': {required:true, greaterThan: "#hojaruta_fechasalida" },
            }
        });

    }
    var configurarDataTable = function () {
        table = $("table#hojaruta_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('hojaruta_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"vehiculo"},{data:"codigo"},{data:"fechasalida"},{data:"fechallegada"},{data:"acciones"}
                ],
                columnDefs:[
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

                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm hojaruta_show" data-href="'+Routing.generate('hojaruta_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('hojaruta_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_hojaruta" data-href="'+Routing.generate('hojaruta_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '}
                }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.hojaruta_show', function (evento)
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
                   // base.Error();
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
                          authenticated.importeVehiculo('input#hojaruta_litrosconsumidos', 'input#hojaruta_fechasalida','select#hojaruta_vehiculo','input#hojaruta_importe');
                         $('div#basicmodal').modal('show');
                    }
                },
                error: function ()
                {
                   // base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        });
    }

    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#hojaruta_new', function (evento)
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
                error: function ()
                {
                    base.Error();
                }
            });
        });
    }

    var edicionAction = function () {
        $('div#basicmodal').on('submit', 'form#hojaruta_edit', function (evento)
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
                        obj.parents('tr').children('td:nth-child(2)').html(data['vehiculo']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['codigo']);
                        obj.parents('tr').children('td:nth-child(4)').html(data['fechasalida']);
                        obj.parents('tr').children('td:nth-child(5)').html(data['fechallegada']);
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
        $('table#hojaruta_table').on('click', 'a.eliminar_hojaruta', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Desea eliminar esta hoja de ruta?",
                message: "<p>¿Está seguro que desea eliminar esta hoja de ruta?</p>",
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
                            // dataType: 'html', esta url se comenthojaruta porque lo k estamos mandando es un json y no un html plano
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



