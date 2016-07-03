<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("header") . ( substr("header",-1,1) != "/" ? "/" : "" ) . basename("header") );?>



<script src="plugins/kardex/view/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="plugins/kardex/view/js/locale/grid.locale-es.js" type="text/javascript"></script>

<script src="plugins/kardex/view/js/plugins/jquery.jqGrid.min.js" type="text/javascript"></script>







<div class="container-fluid">
   <div class="row">
      <div class="col-sm-12">
         <div class="btn-group">
            <a class="btn btn-sm btn-default" href="<?php echo $fsc->url();?>" title="Recargar la página">
               <span class="glyphicon glyphicon-refresh"></span>
            </a>
         </div>
         <div class="btn-group">
             <?php if( !$fsc->kardex_procesandose ){ ?>

            <a href="#" class="btn btn-default btn-sm" id="b_kardex" name="kardex">
               <span class='glyphicon glyphicon-equalizer'></span>
               <span class="hidden-xs">&nbsp; Calcular Inventario Diario</span>
            </a>
            <?php } ?>

            <?php if( !$fsc->kardex_procesandose ){ ?>

            <a href="#" class="btn btn-default btn-sm" id="b_opciones_kardex" name="opciones_kardex">
               <span class='glyphicon glyphicon-cog'></span>
               <span class="hidden-xs">&nbsp; Opciones de Calculo</span>
            </a>
            <?php } ?>

         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-sm-12">
         <div class="page-header">
            <h1>
               <span class='glyphicon glyphicon-equalizer'></span>
               Kardex
               <?php if( $fsc->kardex_ultimo_proceso ){ ?>

               <small>
                  Última fecha procesada: <?php echo $fsc->kardex_ultimo_proceso;?>

               </small>
               <?php } ?>

            </h1>
         </div>
         <p class='help-block'>
            Por favor seleccione el rango de fechas, el almacen involucrado y
            proceda a hacer clic en el boton <b>Generar</b>.
         </p>
      </div>
   </div>
   <div class="row">
      <div class="col-sm-12">
         <?php if( $fsc->kardex_procesandose ){ ?>

         <div class="alert alert-warning">
            Usuario <b><?php echo $fsc->kardex_usuario_procesando;?></b> procesando Kardex,
            por favor actualice la página hasta que el usuario termine su proceso.
         </div>
         <?php } ?>

         <?php if( $fsc->kardex_ultimo_proceso=='' ){ ?>

         <div class="alert alert-warning">
            No se ha ejecutado el Calculo de Inventario Diario nunca, por favor ejecutelo
            ahora o programelo para que pueda tener información correcta de los art&iacute;culos
         </div>
         <?php } ?>

      </div>
   </div>
   <form id="f_generar_reporte" method="post" action='<?php echo $fsc->url();?>'>
      <input type="hidden" name="tipo-reporte" id="tipo-reporte" value='<?php echo $fsc->reporte;?>'>
      <div class="row">
         <div class="col-sm-2">
            <div class="form-group">
               Fecha Inicio:
               <input type="text" class="form-control datepicker" name='inicio' id="inicio" autocomplete="off"/>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group">
               Fecha Fin:
               <div class="input-group">
                  <input type="text" class="form-control datepicker" name="fin" id="fin" autocomplete="off"/>
                  <span class="input-group-btn">
                     <button class="btn btn-primary" type="submit" id="b_generar_reporte" name="buscar">
                        Generar
                     </button>
                  </span>
               </div>
            </div>
         </div>
         <div class="col-sm-2">
            <div class="form-group">
               <a href="<?php echo $fsc->almacenes->url();?>">Almacen</a>:
               <select class="form-control selectpicker" multiple='' name="codalmacen" id="codalmacen" data-style="btn-default" data-actions-box="true" required=''>
               <?php $loop_var1=$fsc->almacenes->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                  <?php if( $counter1==0 ){ ?>

                  <option value="<?php echo $value1->codalmacen;?>" selected=""><?php echo $value1->nombre;?></option>
                  <?php }else{ ?>

                  <option value="<?php echo $value1->codalmacen;?>"><?php echo $value1->nombre;?></option>
                  <?php } ?>

               <?php } ?>

               </select>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group">
               <a href="<?php echo $fsc->familias->url();?>">Familia</a>:
               <select class="form-control selectpicker" multiple='' name="codfamilia" id="codfamilia" data-style="btn-default" data-actions-box="true">
                  <?php $loop_var1=$fsc->familias->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                  <option value="<?php echo $value1->codfamilia;?>"><?php echo $value1->descripcion;?></option>
                  <?php } ?>

               </select>
            </div>
         </div>
         <div class="col-sm-2">
            <div class="form-group">
               <a href="<?php echo $fsc->articulos->url();?>">Artículo</a>:
               <select class="form-control input-sm selectpicker" multiple='' name="referencia" id="referencia" data-style="btn-default" data-actions-box="true">
                  <?php $loop_var1=$fsc->articulos->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                  <option value="<?php echo $value1->referencia;?>"><?php echo $value1->descripcion;?></option>
                  <?php } ?>

               </select>
            </div>
         </div>
      </div>
   </form>
   <div class="row">
      <div class="col-sm-12">
         <div id="kardex-resultados">
            <table id="grid_kardex"></table>
            <div id="grid_kardex_pager"></div>


         </div>
      </div>


   </div>
