var planefectivo = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#planefectivo_mes').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#planefectivo_anno').select2({
            dropdownParent: $("#basicmodal"),
        });

        $("div#basicmodal form").validate({
            rules:{
                'planefectivo[mes]': {required:true},
                'planefectivo[anno]': {required:true},
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
        table = $("table#planefectivo_table").DataTable(
            {
                responsive:true,
                ajax: Routing.generate('planefectivo_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"anno"},{data:"mes"},{data:"acciones"}
                ],
                columnDefs:[
                    {targets:2,title:"Mes",orderable:!1,render:function(a,e,t,n){
                            var meses=new Array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
                            return meses[t.mes-1];
                        }
                    },
                    {targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal text-uppercase m-btn m-btn--icon btn-sm" href="'+Routing.generate('planefectivo_show',{id:t.id})+'"><i class="flaticon-eye"></i> Visualizar</a></li>' +
                            '<li class="m-nav__item"><a class=" m--font-boldest btn btn-danger text-uppercase m-btn m-btn--icon btn-sm eliminar_planefectivo" data-href="'+Routing.generate('planefectivo_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> Eliminar</a></li>\n '}
                }]
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
        $('div#basicmodal').on('submit', 'form#planefectivo_new', function (evento)
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
                            "anno": data['anno'],
                            "mes": data['mes'],
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
        $('table#planefectivo_table').on('click', 'a.eliminar_planefectivo', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');

           bootbox.confirm({
                title: "Eliminar plan de efectivo",
                message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este plan de efectivo?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                    eliminar();
                }
            );
        }
    }
}();