var subelemento = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#subelemento_partida').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#subelemento_elemento').select2({
            dropdownParent: $("#basicmodal"),
        });

        $("div#basicmodal form").validate({
            rules:{
                'subelemento[nombre]': {required:true},
                'subelemento[codigo]': {required:true, number: true, min: 0},
                'subelemento[partida]': {required:true},
                'subelemento[elemento]': {required:true},
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
        table = $("table#subelemento_table").DataTable(
            {
                responsive:true,
                ajax: Routing.generate('subelemento_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"nombre"},{data:"codigo"},{data:"acciones"}
                ],
                columnDefs:[{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm text-uppercase subelemento_show" data-href="'+Routing.generate('subelemento_show',{id:t.id})+'"><i class="flaticon-eye"></i> Visualizar</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm text-uppercase edicion" data-href="'+Routing.generate('subelemento_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> Editar</a></li>';
                }
                }]
            });
    }

    var elementoListener = function () {
        $('div#basicmodal').on('change', 'select#subelemento_partida', function (evento)
        {

            if ($(this).val() > 0)
                $.ajax({
                    type: 'get',
                    dataType: 'html',
                    url: Routing.generate('elemento_searchbypartida', {'partida': $(this).val()}),
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando elementos..."});
                    },
                    success: function (data) {
                        var cadena="";
                        var array=JSON.parse(data);
                        for(var i=0;i<array.length;i++)
                            cadena+="<option value="+array[i]['id']+">"+array[i]['nombre']+"</option>";
                        $('select#subelemento_elemento').html(cadena);
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

    var show = function () {
        $('body').on('click', 'a.subelemento_show', function (evento)
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

    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#subelemento_new', function (evento)
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
                            "nombre": data['nombre'],
                            "codigo": data['codigo'],
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
        $('div#basicmodal').on('submit', 'form#subelemento_edit', function (evento)
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
                        obj.parents('tr').children('td:nth-child(2)').html(data['nombre']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['codigo']);
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
        $('div#basicmodal').on('click', 'a.eliminar_subelemento', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $('div#basicmodal').modal('hide');

           bootbox.confirm({
                title: "Eliminar subelemento",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este subelemento?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                    elementoListener();
                    show();
                    newAction();
                    edicion();
                    edicionAction();
                    eliminar();
                }
            );
        }
    }
}();



