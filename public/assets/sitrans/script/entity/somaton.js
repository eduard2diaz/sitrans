var somaton = function () {
    var table = null;
    var obj = null;


    var configurarFormulario=function(){
        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return moment(value)> moment($(params).val());
            },'Tiene que ser superior a la fecha de inicio');

        $('select#somaton_vehiculo').select2({
            dropdownParent: $("#basicmodal"),
        });

        $('input#somaton_fechainicio').datetimepicker();
        $('input#somaton_fechafin').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'somaton[vehiculo]': {required:true},
                'somaton[descripcion]': {required:true},
                'somaton[fechainicio]': {required:true},
                'somaton[fechafin]': {required:true, greaterThan: "#somaton_fechainicio"},
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
        table = $("table#somaton_table").DataTable(
            {
                responsive:true,
                ajax: Routing.generate('somaton_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"vehiculo"},{data:"fechainicio"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: " Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fechainicio.date).format('DD-MM-YYYY');
                        }
                    }
                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item">' +
                            '<a class="btn btn-metal m-btn m-btn--icon btn-sm somaton_show" data-href="'+Routing.generate('somaton_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li>' +
                            '<li class="m-nav__item"><a class="m--font-boldest btn btn-danger m-btn m-btn--icon btn-sm eliminar_somaton" data-href="'+Routing.generate('somaton_delete',{id:t.id})+'"><i class="flaticon-delete-1"></i> ELIMINAR</a></li>' +
                            '</ul>';
                    }
                }],

            });
    }


    var show = function () {
        $('body').on('click', 'a.somaton_show', function (evento)
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

    var nuevo = function () {
        $('body').on('click', 'a#somaton_new', function (evento)
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
        $('div#basicmodal').on('submit', 'form#somaton_new', function (evento)
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
                            "vehiculo": data['vehiculo'],
                            "fechainicio": data['fechainicio'],
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
        $('table#somaton_table').on('click', 'a.eliminar_somaton', function (evento)
        {
            evento.preventDefault();
            var obj = $(this);
            var link = $(this).attr('data-href');


                bootbox.confirm({
                    title: "Eliminar somatón",
                    message: "<div class='text-justify'><p class='confirm_message'>¿Está seguro que desea eliminar este somatón?</p><p class='confirm_detail'>Esta acción no se podrá deshacer</p></div>",
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
                    show();
                    nuevo();
                    eliminar();

                }
            );
        }
    }
}();



