var recargakw = function () {
    var table = null;
    var obj = null;

    var configurarFormulario=function(){
        $('select#recarga_kw_reloj').select2({
            dropdownParent: $("#basicmodal"),
            //allowClear: true
        });
        $('input#recarga_kw_fecha').datetimepicker();

        $("div#basicmodal form").validate({
            rules:{
                'recarga_kw[asignacion]': {required:true, min: 1},
                'recarga_kw[fecha]': {required:true},
                'recarga_kw[reloj]': {required:true},
                'recarga_kw[codigoSTS]': {required:true},
                'recarga_kw[folio00]': {required:true, min: 0},
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
        table = $("table#recargakw_table").DataTable(
            {
                responsive:true,
                //   searchDelay:500,
                //  processing:true,
                //    serverSide:true,
                ajax: Routing.generate('recargakw_index'),
                "language": {
                    url: datatable_translation
                },
                columns:[
                    {data:"id"},{data:"reloj"},{data:"fecha"},{data:"asignacion"},{data:"folio00"},{data:"acciones"}
                ],
                columnDefs:[
                    {
                        targets: 2, title: " Fecha", orderable: !1, render: function (a, e, t, n) {
                            return moment(t.fecha.date).format('DD-MM-YYYY h:mm a')

                        }
                    }
                    ,{targets:-1,title:" ",orderable:!1,render:function(a,e,t,n){
                        return' <ul class="m-nav m-nav--inline m--pull-right">'+
                            '<li class="m-nav__item"><a class="btn btn-metal m-btn m-btn--icon btn-sm recargakw_show" data-href="'+Routing.generate('recargakw_show',{id:t.id})+'"><i class="flaticon-eye"></i> VISUALIZAR</a></li></ul>';
                    }
                }],

            });
    }

    var relojListener = function () {
        $('div#basicmodal').on('change', 'select#recarga_kw_reloj', function (evento)
        {
            if ($(this).val() > 0 && $('input#recarga_kw_fecha').val()!="")
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    //dataType: 'html',
                    url: Routing.generate('recargakw_ajax'),
                    data: {
                        reloj: $('select#recarga_kw_reloj').val(),
                        fecha: $('input#recarga_kw_fecha').val(),
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    success: function (data) {
                        $('input#recarga_kw_folio00').val(data['restante']);
                    },
                    error: function () {
                        base.Error();
                    },
                    complete: function () {
                        mApp.unblock("div#basicmodal div.modal-body")
                    }
                });
        });

        $('div#basicmodal').on('change', 'input#recarga_kw_fecha', function (evento)
        {
            var value = $(this).val();
            if ($('select#recarga_kw_reloj').val()>0)
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    //dataType: 'html',
                    url: Routing.generate('recargakw_ajax'),
                    data: {
                        reloj: $('select#recarga_kw_reloj').val(),
                        fecha: value,
                    },
                    beforeSend: function (data) {
                        mApp.block("div#basicmodal div.modal-body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    success: function (data) {
                        $('input#recarga_kw_folio00').val(data['restante']);
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
        $('body').on('click', 'a.recargakw_show', function (evento)
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
         //   obj = $(this);
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
        $('div#basicmodal').on('submit', 'form#recargakw_new', function (evento)
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
                            "reloj": data['reloj'],
                            "fecha": data['fecha'],
                            "asignacion": data['asignacion'],
                            "folio00": data['folio00']
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
       $('div#basicmodal').on('click', 'a.eliminar_recargakw', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');

            $('div#basicmodal').modal('hide');

          setTimeout(function(){
              bootbox.confirm({
                  title: "Eliminar recarga de kilowatts",
                  message: "<p>¿Está seguro que desea eliminar esta recarga?</p>",
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
                              // dataType: 'html', esta url se comentrecargakw porque lo k estamos mandando es un json y no un html plano
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
                    relojListener();
                    edicion();
                    eliminar();
                }
            );
        }
    }
}();



