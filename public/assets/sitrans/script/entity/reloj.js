var reloj = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#reloj_area').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $("div#basicmodal form").validate({
            rules:{
                'reloj[nombre]': {required:true},
                'reloj[area]': {required:true},
            }
        });
    }
    var configurarDataTable = function () {
        table = $("table#reloj_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('reloj_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"codigo"},{data:"area"},{data:"activo"},{data:"acciones"}
                ],
                columnDefs:[
                    {targets:-2,title:"Activo",orderable:1,render:function(a,e,t,n){
                            return colorear(t.activo);
                        }},


                    {targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm reloj_show" data-href="'+Routing.generate('reloj_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('reloj_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_reloj" data-href="'+Routing.generate('reloj_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '}
                }]
            });
    }

    var show = function () {
        $('body').on('click', 'a.reloj_show', function (evento)
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
                   // base.Error();
                },
                complete: function () {
                    mApp.unblock("body")
                }
            });
        });
    }



    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#reloj_new', function (evento)
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
                            "codigo": data['codigo'],
                            "area": data['area'],
                            "activo": data['activo'],
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
        $('div#basicmodal').on('submit', 'form#reloj_edit', function (evento)
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
                        obj.parents('tr').children('td:nth-child(2)').html(data['codigo']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['area']);
                        obj.parents('tr').children('td:nth-child(4)').html(colorear(data['activo']));
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
        $('table#reloj_table').on('click', 'a.eliminar_reloj', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Desea eliminar este reloj?",
                message: "<p>¿Está seguro que desea eliminar este reloj?</p>",
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
                            // dataType: 'html', esta url se comentreloj porque lo k estamos mandando es un json y no un html plano
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



