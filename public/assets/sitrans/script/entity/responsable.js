var responsable = function () {
    var table = null;
    var obj = null;
    var responsable_id = null;

    var configurarFormulario=function(){
        $('select#responsable_area').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#responsable_institucion').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('select#responsable_tarjetas').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });

        $("input#responsable_ci").maxlength({warningClass:"m-badge m-badge--warning m-badge--rounded m-badge--wide",limitReachedClass:"m-badge m-badge--success m-badge--rounded m-badge--wide",appendToParent:!0});

        $("div#basicmodal form").validate({
            rules:{
                'responsable[nombre]': {required:true},
                'responsable[apellidos]': {required:true},
                'responsable[ci]': {required:true, maxlength: 11},
                'responsable[area]': {required:true},
                'responsable[direccion]': {required:true},
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
        table = $("table#responsable_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('responsable_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"nombre"},{data:"apellidos"},{data:"ci"},{data:"area"},{data:"acciones"}
                ],
                columnDefs:[{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm responsable_show" data-href="'+Routing.generate('responsable_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm edicion" data-href="'+Routing.generate('responsable_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> EDITAR</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_responsable" data-href="'+Routing.generate('responsable_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>\n '}
                }]
            });
    }
    var institucionListener = function () {
        $('div#basicmodal').on('change', 'select#responsable_institucion', function (evento)
        {
            if ($(this).val() > 0)
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'html',
                    url: Routing.generate('area_findbyinstitucion', {'id': $(this).val()}),
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    success: function (data) {
                        var cadena="";
                        var array=JSON.parse(data);
                        for(var i=0;i<array.length;i++)
                            cadena+="<option value="+array[i]['id']+">"+array[i]['nombre']+"</option>";
                        $('select#responsable_area').html(cadena);
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
                    url: Routing.generate('tarjeta_findbyinstitucion', {'id': $(this).val()}),
                    data: {
                       responsable: responsable_id
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    success: function (data) {
                        var cadena="";
                        var array=JSON.parse(data);
                        for(var i=0;i<array.length;i++)
                            cadena+="<option value="+array[i]['id']+">"+array[i]['nombre']+"</option>";
                        $('select#responsable_tarjetas').html(cadena);
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
    var show = function () {
        $('body').on('click', 'a.responsable_show', function (evento)
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
            //    dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                success: function (data) {
                      if ($('div#basicmodal').html(data.html)) {
                          configurarFormulario();
                         $('div#basicmodal').modal('show');
                         if(data.responsable)
                             responsable_id=data.responsable;
                         else
                             responsable_id=null;
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
        $('div#basicmodal').on('submit', 'form#responsable_new', function (evento)
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
                            "apellidos": data['apellidos'],
                            "ci": data['ci'],
                            "area": data['area'],
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
        $('div#basicmodal').on('submit', 'form#responsable_edit', function (evento)
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
                    mApp.unblock("body")
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
                        obj.parents('tr').children('td:nth-child(3)').html(data['apellidos']);
                        obj.parents('tr').children('td:nth-child(4)').html(data['ci']);
                        obj.parents('tr').children('td:nth-child(5)').html(data['area']);
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
        $('table#responsable_table').on('click', 'a.eliminar_responsable', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Eliminar responsable",
                message: "<p>¿Está seguro que desea eliminar este responsable?</p>",
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
                            // dataType: 'html', esta url se comentresponsable porque lo k estamos mandando es un json y no un html plano
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
                    institucionListener();
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



