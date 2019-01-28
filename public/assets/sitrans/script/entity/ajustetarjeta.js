var ajustetarjeta = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#ajuste_tarjeta_tarjeta').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#ajuste_tarjeta_tipo').select2({
            dropdownParent: $("#basicmodal"),
        });

        $('input#ajuste_tarjeta_fecha').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
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
                ajax: Routing.generate('ajustetarjeta_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"tarjeta"},{data:"fecha"},{data:"cantidadefectivo"},{data:"acciones"}
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
        $('div#basicmodal').on('submit', 'form#ajustetarjeta_new', function (evento)
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
                            "fecha": data['fecha'],
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

                bootbox.confirm({
                    title: "Eliminar ajuste",
                    message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este ajuste?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                    edicion();
                    show();
                    eliminar();
                }
            );
        }
    }
}();



