var chip = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#chip_tarjeta').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#chip_moneda').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#chip_cupet').select2({
            dropdownParent: $("#basicmodal"),
        });

        $('input#chip_fecha').datetimepicker();

        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return parseInt(value) <= parseInt($(params).val());
            },'El importe debe ser menor o igual que el saldo inicial');

        $("div#basicmodal form").validate({
            rules:{
                'chip[tarjeta]': {required:true},
                'chip[cupet]': {required:true},
                'chip[numerocomprobante]': {required:true},
                'chip[fecha]': {required:true},
                'chip[importe]': {required:true, min:0.1,  greaterThan: "#chip_saldoinicial"},
                'chip[idfisico]': {required:true},
                'chip[idlogico]': {required:true},
                'chip[moneda]': {required:true},
                'chip[servicio]': {required:true},
                'chip[saldoinicial]': {required:true, min:1 },
                'chip[litrosextraidos]': {required:true, min:1 },
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
                            '<li class="m-nav__item"><a class="btn btn-metal text-uppercase m-btn m-btn--icon btn-sm chip_show" data-href="'+Routing.generate('chip_show',{id:t.id})+'"><i class="flaticon-eye"></i> Visualizar</a></li>';
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
        $('div#basicmodal').on('submit', 'form#chip_new', function (evento)
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
                bootbox.confirm({
                    title: "Eliminar chip",
                    message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este chip?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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

    var saldoInicialTarjeta= function() {
        $('div#basicmodal').on('change', 'select#chip_tarjeta', function (evento) {
            var value = $(this).val();
            if (value >= 1 && $('input#chip_fecha').val()!="")
                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: Routing.generate('tarjeta_cantidadefectivo',{'id':value}),
                    data: {
                        fecha: $('input#chip_fecha').val(),
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $('input#chip_saldoinicial').val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });

        $('div#basicmodal').on('change', 'input#chip_fecha', function (evento) {
            var value = $(this).val();
            var tarjeta = $('select#chip_tarjeta').val();
            if (tarjeta > 0)
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('tarjeta_cantidadefectivo',{'id':tarjeta}),
                    data: {
                        fecha: value,
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $('input#chip_saldoinicial').val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });
    }

    var importeTarjeta= function() {
        $('div#basicmodal').on('change', 'input#chip_importe', function (evento) {
            var importe = $(this).val();
            var fecha = $('input#chip_fecha').val();
            var tarjeta =  $('select#chip_tarjeta').val();
            if (importe >0 && fecha!="" && tarjeta>0)
                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: Routing.generate('preciocombustible_findbytarjeta'),
                    data: {
                        importe: importe,
                        fecha: fecha,
                        tarjeta:tarjeta
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $('input#chip_litrosextraidos').val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });

        $('div#basicmodal').on('change', 'input#chip_fecha', function (evento) {
            var fecha = $(this).val();
            var importe = $('input#chip_importe').val();
            var tarjeta =  $('select#chip_tarjeta').val();
            if (importe >0 && fecha!="" && tarjeta>0)
                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: Routing.generate('preciocombustible_findbytarjeta'),
                    data: {
                        importe: importe,
                        fecha: fecha,
                        tarjeta: tarjeta
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor: "#000000", type: "loader", state: "success", message: "Cargando..."});
                    },
                    success: function (data) {
                        $('input#chip_litrosextraidos').val(data);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
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
                    saldoInicialTarjeta();
                    importeTarjeta();
                }
            );
        }
    }
}();



