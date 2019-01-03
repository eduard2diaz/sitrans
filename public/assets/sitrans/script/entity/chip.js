var chip = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#chip_tarjeta').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#chip_moneda').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#chip_cupet').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });

        $('input#chip_fecha').datetimepicker();

        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return parseInt(value) <= parseInt($(params).val());
            },'Los litros extraídos deben ser menor o igual al saldo inicial');

        $("div#basicmodal form").validate({
            rules:{
                'chip[tarjeta]': {required:true},
                'chip[cupet]': {required:true},
                'chip[numerocomprobante]': {required:true},
                'chip[fecha]': {required:true},
                'chip[importe]': {required:true, min:0.1},
                'chip[idfisico]': {required:true},
                'chip[idlogico]': {required:true},
                'chip[moneda]': {required:true},
                'chip[servicio]': {required:true},
                'chip[saldoinicial]': {required:true, min:1 },
                'chip[litrosextraidos]': {required:true, min:1,  greaterThan: "#chip_saldoinicial" },
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
        table = $("table#chip_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('chip_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"tarjeta"},{data:"fecha"},{data:"idfisico"},{data:"idlogico"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: " Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fecha.date).format('DD-MM-YYYY h:mm a');

                        }
                    }
                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm chip_show" data-href="'+Routing.generate('chip_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>';
                    }
                }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.chip_show', function (evento)
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
        $('div#basicmodal').on('submit', 'form#chip_new', function (evento)
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
                            "idfisico": data['idfisico'],
                            "fecha": data['fecha'],
                            "idlogico": data['idlogico'],
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
        $('div#basicmodal').on('click', 'a.eliminar_chip', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');
            setTimeout(function(){
                bootbox.confirm({
                    title: "Eliminar chip",
                    message: "<p>¿Está seguro que desea eliminar este chip?</p>",
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
                                // dataType: 'html', esta url se comentchip porque lo k estamos mandando es un json y no un html plano
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
                    show();
                    edicion();
                    eliminar();
                    authenticated.importeTarjeta('input#chip_litrosextraidos', 'input#chip_fecha','select#chip_tarjeta','input#chip_importe');
                }
            );
        }
    }
}();