</div>

<div class="modal" id="modal_proceso_kardex">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-body" id='modal_proceso_kardex_body'>
            <div id="divProgress" class="text-left"></div>
            <div class="progress progress-popup">
               <div class="progress-bar progress-bar-striped active" id="progressor"></div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="modal_calcular_kardex">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Ejecutar Calculo de Inventario Diario</h4>
         </div>
         <div class="modal-body bg-info">
            <p align="justify">
               Si lo necesita puede indicar dos fechas para procesar el Inventario diario, si deja en blanco estos valores, se procesar&aacute;
               siguiendo la ultima fecha procesada, que es <b><?php echo $fsc->kardex_ultimo_proceso;?></b>.
            </p>
            <hr/>
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group col-xs-6 col-md-6">
                   <label for='kardex_inicio' class="control-label"><b>Fecha Inicio</b> (opcional)</label>
                   <input type="text" class="form-control datepicker" name='kardex_inicio' id="kardex_inicio">
                  </div>
                  <div class="form-group col-xs-6 col-md-6">
                      <label for='kardex_fin'><b>Fecha Fin</b> (opcional)</label>
                      <input type="text" class="form-control datepicker" name="kardex_fin" id="kardex_fin">
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" id="b_calcular_kardex" data-disable="true" class="btn btn-sm btn-warning">
               <span class='glyphicon glyphicon-equalizer'></span> &nbsp; Calcular
            </button>
         </div>
      </div>
   </div>
</div>

<div class="modal fade" id="modal_opciones_kardex">
   <div class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="f_opciones_kardex" role="form" action="<?php echo $fsc->url();?>" method="POST">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Opciones de Calculo de Inventario Diario</h4>
         </div>
         <div class="modal-body bg-warning">
            <p align="justify">
               Aqui puede indicar una hora espec&iacute;fica para ejecutar autom&aacute;ticamente el Calculo de Inventario Diario, pero para esto debe tener correctamente configurado
               el proceso de cron al instalar FacturaScripts, tenga en cuenta que si su cron esta configurado para ejecutarse una sola vez al d&iacute;a, entonces debe colocar
               la hora de ejecuci&oacute;n del Calculo de Inventario a la hora que corre el cron:
            </p>
            <hr/>
            <div class="row">
               <div class="col-sm-6">
                  <label class="control-label"><b>Se ejecuta autom&aacute;ticamente?</b></label>
               </div>
               <div class="col-sm-6">
                  <div class="form-group" id="kardex_cron_ops">
                     <label class="control-label">
                        <input type="radio" name='kardex_cron' value='TRUE'<?php if( $fsc->kardex_cron ){ ?> checked=''<?php } ?>>
                        Si
                     </label>
                     &nbsp;
                     <label class="control-label">
                        <input type="radio" name='kardex_cron' value=''<?php if( !$fsc->kardex_cron ){ ?> checked=''<?php } ?>>
                        No
                     </label>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <label class="control-label col-sm-6 pull-left" for='kardex_programado'><b>Hora de Ejecuci&oacute;n:</b></label>
               <select class="control-label selectpicker col-xs-2" name='kardex_programado' style="width: 20px;">
                  <?php $loop_var1=$fsc->loop_horas; $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                     <option value="<?php echo $value1;?>" <?php if( $fsc->kardex_programado == $value1 ){ ?>selected<?php } ?>><?php echo $value1;?></option>
                  <?php } ?>

               </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="submit" data-disable="true" class="btn btn-sm btn-warning">
               <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar Cambios
            </button>
         </div>
        </form>
      </div>
   </div>
</div>

