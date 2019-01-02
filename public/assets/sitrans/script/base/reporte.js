var reporte = function () {
    var ultimoreporte=null;
    //Inicio de la definicion de los formularios
    function createDateRangeForm(title,form_id, action){
        var dialog = bootbox.dialog({
                title: title,
                message: '<form id="'+form_id+'" class="daterange" action="'+action+'">' +
                '<div class="row">' +
                '<div class="col-md-6"><label for="finicio">Fecha de inicio</label>' +
                '<input type="text" class="form-control input-medium" id="finicio" name="finicio"/></div>' +
                '<div class="col-md-6"><label for="ffin">Fecha de fin</label>' +
                '<input type="text" class="form-control input-medium" id="ffin" name="ffin"/></div>' +
                '</div>' +
                '</form>',
                buttons: {
                    cancel: {
                        label: "Cancelar",
                        className: 'btn-metal',
                    },
                    noclose: {
                        label: "Enviar",
                        className: 'btn btn-primary',
                        callback: function(){
                            if ($('div.bootbox form.daterange').valid()) {
                                $('div.bootbox form.daterange').submit();
                            } else {
                                return false;
                            }
                        }
                    },
                }
            }
        );

        $('input#finicio').datepicker();
        $('input#ffin').datepicker();
        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return moment(value)> moment($(params).val());
            },'Tiene que ser superior a la fecha de salida');


        $("div.bootbox form.daterange").validate({
            rules:{
                'finicio': {required:true},
                'ffin': {required:true, greaterThan: "#finicio" },
            }
        });
    }

    function createVehiculoDateRangeForm(title,form_id, action){
        var dialog = bootbox.dialog({
                title: title,
                message: '<form id="'+form_id+'" class="daterange" action="'+action+'">' +
                '<div class="row">' +
                '<div class="col-md-6"><label for="vehiculo">Vehículo</label>' +
                '<select id="vehiculo" name="vehiculo">' +
                '</select></div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-6"><label for="finicio">Fecha de inicio</label>' +
                '<input type="text" class="form-control input-medium" id="finicio" name="finicio"/></div>' +
                '<div class="col-md-6"><label for="ffin">Fecha de fin</label>' +
                '<input type="text" class="form-control input-medium" id="ffin" name="ffin"/></div>' +
                '</div>' +
                '</form>',
                buttons: {
                    cancel: {
                        label: "Cancelar",
                        className: 'btn-metal',
                    },
                    noclose: {
                        label: "Enviar",
                        className: 'btn btn-primary',
                        callback: function(){
                            if ($('div.bootbox form.daterange').valid()) {
                                $('div.bootbox form.daterange').submit();
                            } else {
                                return false;
                            }
                        }
                    },
                }
            }
        );

        $('input#finicio').datepicker();
        $('input#ffin').datepicker();
        $('select#vehiculo').select2({
                  dropdownParent: $("div.bootbox"),
            //allowClear: true
        });

        $.ajax({
            url: Routing.generate('vehiculo_activos'),
            type: "GET",
            data: $(this).serialize(), //para enviar el formulario hay que serializarlo
            beforeSend: function () {
                mApp.block("body",
                    {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando vehículos activos..."});
            },
            complete: function () {
                mApp.unblock("body");
            },
            success: function (data) {
                {
                    $('select#vehiculo').html(data);
                }
            },
            error: function ()
            {
                base.Error();
            }
        });

        jQuery.validator.addMethod("greaterThan",
            function(value, element, params) {
                return moment(value)> moment($(params).val());
            },'Tiene que ser superior a la fecha de salida');


        $("div.bootbox form.daterange").validate({
            rules:{
                'finicio': {required:true},
                'ffin': {required:true, greaterThan: "#finicio" },
                'vehiculo': {required:true},
            }
        });
    }

    function createPortadoresForm(title,form_id, action){
        var dialog = bootbox.dialog({
                title: title,
                message: '<form id="'+form_id+'" class="portadores" action="'+action+'">' +
                '<div class="row">' +
                '<div class="col-md-6"><label for="anno">Año</label>' +
                '<input type="text" class="form-control input-medium" id="anno" name="anno"/></div>' +
                '<div class="col-md-6"><label for="categoria">Categoría</label>' +
                '<select id="categoria" name="categoria">' +
                '<option value="0">Combustible</option>' +
                '<option value="1">Electricidad</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '</form>',
                buttons: {
                    cancel: {
                        label: "Cancelar",
                        className: 'btn-metal',
                    },
                    noclose: {
                        label: "Enviar",
                        className: 'btn btn-primary',
                        callback: function(){
                            if ($('div.bootbox form.portadores').valid()) {
                                $('div.bootbox form.portadores').submit();
                            } else {
                                return false;
                            }
                        }
                    },
                }
            }
        );
        $('select#categoria').select2({
                  dropdownParent: $("div.bootbox"),
            //allowClear: true
        });
        $("div.bootbox form.portadores").validate({
            rules:{
                'anno': {required:true, min:1980},
                'categoria': {required:true, min:0, max:1 },
            }
        });
    }

    function createYearForm(title,form_id, action){
        var dialog = bootbox.dialog({
                title: title,
                message: '<form id="'+form_id+'" class="year" action="'+action+'">' +
                '<div class="row">' +
                '<div class="col-md-6"><label for="anno">Año</label>' +
                '<input type="text" class="form-control input-medium" id="anno" name="anno"/></div>' +
                '</div>' +
                '</div>' +
                '</form>',
                buttons: {
                    cancel: {
                        label: "Cancelar",
                        className: 'btn-metal',
                    },
                    noclose: {
                        label: "Enviar",
                        className: 'btn btn-primary',
                        callback: function(){
                            if ($('div.bootbox form.year').valid()) {
                                $('div.bootbox form.year').submit();
                            } else {
                                return false;
                            }
                        }
                    },
                }
            }
        );
        $("div.bootbox form.year").validate({
            rules:{
                'anno': {required:true, min:1980},
            }
        });
    }

    function createMonthYearForm(title,form_id, action){
        var dialog = bootbox.dialog({
                title: title,
                message: '<form id="'+form_id+'" class="monthyear" action="'+action+'">' +
                '<div class="row">' +
                '<div class="col-md-6"><label for="anno">Año</label>' +
                '<input type="text" class="form-control input-medium" id="anno" name="anno"/></div>' +
                '<div class="col-md-6"><label for="mes">Mes</label>' +
                '<select id="mes" name="mes">'+
                '<option value="1">Enero</option>'+
                '<option value="2">Febrero</option>'+
                '<option value="3">Marzo</option>'+
                '<option value="4">Abril</option>'+
                '<option value="5">Mayo</option>'+
                '<option value="6">Junio</option>'+
                '<option value="7">Julio</option>'+
                '<option value="8">Agosto</option>'+
                '<option value="9">Septiembre</option>'+
                '<option value="10">Octubre</option>'+
                '<option value="11">Noviembre</option>'+
                '<option value="12">Diciembre</option>'+
                '</select>' +
                '</div>' +
                '</div>' +
                '</form>',
                buttons: {
                    cancel: {
                        label: "Cancelar",
                        className: 'btn-metal',
                    },
                    noclose: {
                        label: "Enviar",
                        className: 'btn btn-primary',
                        callback: function(){
                            if ($('div.bootbox form.monthyear').valid()) {
                                $('div.bootbox form.monthyear').submit();
                            } else {
                                return false;
                            }
                        }
                    },
                }
            }
        );

        $('select#mes').select2({
                  dropdownParent: $("div.bootbox"),
            //allowClear: true
        });

        $("div.bootbox form.monthyear").validate({
            rules:{
                'anno': {required:true, min:1980},
                'mes': {required:true, min:1,max:12},
            }
        });
    }
    //Fin de la definicion de los formularios

    function exportar(title){
        if(!title)
            title='Reporte';

        var doc = new jsPDF();
        doc.text(title, 14, 16);
        var elem = document.getElementById("jspdf_content");
        var res = doc.autoTableHtmlToJson(elem);
        doc.autoTable(res.columns, res.data, {startY: 20});
        doc.save(title);
        return doc;
    }

    //Inicio de los reporte del consumo y los kms
    var kmConsumoReportLink=function() {
        $('body').on('click', 'a#kmconsumoreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('kmconsumo_report');
            var form_id = 'kmconsumoreport';
            var title = 'Consumo kilómetros y litros';
            createDateRangeForm(title,form_id,link);
        });
    }

    var kmConsumoReportAction = function () {
        $('body').on('submit', 'form#kmconsumoreport', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data= $(this).serialize();

            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        {
                            if($('div#extramodal').html(data.html)) {
                                $("div#extramodal table").DataTable(
                                    {
                                        "language": {
                                            url: datatable_translation
                                        },
                                    }
                                );
                                $('div#extramodal').modal('show');
                                $('div.alert-notification').fadeIn(3000);
                                ultimoreporte=data.pdf;
                            }
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500)

        });
    }
    //Fin del reporte del consumo y los kms

    //Inicio de diferencia de consumo
    var diferenciaConsumoReportLink=function() {
        $('body').on('click', 'a#diferenciaconsumoreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('diferenciaconsumo_report');
            var form_id = 'diferenciaconsumoreport';
            var title = 'Diferencia de consumo';
            createDateRangeForm(title,form_id,link);
        });
    }

    var diferenciaConsumoReportAction = function () {
        $('body').on('submit', 'form#diferenciaconsumoreport', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if( $('div#extramodal').html(data.html)) {
                            $("div#extramodal table").DataTable(
                                {
                                    "language": {
                                        url: datatable_translation
                                    },
                                }
                            );
                            ultimoreporte=data.pdf;
                            $('div#extramodal').modal('show');
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);

        });
    }
    //Fin de diferencia de consumo

    //inicio de remanente actual
    var remanenteActualReport = function () {
        $('body').on('click', 'a#remanente_actual', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                url: link,
                beforeSend: function () {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                complete: function () {
                    mApp.unblock("body");
                },
                success: function (data) {
                    if ($('div#extramodal').html(data.html)) {
                        $("div#extramodal table").DataTable(
                            {
                                "language": {
                                    url: datatable_translation
                                },
                            }
                        );
                        $('div#extramodal').modal('show');
                        ultimoreporte=data.pdf;
                    }
                },
                error: function () {
                    base.Error();
                }
            });
        });
    }
    //fin de remanente actual

    //Inicio de porciento de violacion link
    var porcientoDesviacionReportLink=function() {
        $('body').on('click', 'a#porcientodesviacionreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('porcientodesviacion_report');
            var form_id = 'porcientodesviacionreport';
            var title = 'Porciento de desviación';
            createDateRangeForm(title,form_id,link);
        });
    }

    var porcientoDesviacionReportAction = function () {
        $('body').on('submit', 'form#porcientodesviacionreport', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if( $('div#extramodal').html(data.html)) {
                            $("div#extramodal table").DataTable(
                                {
                                    "language": {
                                        url: datatable_translation
                                    },
                                }
                            );
                            $('div#extramodal').modal('show');
                            ultimoreporte = data.pdf;
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);

        });
    }
    //fin de porciento de desviacion

    //Inicio de pendiente a mantenimiento
    var pendienteMantenimiento = function () {
        $('body').on('click', 'a#pendiente_mantenimiento', function (evento)
        {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                url: link,
                beforeSend: function () {
                    mApp.block("body",
                        {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                },
                complete: function () {
                    mApp.unblock("body");
                },
                success: function (data) {
                    if ($('div#extramodal').html(data.html)) {
                        $("div#extramodal table").DataTable(
                            {
                                "language": {
                                    url: datatable_translation
                                },
                            }
                        );
                        $('div#extramodal').modal('show');
                        ultimoreporte=data.pdf;
                    }
                },
                error: function () {
                    base.Error();
                },
            });
        });
    }
    //Fin de pendiente a mantenimiento

    //Inicio de kw consumo
    var kwConsumoAreaReportLink=function() {
        $('body').on('click', 'a#kwconsumoareareport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('kwconsumoarea_report');
            var form_id = 'kwconsumoarea_report';
            var title = 'Consumo de kilowatts por área';
            createMonthYearForm(title,form_id,link);
        });
    }

    var kwConsumoAreaReportAction = function () {
        $('body').on('submit', 'form#kwconsumoarea_report', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if($('div#extramodal').html(data.html)) {
                            $('div#extramodal').modal('show');
                            ultimoreporte = data.pdf;
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);

        });
    }
    //Fin de kw consumo

    //Inicio de consumo de combustible para mes X
    var combustibleConsumoMesAreaReportLink=function() {
        $('body').on('click', 'a#combustibleconsumomesareareport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('combustibleconsumomesarea_report');
            var form_id = 'combustibleconsumomesarea_report';
            var title = 'Consumo de combustible por área';
            createMonthYearForm(title,form_id,link);
        });
    }

    var combustibleConsumoMesAreaReportAction = function () {
        $('body').on('submit', 'form#combustibleconsumomesarea_report', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if($('div#extramodal').html(data.html)) {
                            $('div#extramodal').modal('show');
                            ultimoreporte = data.pdf;
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);
        });
    }
    //Fin de consumo de combustible para mes X

    // Inicio de distribucion combustible para mes X
    var combustibleDistribucionMesReportLink=function() {
        $('body').on('click', 'a#combustibledistribucionmesreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('combustibledistribucionmes_report');
            var form_id = 'combustibledistribucionmes_report';
            var title = 'Distribución de combustible por mes';
            createMonthYearForm(title,form_id,link);
        });
    }

    var combustibleDistribucionMesReportAction = function () {
        $('body').on('submit', 'form#combustibledistribucionmes_report', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if($('div#extramodal').html(data.html)){
                            $('div#extramodal').modal('show');
                            ultimoreporte = data.pdf;
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);

        });
    }
    //Fin de consumo de combustible para mes X

    // Inicio de distribucion combustible para mes X por vehiculo
    var combustibleConsumoMesVehiculoReportLink=function() {
        $('body').on('click', 'a#combustibleconsumomesvehiculoreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('combustibleconsumomesvehiculo_report');
            var form_id = 'combustibleconsumomesvehiculo_report';
            var title = 'Consumo de combustible por vehículo para un mes';
            createMonthYearForm(title,form_id,link);
        });
    }

    var combustibleConsumoMesVehiculoReportAction = function () {
        $('body').on('submit', 'form#combustibleconsumomesvehiculo_report', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if($('div#extramodal').html(data.html)){
                            $('div#extramodal').modal('show');
                            ultimoreporte = data.pdf;
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);
        });
    }
    //Fin de consumo de combustible para mes X por vehiculo

    //Inicio del estado de los vehiculos
    var estadoVehiculos = function () {
        $('body').on('click', 'a#estado_vehiculos', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            $.ajax({
                type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                },
                success: function (data) {
                    var este=JSON.parse(data);
                    if ($('div#extramodal').html(este.view)) {
                        $('div#extramodal').modal('show');
                    }
                    am4core.useTheme(am4themes_animated);
                    // Themes end
                    // Create chart instance
                    var chart = am4core.create("modal-body", am4charts.PieChart);
                    // Add data
                    chart.data=JSON.parse(este.data);
                    // Add and configure Series
                    var pieSeries = chart.series.push(new am4charts.PieSeries());
                    pieSeries.dataFields.value = "cantidad";
                    pieSeries.dataFields.category = "estado";
                    pieSeries.slices.template.stroke = am4core.color("#fff");
                    pieSeries.slices.template.strokeWidth = 2;
                    pieSeries.slices.template.strokeOpacity = 1;
                    // This creates initial animation
                    pieSeries.hiddenState.properties.opacity = 1;
                    pieSeries.hiddenState.properties.endAngle = -90;
                    pieSeries.hiddenState.properties.startAngle = -90;

                },
                error: function () {
                    base.Error();
                },
                complete: function () {
                }
            });
        });
    }
    //Fin de estado de los vehiculos

    //Inicio de resumen de viajes
    var resumenViajesReportLink=function() {
        $('body').on('click', 'a#resumenviajesreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('resumenviajes_report');
            var form_id = 'resumenviajesreport';
            var title = 'Resumen de viajes';
            createDateRangeForm(title,form_id,link);
        });
    }

    var resumenViajesReportAction = function () {
        $('body').on('submit', 'form#resumenviajesreport', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                        $('div#basicmodal').modal('hide');
                    },
                    success: function (datos) {
                        $('div#extramodal').html(datos.view);
                        $('div#extramodal').modal('show');

                        am4core.useTheme(am4themes_animated);
                        var visits = 10;
                        // Create chart
                        var chart = am4core.createFromConfig({
                            // Set settings and data
                            "paddingRight": 20,
                            //"data": data,
                            "data": datos.data,

                            // Create X axes
                            "xAxes": [{
                                "type": "DateAxis",
                                "renderer": {
                                    "grid": {
                                        "location": 0
                                    }
                                }
                            }],

                            // Create Y axes
                            "yAxes": [{
                                "type": "ValueAxis",
                                "tooltip": {
                                    "disabled": true
                                },
                                "renderer": {
                                    "minWidth": 35
                                }
                            }],

                            // Create series
                            "series": [{
                                "id": "s1",
                                "type": "LineSeries",
                                "dataFields": {
                                    "dateX": "date",
                                    "valueY": "value"
                                },
                                "tooltipText": "{valueY.value}"
                            }],

                            // Add cursor
                            "cursor": {
                                "type": "XYCursor"
                            },

                            // Add horizontal scrollbar
                            "scrollbarX": {
                                "type": "XYChartScrollbar",
                                "series": ["s1"]
                            }
                        }, "chartdiv", "XYChart");
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);

        });
    }
    //Fin de resumen de viajes

    //Inicio de resumen mensual de kw
    var consumoMensualKwReportLink=function() {
        $('body').on('click', 'a#consumomensualkwreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('consumomensualkw_report');
            var form_id = 'consumomensual_kw';
            var title = 'Consumo mensual de kilowatts';
            createYearForm(title,form_id,link);
        });
    }

    var consumoMensualKwReportAction = function () {
        $('body').on('submit', 'form#consumomensual_kw', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            setTimeout(function(){
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    url:  action,
                    beforeSend: function (data) {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        if ($('div#extramodal').html(data.view))
                            $('div#extramodal').modal('show');
                        var chart = am4core.createFromConfig({
                            // Reduce saturation of colors to make them appear as toned down
                            "colors": {
                                "saturation": 0.4
                            },

                            // Setting data
                            "data": data.data,

                            // Add Y axis
                            "yAxes": [{
                                "type": "ValueAxis",
                                "renderer": {
                                    "maxLabelPosition": 0.98
                                }
                            }],

                            // Add X axis
                            "xAxes": [{
                                "type": "CategoryAxis",
                                "renderer": {
                                    "minGridDistance": 20,
                                    "grid": {
                                        "location": 0
                                    }
                                },
                                "dataFields": {
                                    "category": "mes"
                                }
                            }],

                            // Add series
                            "series": [{
                                // Set type
                                "type": "ColumnSeries",

                                // Define data fields
                                "dataFields": {
                                    "categoryX": "mes",
                                    "valueY": "contador"
                                },

                                // Modify default state
                                "defaultState": {
                                    "ransitionDuration": 1000
                                },

                                // Set animation options
                                "sequencedInterpolation": true,
                                "sequencedInterpolationDelay": 100,

                                // Modify color appearance
                                "columns": {
                                    // Disable outline
                                    "strokeOpacity": 0,

                                    // Add adapter to apply different colors for each column
                                    "adapter": {
                                        "fill": function (fill, target) {
                                            return chart.colors.getIndex(target.dataItem.index);
                                        }
                                    }
                                }
                            }],

                            // Enable chart cursor
                            "cursor": {
                                "type": "XYCursor",
                                "behavior": "zoomX"
                            }
                        }, "chartdiv", "XYChart")
                    },
                    error: function () {
                        base.Error();
                    },
                });
            },500);

        });
    }
    //Fin de consumo mensual de kw

    //inicio de analisis de portadores
    var analisisPortadoresReportLink=function() {
        $('body').on('click', 'a#analisisportadoresreport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('analisisportadores_report');
            var form_id = 'analisisportadoresreport';
            var title = 'Análisis de portadores';
            createPortadoresForm(title,form_id,link);
        });
    }

    var analisisPortadoresReportAction = function () {
        $('body').on('submit', 'form#analisisportadoresreport', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (datos) {
                        $('div#extramodal').html(datos.view);
                        $('div#extramodal').modal('show');

                        am4core.useTheme(am4themes_animated);
// create chart
                        var chart = am4core.createFromConfig({
                                // Data
                                "data": datos.data,

                                // Category axis
                                "xAxes": [
                                    {
                                        "type": "CategoryAxis",
                                        "renderer": {
                                            "grid": {
                                                "location": 0,
                                                "disabled": true
                                            }
                                        },
                                        "dataFields": {
                                            "category": "mes"
                                        }
                                    }
                                ],

                                // Value axis
                                "yAxes": [
                                    {
                                        "type": "ValueAxis",
                                        "min": 0,
                                        "tooltip": {
                                            "disabled": true
                                        }
                                    }
                                ],

                                // Series
                                "series": [
                                    {
                                        "id": "s1",
                                        "type": "ColumnSeries",
                                        "dataFields": {
                                            "categoryX": "mes",
                                            "valueY": "plan"
                                        },
                                        "tooltipText": "Plan: {valueY.value}",
                                        "sequencedInterpolation": true,
                                        "columns": {
                                            "cornerRadiusTopLeft": 10,
                                            "cornerRadiusTopRight": 10,
                                            "strokeWidth": 1,
                                            "strokeOpacity": 1,
                                            "propertyFields": {
                                                "strokeDasharray": "stroke",
                                                "fillOpacity": "opacity"
                                            }
                                        }
                                    },
                                    {
                                        "id": "s2",
                                        "type": "LineSeries",
                                        "dataFields": {
                                            "categoryX": "mes",
                                            "valueY": "real"
                                        },
                                        "tooltipText": "Real: {valueY.value}",
                                        "sequencedInterpolation": true,
                                        "stroke": "#dcaf67",
                                        "strokeWidth": 2,

                                        "propertyFields": {
                                            "strokeDasharray": "lineStroke",
                                            "strokeOpacity": "lineOpacity"
                                        },
                                        "bullets": [
                                            {
                                                "type": "CircleBullet",
                                                "fill": "#dcaf67",
                                                "radius": 4
                                            }
                                        ]
                                    }
                                ],
                                "cursor": {
                                    "behavior": "none",
                                    "lineX": {
                                        "opacity": 0
                                    },
                                    "lineY": {
                                        "opacity": 0
                                    }
                                }
                            },
                            "chartdiv", "XYChart"
                        );
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);

        });
    }
    //fin de analisis de portadores

    //Inicio de los reporte del consumo y los kms
    var analisisHojaRutaReportLink=function() {
        $('body').on('click', 'a#analisishojarutareport_link', function (evento) {
            evento.preventDefault();
            var link = Routing.generate('analisishojaruta_report');
            var form_id = 'analisishojaruta_report';
            var title = 'Análisis hoja de ruta';
            createVehiculoDateRangeForm(title,form_id,link);
        });
    }

    var analisisHojaRutaReportAction = function () {
        $('body').on('submit', 'form#analisishojaruta_report', function (evento)
        {
            evento.preventDefault();
            $('div.bootbox').modal('hide');
            var action=$(this).attr("action");
            var data=$(this).serialize();
            setTimeout(function(){
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data, //para enviar el formulario hay que serializarlo
                    beforeSend: function () {
                        mApp.block("body",
                            {overlayColor:"#000000",type:"loader",state:"success",message:"Cargando..."});
                    },
                    complete: function () {
                        mApp.unblock("body");
                    },
                    success: function (data) {
                        {
                            if($('div#extramodal').html(data.html)) {
                                $('div#extramodal').modal('show');
                                ultimoreporte = data.pdf;
                            }
                        }
                    },
                    error: function ()
                    {
                        base.Error();
                    }
                });
            },500);
        });
    }
    //Fin del reporte del consumo y los kms

    var exportarAction = function () {
        $('div#extramodal').on('click', 'a.exportar', function (evento)
        {
            evento.preventDefault();

            $.fileDownload(Routing.generate('exportar_report'), {
                data:{
                    form: ultimoreporte
                }
            });

        });
    }


    return {
        init: function () {
            $().ready(function(){
                kmConsumoReportLink();
                kmConsumoReportAction();
                diferenciaConsumoReportLink();
                diferenciaConsumoReportAction();
                remanenteActualReport();
                porcientoDesviacionReportLink();
                porcientoDesviacionReportAction();
                pendienteMantenimiento();
                estadoVehiculos();
                resumenViajesReportLink();
                resumenViajesReportAction();
                analisisPortadoresReportLink();
                analisisPortadoresReportAction();
                consumoMensualKwReportLink();
                consumoMensualKwReportAction();
                kwConsumoAreaReportLink();
                kwConsumoAreaReportAction();
                combustibleConsumoMesAreaReportLink();
                combustibleConsumoMesAreaReportAction();
                combustibleDistribucionMesReportLink();
                combustibleDistribucionMesReportAction();
                combustibleConsumoMesVehiculoReportLink();
                combustibleConsumoMesVehiculoReportAction();
                analisisHojaRutaReportLink();
                analisisHojaRutaReportAction();
                exportarAction();
            });
        },
    };
}();