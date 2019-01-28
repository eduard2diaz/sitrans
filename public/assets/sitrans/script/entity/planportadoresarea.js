var planportadoresarea = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#planportadores_area_areas').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#planportadores_area_categoria').select2({
            dropdownParent: $("#basicmodal"),
        });


        $("div#basicmodal form").validate({
            rules:{
                'planportadores_area[areas][]': {required:true},
                'planportadores_area[categoria]': {required:true},
                'planportadores_area[valor]': {required:true, min: 1},
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
        table = $("table#planportadoresarea_table").DataTable(
            {
                responsive:true,
                ajax: Routing.generate('planportadoresarea_index',{'id':planportador}),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"area"},{data:"categoria"},{data:"valor"},{data:"acciones"}
                ],
                columnDefs:[
                    {targets:2,title:"Categoría",orderable:!1,render:function(a,e,t,n) {
                        categorias=new Array('Combustible','Electricidad');
                            return categorias[t.categoria];
                    }
                    },
                    {targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm text-uppercase planportadores_show" data-href="'+Routing.generate('planportadoresarea_show',{id:t.id})+'"><i class="flaticon-eye"></i> Visualizar</a></li>' +
                            '<li class="m-nav__item"><a class="btn btn-info m-btn m-btn--icon btn-sm text-uppercase edicion" data-href="'+Routing.generate('planportadoresarea_edit',{id:t.id})+'"><i class="flaticon-edit-1"></i> Editar</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm text-uppercase eliminar_portadores" data-href="'+Routing.generate('planportadoresarea_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> Eliminar</a></li>\n '}
                }]
            });
    }

    var show = function () {
        $('body').on('click', 'a.planportadores_show', function (evento)
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
        $('div#basicmodal').on('submit', 'form#planportadoresarea_new', function (evento)
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
        $('div#basicmodal').on('submit', 'form#planportadoresarea_edit', function (evento)
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
        $('table#planportadoresarea_table').on('click', 'a.eliminar_portadores', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Eliminar plan de portadores por área",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar el plan de portadores de esta área?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                    eliminar();
                }
            );
        }
    }
}();