<script>
    $.fn.selectpicker.defaults = {
        selectAllText: 'Marcar Todo',
        deselectAllText: 'Desmarcar',
        noneSelectedText: 'Nada seleccionado',
        countSelectedText: "{0} de {1} selecionados",
        selectedTextFormat: 'count'
    };

    $.jgrid.defaults.width = '100%';
    $.jgrid.defaults.responsive = true;
    $.jgrid.defaults.styleUI = 'Bootstrap';

    function last_stock(val, name, record){
        return parseFloat(record[name]||0);
    }
    
   function execute_kardex(){
      var kardex_inicio = $('#kardex_inicio').val();
      var kardex_fin = $('#kardex_fin').val();
      if (!window.XMLHttpRequest){
         alert("Tu navegador no soporta tecnología XMLHttpRequest.");
         return;
      }
      try{
         var xhr = new XMLHttpRequest();
         xhr.previous_text = '';
         xhr.onerror = function() { alert("Ocurrió un error inesperado en la llamada [XHR]."); };
         xhr.onreadystatechange = function() {
             try{
                 if (xhr.readyState === 4){
                     alert('Trabajo completado');
                     $('#modal_proceso_kardex').modal('hide');
                     window.location.assign("<?php echo $fsc->url();?>");
                 }
                 else if (xhr.readyState > 2){
                     var new_response = xhr.responseText.substring(xhr.previous_text.length);
                     var result = JSON.parse( new_response );
                     $('#modal_proceso_kardex').modal('show');
                     document.getElementById("divProgress").innerHTML = result.message;
                     document.getElementById("progressor").innerHTML = result.progress + "%";
                     document.getElementById('progressor').style.width = result.progress + "%";
                     xhr.previous_text = xhr.responseText;
                 }
             }
             catch (e){
                 console.log(e);
             }
         };
         xhr.open('GET', '<?php echo $fsc->url();?>&procesar-kardex=TRUE&kardex_inicio='+kardex_inicio+'&kardex_fin='+kardex_fin, true);
         xhr.send();
      }
      catch (e){
          console.log(e);
      }
   }

   function set_options(){
      $.ajax({
         type: 'POST',
         url: '<?php echo $fsc->url();?>',
         data: $('#f_opciones_kardex').serialize()+'&opciones-kardex=TRUE',
         success: function(datos) {
            alert(datos.mensaje);
            $("#modal_opciones_kardex").modal('hide');
         }
      });
   }

   function get_stock(){
      var inicio = $('#inicio').val();
      var fin = $('#fin').val();
      var almacen = $('#codalmacen').val();
      var familia = $('#codfamilia').val();
      var articulo = $('#referencia').val();
       $.ajax({
           type: 'POST',
           url: '<?php echo $fsc->url();?>',
           async: false,
           data: 'procesar-reporte=true&inicio='+inicio+'&fin='+fin+'&almacen='+almacen+'&familia='+familia+'&articulo='+articulo,
           success: function(datos) {
               console.log(datos.rows);



               $("#grid_kardex").jqGrid('clearGridData');
               $("#grid_kardex").jqGrid({
                   title: 'Kardex del '+inicio+' al '+fin,
                   data: datos.rows,
                   datatype: "local",
                   colModel: [
                       { label: 'Almacén', name: 'nombre', width: 150 },
                       { label: 'Fecha', name: 'fecha', key: true, width: 150 },
                       { label: 'Documento', name: 'tipo_documento', width: 70 },
                       { label: 'Núm Doc', name: 'documento', width: 50 },
                       { label: 'Artículo', name: 'descripcion', width: 150 },

                       {
                           label: 'Salida',
                           name: 'salida_cantidad',
                           width: 40,
                           summaryTpl : "<b>{0}</b>"
                       },
                       {
                           label: 'Ingreso',
                           name: 'ingreso_cantidad',
                           width: 40,
                           summaryTpl : "<b>{0}</b>"

                       },
                       {
                           label: 'P.Unitario',
                           name: 'salida_precio_uni',
                           width: 70,
                           summaryTpl : "<b>{0}</b>",
                           formatter: 'number', align: 'right', summaryType: last_stock,
                           formatoptions : { decimalSeparator: ".", decimalPlaces:2, thousandsSeparator:"," }
                       },
                       {label: 'Stock',width: 70},
                       {label: 'Saldo',width: 70},
                       {label: 'Promedio',width: 70}


                   ],
                   loadonce:true,
                   viewrecords: true,
                   rowList: [100,150,200],
                   headertitles: true,
                   autowidth: true,
                   height: 550,
                   rowNum: 100,
                   sortname: 'fecha',
                   pager: "#grid_kardex_pager",
                   grouping: true,
                   hoverrows: true,
                   groupingView: {
                       groupField: ["nombre", "descripcion"],
                       groupColumnShow: [true, true],
                       groupText: [
                           "<b>{0}</b>",
                           "<b>{0}</b>"
                       ],
                       groupOrder: ["asc", "asc"],
                       groupSummary: [true, false],
                       groupSummaryPos: ['header', 'header'],
                       groupCollapse: false
                   }
               });
               $('#grid_kardex').jqGrid()
                   .setGridParam({
                       data: datos
                       //lo cambie  datos.row
                   })
                   .trigger("reloadGrid");

               $('#grid_kardex').navGrid('#grid_kardex_pager',
                   { edit: false, add: false, del: false, search: true, refresh: false, view: false, position: "left", cloneToTop: true
               });
               $('#b_descargar_reporte').attr('href',datos.filename);
               $('#b_descargar_reporte').show();

                var salida = 0;
                var entrada= 0;
                var promedio=0;
                var saldo = 0;
               $('#grid_kardex tbody tr').each(function(){

                   //-stock
                   if($(this).find("td").eq(4).text()!= ''&&$(this).find("td").eq(4).html()!='&nbsp;'){
                       entrada += Math.floor($(this).find("td").eq(6).text());
                       salida += Math.floor($(this).find("td").eq(5).text());
                       console.log(entrada-salida);
                       $(this).find("td").eq(8).text(entrada-salida);
                   }else{
                       $(this).find("td").eq(7).text('');
                       if( $(this).find("td").eq(0).text()=='ALMACEN GENERAL'||$(this).find("td").eq(0).text()==''){
                           $(this).find("td").eq(10).text('');
                           $(this).find("td").eq(9).text('');
                       }else{
                           $(this).find("td").eq(10).text('0');
                           $(this).find("td").eq(9).addClass('padding-left:12px');
                           $(this).find("td").eq(9).append('<b>Salado Inicial</b>');

                       }

                       entrada=0;salida = 0;
                   }

                   if($(this).find("td").eq(4).text()!= ''&&$(this).find("td").eq(4).html()!='&nbsp;') {
                       //-saldo
                       var venta = Math.floor($(this).find("td").eq(5).text());
                       var ingrezo = Math.floor($(this).find("td").eq(6).text());

                       if (ingrezo == 0) {
                           $(this).find("td").eq(7).text(promedio);

                       }
                       var precio = $(this).find("td").eq(7).text();
                       precio = Math.floor(precio.replace(',', ''));


                       $(this).find("td").eq(9).text((ingrezo * precio+saldo - venta * precio));


                       var cant = $(this).find("td").eq(8).text();
                       var valores = $(this).find("td").eq(9).text();
                       saldo = Math.round(valores);
                       promedio = Math.round(valores / cant);


                       $(this).find("td").eq(10).text(promedio);
                   }else{
                        promedio=0;
                        saldo = 0;
                   }

                   if($(this).find("td").eq(0).text()=='Sillas para escritorio para el uso'||$(this).find("td").eq(4).text()=='Sillas para escritorio para el uso'){

                       $(this).hide();
                   }

               });
                

           }
       });
   }

    $('#bs-example-navbar-collapse-1 a').each(function(){
        if($(this).text()=='Ejercicios'){

            $(this).text('Plan contable');
        }

    });
    //----------------------


    //--------

   function runningFormatter(value, row, index) {
       return index;
   }

   function totalFormatter(data) {
       return data.length + ' Documentos';
   }

   function totalTextFormatter(data) {
       return 'Total';
   }

   function numberFormatter(value, row, index) {
       return parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
   }

   function sumFormatter(data) {
       field = this.field;
       return parseFloat(data.reduce(function(sum, row) {
           return sum + (+row[field]);
       }, 0)).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
   }

   function sumNormalFormatter(data) {
       field = this.field;
       return parseFloat(data.reduce(function(sum, row) {
           return sum + (+row[field]);
       }, 0)).toFixed(2);
   }

  $('#inicio').datepicker('option', 'dateFormat','Y-m-d');
  $('#inicio').datepicker('update', '<?php echo $fsc->fecha_inicio;?>');
  $('#fin').datepicker('option', 'dateFormat','Y-m-d');
  $('#fin').datepicker('update', '<?php echo $fsc->fecha_fin;?>');
  $('#kardex_inicio').datepicker('option', 'dateFormat','Y-m-d');
  $('#kardex_fin').datepicker('option', 'dateFormat','Y-m-d');

  $(document).ready(function () {
     $('#f_generar_reporte').submit(function (event) {
         event.preventDefault();
         get_stock();
     });

     $('#f_opciones_kardex').submit(function (event) {
         event.preventDefault();
         set_options();
     });
     $('#b_kardex').click(function (event) {
         $("#modal_calcular_kardex").modal('show');
     });

     $('#b_calcular_kardex').click(function (event) {
        $("#modal_calcular_kardex").modal('hide');
         execute_kardex()
         console.log(execute_kardex());
     });

     $("#b_opciones_kardex").click(function(event) {
        event.preventDefault();
        $("#modal_opciones_kardex").modal('show');
     });

   });
</script>

<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("footer") . ( substr("footer",-1,1) != "/" ? "/" : "" ) . basename("footer") );?>