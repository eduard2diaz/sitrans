var planefectivocuenta = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#planefectivo_cuenta_subelemento').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#planefectivo_cuenta_centrocosto').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#planefectivo_cuenta_cuenta').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });

        $("div#basicmodal form").validate({
            rules:{
                'planefectivo_cuenta[subelemento][]': {required:true},
                'planefectivo_cuenta[centrocosto][]': {required:true},
                'planefectivo_cuenta[cuenta]': {required:true},
                'planefectivo_cuenta[valor]': {required:true, min: 1},
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
        table = $("table#planefectivocuenta_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('planefectivocuenta_index',{'id':planefectivo}),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"cuenta"},{data:"centrocosto"},{data:"subelemento"},{data:"acciones"}
                ],
                columnDefs:[
                    {targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm planefectivo_show" data-href="'+Routing.generate('planefectivocuenta_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('planefectivocuenta_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_planefectivo" data-href="'+Routing.generate('planefectivocuenta_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '}
                }]
            });
    }

    var cuentaListener = function () {
        $('div#basicmodal').on('change', 'select#planefectivo_cuenta_cuenta', function (evento)
        {

            if ($(this).val() > 0){
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('subelemento_searchbycuenta', {'cuenta': $(this).val()}),
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    success: function (data) {
                        var cadena="";
                        var array=JSON.parse(data);
                        for(var i=0;i<array.length;i++)
                            cadena+="<option value="+array[i]['id']+">"+array[i]['nombre']+"</option>";
                        $('select#planefectivo_cuenta_subelemento').html(cadena);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });

            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: Routing.generate('centrocosto_searchbycuenta', {'cuenta': $(this).val()}),
                beforeSend: function (data) {
                    mApp.block("div#basicmodal div.modal-body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                success: function (data) {
                    var cadena="";
                    var array=JSON.parse(data);
                    for(var i=0;i<array.length;i++)
                        cadena+="<option value="+array[i]['id']+">"+array[i]['nombre']+"</option>";
                    $('select#planefectivo_cuenta_centrocosto').html(cadena);
                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                    mApp.unblock("div#basicmodal div.modal-body")
                }
            });
            }
        });
    }


    var show = function () {
        $('body').on('click', 'a.planefectivo_show', function (evento)
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
        $('div#basicmodal').on('submit', 'form#planefectivocuenta_new', function (evento)
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
                        document.location.reload();
                },
                error: function ()
                {
                    base.Error();
                }
            });
        });
    }

    var edicionAction = function () {
        $('div#basicmodal').on('submit', 'form#planefectivocuenta_edit', function (evento)
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
                        document.location.reload();
                },
                error: function ()
                {
                    base.Error();
                }
            });
        });
    }
    var eliminar = function () {
        $('table#planefectivocuenta_table').on('click', 'a.eliminar_planefectivo', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Eliminar plan de efectivo por cuenta",
                message: "<p>¿Está seguro que desea eliminar el plan de efectivo para esta cuenta?</p>",
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
                            // dataType: 'html', esta url se comentplanefectivo porque lo k estamos mandando es un json y no un html plano
                            url: link,
                            beforeSend: function () {
                                mApp.block("body",
                                    {overlayColor:"#000000",type:"loader",state:"success",message:"Eliminando..."});
                            },
                            complete: function () {
                                mApp.unblock("body")
                            },
                            success: function (data) {
                                document.location.reload();
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
                    edicionAction();
                    cuentaListener();
                    eliminar();
                }
            );
        }
    }
}();



