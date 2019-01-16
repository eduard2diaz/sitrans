var pruebalitro = function () {
    var table = null;
    var obj = null;


    var configurarFormulario=function(){
        $('select#pruebalitro_vehiculo').select2({
      //      dropdownParent: $("#basicmodal"),
            //allowClear: true
        });

        $('input#pruebalitro_fecha').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'pruebalitro[vehiculo]': {required:true},
                'pruebalitro[kmrecorrido]': {required:true, min: 0.5},
                'pruebalitro[fecha]': {required:true},
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
        table = $("table#pruebalitro_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('pruebalitro_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"vehiculo"},{data:"fecha"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: " Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fecha.date).format('DD-MM-YYYY h:mm a');
                        }
                    }
                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item">' +
                            '<a class="btn btn-metal m-btn m-btn--icon btn-sm pruebalitro_show" data-href="'+Routing.generate('pruebalitro_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_pruebalitro" data-href="'+Routing.generate('pruebalitro_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>' +
                            '</ul>';
                    }
                }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.pruebalitro_show', function (evento)
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

    var nuevo = function () {
        $('body').on('click', 'a#pruebalitro_new', function (evento)
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
                   // base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        });
    }

    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#pruebalitro_new', function (evento)
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
        $('table#pruebalitro_table').on('click', 'a.eliminar_pruebalitro', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

            setTimeout(function(){
                bootbox.confirm({
                    title: "Eliminar prueba de litro",
                    message: "<p>¿Está seguro que desea eliminar esta prueba de litro?</p>",
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
                                // dataType: 'html', esta url se comentpruebalitro porque lo k estamos mandando es un json y no un html plano
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
                    nuevo();
                    eliminar();

                }
            );
        }
    }
}();



