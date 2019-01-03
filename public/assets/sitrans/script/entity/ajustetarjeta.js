var ajustetarjeta = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#ajuste_tarjeta_tarjeta').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#ajuste_tarjeta_tipo').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });

        $('input#ajuste_tarjeta_fecha').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'ajuste_tarjeta[monto]': {required:true, min: 1},
                'ajuste_tarjeta[cantidadefectivo]': {required:true, min: 0.1},
                'ajuste_tarjeta[fecha]': {required:true},
                'ajuste_tarjeta[tarjeta]': {required:true},
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
        table = $("table#ajustetarjeta_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('ajustetarjeta_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"tarjeta"},{data:"fecha"},{data:"cantidadlitros"},{data:"cantidadefectivo"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: "Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fecha.date).format('DD-MM-YYYY h:mm a');
                        }
                    }
                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm ajustetarjeta_show" data-href="'+Routing.generate('ajustetarjeta_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>';
                    }
                }],

            });
    }

    var show = function () {
        $('body').on('click', 'a.ajustetarjeta_show', function (evento)
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
        $('div#basicmodal').on('submit', 'form#ajustetarjeta_new', function (evento)
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
                            "cantidadlitros": data['cantidadlitros'],
                            "cantidadefectivo": data['cantidadefectivo']
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
        $('div#basicmodal').on('click', 'a.eliminar_ajustetarjeta', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

            setTimeout(function(){
                bootbox.confirm({
                    title: "Eliminar ajuste",
                    message: "<p>¿Está seguro que desea eliminar este ajuste?</p>",
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
                                // dataType: 'html', esta url se comentajustetarjeta porque lo k estamos mandando es un json y no un html plano
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
            },500);
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
                    authenticated.importeTarjeta('input#ajuste_tarjeta_monto', 'input#ajuste_tarjeta_fecha','select#ajuste_tarjeta_tarjeta','input#ajuste_tarjeta_cantefectivo');
                }
            );
        }
    }
}();



