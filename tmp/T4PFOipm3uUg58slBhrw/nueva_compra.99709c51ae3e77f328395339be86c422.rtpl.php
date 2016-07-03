<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("header") . ( substr("header",-1,1) != "/" ? "/" : "" ) . basename("header") );?>


<?php if( $fsc->proveedor_s ){ ?>

<script type="text/javascript" src="<?php echo $fsc->get_js_location('nueva_compra.js');?>"></script>
<script type="text/javascript">
   fs_nf0 = <?php  echo FS_NF0;?>;
   all_impuestos = <?php echo json_encode($fsc->impuesto->all()); ?>;
   all_series = <?php echo json_encode($fsc->serie->all()); ?>;
   proveedor = <?php echo json_encode($fsc->proveedor_s); ?>;
   nueva_compra_url = '<?php echo $fsc->url();?>';
   precio_compra = '<?php  echo FS_PRECIO_COMPRA;?>';
   
   <?php if( $fsc->empresa->recequivalencia ){ ?>

   tiene_recargo = true;
   <?php } ?>

   //------------ cod asiento----------------------------
   function add_cuenta()
   {
      numlineas++;
      $("#cuentas").append("<div class='col-sm-3'><input class='form-control' id='codsubcuenta_"+numlineas+"' name='idsubcuenta' type='text'\n\
               onclick=\"show_buscar_subcuentas('"+numlineas+"','subcuenta')\" onkeyup='document.f_buscar_subcuentas.query.value=$(this).val();buscar_subcuentas()'\n\
               autocomplete='off' placeholder='Seleccionar'/></div>\n\
               <input type='hidden' id='desc_"+numlineas+"' name='scuenta_name' value=''/>\n\
               <div class='col-sm-5'><input class='form-control' type='text' id='descri' name='desc_"+numlineas+"' disabled='disabled'/></div>");

      document.f_new_albaran.numlineas.value = numlineas;
      recalcular();
   }
   function show_buscar_subcuentas(num, tipo)
   {
      $("#subcuentas").html('');
      document.f_buscar_subcuentas.fecha.value = document.f_new_albaran.fecha.value;
      document.f_buscar_subcuentas.tipo.value = tipo;
      document.f_buscar_subcuentas.numlinea.value = num;
      document.f_buscar_subcuentas.query.value = '';
      $("#modal_subcuentas").modal('show');
      document.f_buscar_subcuentas.query.focus();
   }
   function buscar_subcuentas()
   {
      if(document.f_buscar_subcuentas.query.value == '')
      {
         $("#subcuentas").html('');
      }
      else
      {
         var datos = 'query='+document.f_buscar_subcuentas.query.value;
         datos += "&fecha="+document.f_buscar_subcuentas.fecha.value;
         $.ajax({
            type: 'POST',
            url: 'index.php?page=contabilidad_nuevo_asiento',
            dataType: 'html',
            data: datos,
            success: function(datos) {
               var re = /<!--(.*?)-->/g;
               console.log(document.f_buscar_subcuentas.query.value );
               var m = re.exec( datos );
               if( m[1] == document.f_buscar_subcuentas.query.value )
               {
                  $("#subcuentas").html(datos);
               }
            }
         });
      }
   }
   function select_subcuenta(codsubcuenta, saldo, descripcion)
   {
      var num = document.f_buscar_subcuentas.numlinea.value;
      if(document.f_buscar_subcuentas.tipo.value == 'subcuenta')
      {
         $("#codsubcuenta_"+num).val(codsubcuenta);
         $("#desc_"+num).val( Base64.decode(descripcion) );
         $("#descri").val( Base64.decode(descripcion) );
         $("#saldo_"+num).val(saldo);
      }
      else
      {
         $("#codcontrapartida_"+num).val(codsubcuenta);
         $("#saldoc_"+num).val(saldo);
      }
      $("#modal_subcuentas").modal('hide');
      recalcular();
   }
   //--end
   $(document).ready(function() {
      usar_serie();
      recalcular();

      });
</script>

<form id="f_new_albaran" class="form" name="f_new_albaran" action="<?php echo $fsc->url();?>" method="post">
   <input type="hidden" name="petition_id" value="<?php echo $fsc->random_string();?>"/>
   <input type="hidden" id="numlineas" name="numlineas" value="0"/>
   <input type="hidden" name="proveedor" value="<?php echo $fsc->proveedor_s->codproveedor;?>"/>
   <input type="hidden" name="redir"/>
   <div class="container-fluid">
      <div class="row">
         <div class="col-sm-6">
            <h1 style="margin-top: 5px;">
               <span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;
               <a href="<?php echo $fsc->proveedor_s->url();?>"><?php echo $fsc->proveedor_s->nombre;?></a>
            </h1>
         </div>
         <div class="col-sm-2">
            <div class="form-group">
               <a href="<?php echo $fsc->serie->url();?>" class="text-capitalize"><?php  echo FS_SERIE;?></a>:
               <select name="serie" class="form-control" id="codserie" onchange="usar_serie();recalcular();">
               <?php $loop_var1=$fsc->serie->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                  <?php if( $value1->codserie==$fsc->proveedor_s->codserie ){ ?>

                  <option value="<?php echo $value1->codserie;?>" selected=""><?php echo $value1->descripcion;?></option>
                  <?php }elseif( $value1->is_default() AND is_null($fsc->proveedor_s->codserie) ){ ?>

                  <option value="<?php echo $value1->codserie;?>" selected=""><?php echo $value1->descripcion;?></option>
                  <?php }else{ ?>

                  <option value="<?php echo $value1->codserie;?>"><?php echo $value1->descripcion;?></option>
                  <?php } ?>

               <?php } ?>

               </select>
            </div>
         </div>
         <div class="col-sm-2">
            <div class="form-group">
               Fecha:
               <input type="text" name="fecha" class="form-control datepicker" value="<?php echo $fsc->today();?>" autocomplete="off"/>
            </div>
         </div>
         <div class="col-sm-2">
            <div class="form-group">
               Hora:
               <input type="text" name="hora" class="form-control" value="<?php echo $fsc->hour();?>" autocomplete="off"/>
            </div>
         </div>
         <hr/>

      </div>
   </div>
   
   <div role="tabpanel">
      <ul class="nav nav-tabs" role="tablist">
         <li role="presentation" class="active">
            <a href="#lineas" aria-controls="lineas" role="tab" data-toggle="tab">
               <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
               <span class="hidden-xs">&nbsp; Líneas</span>
            </a>
         </li>
         <li role="presentation">
            <a href="#detalles" aria-controls="detalles" role="tab" data-toggle="tab">
               <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
               <span class="hidden-xs">&nbsp; Detalles</span>
            </a>
         </li>
      </ul>
      <div class="tab-content">
         <div role="tabpanel" class="tab-pane active" id="lineas">
            <div class="table-responsive">
               <table class="table table-condensed">
                  <thead>
                     <tr>
                        <th class="text-left" width="180">Referencia</th>
                        <th class="text-left">Descripción</th>
                        <th class="text-right" width="80">Cantidad</th>
                        <th width="50"></th>
                        <th class="text-right" width="110">Precio</th>
                        <th class="text-right" width="90">Dto. %</th>
                        <th class="text-right" width="130">Neto</th>
                        <th class="text-right" width="115"><?php  echo FS_IVA;?></th>
                        <th class="text-right recargo" width="115">RE %</th>
                        <th class="text-right irpf" width="115"><?php  echo FS_IRPF;?> %</th>
                        <th class="text-right" width="140">Total</th>
                     </tr>
                  </thead>
                  <tbody id="lineas_albaran"></tbody>
                  <tbody>
                     <tr class="info">
                        <td><input id="i_new_line" class="form-control" type="text" placeholder="Buscar para añadir..." autocomplete="off"/></td>
                        <td colspan="3">
                           <a href="#" class="btn btn-sm btn-default" title="Añadir sin buscar" onclick="return add_linea_libre()">
                              <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                           </a>
                        </td>
                        <td colspan="2">
                           <div class="form-control text-right">Totales</div>
                        </td>
                        <td><div id="aneto" class="form-control text-right" style="font-weight: bold;"><?php echo $fsc->show_numero(0);?></div></td>
                        <td><div id="aiva" class="form-control text-right" style="font-weight: bold;"><?php echo $fsc->show_numero(0);?></div></td>
                        <td class="recargo">
                           <div id="are" class="form-control text-right" style="font-weight: bold;"><?php echo $fsc->show_numero(0);?></div>
                        </td>
                        <td class="irpf">
                           <div id="airpf" class="form-control text-right" style="font-weight: bold;"><?php echo $fsc->show_numero(0);?></div>
                        </td>
                        <td>
                           <input type="text" name="atotal" id="atotal" class="form-control text-right" style="font-weight: bold;"
                                  value="0" onchange="recalcular()" autocomplete="off"/>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div role="tabpanel" class="tab-pane" id="detalles">
            <div class="container-fluid" style="margin-top: 10px;">
               <div class="row">
                  <div class="col-sm-3">
                     <div class="form-group">
                        Nombre del proveedor:
                        <input class="form-control" type="text" name="nombre" value="<?php echo $fsc->proveedor_s->razonsocial;?>" autocomplete="off"/>
                     </div>
                  </div>
                  <div class="col-sm-2">
                     <div class="form-group">
                        <?php  echo FS_CIFNIF;?>:
                        <input class="form-control" type="text" name="cifnif" value="<?php echo $fsc->proveedor_s->cifnif;?>" autocomplete="off"/>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-3">
                     <div class="form-group">
                        <a href="<?php echo $fsc->agente->url();?>">Empleado</a>:
                        <select name="codagente" class="form-control">
                           <option value="<?php echo $fsc->agente->codagente;?>"><?php echo $fsc->agente->get_fullname();?></option>
                           <?php if( $fsc->user->admin ){ ?>

                              <option value="<?php echo $fsc->agente->codagente;?>">-----</option>
                              <?php $loop_var1=$fsc->agente->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                              <option value="<?php echo $value1->codagente;?>"><?php echo $value1->get_fullname();?></option>
                              <?php } ?>

                           <?php } ?>

                        </select>
                     </div>
                  </div>
                  <div class="col-sm-2">
                     <div class="form-group">
                        <a href="<?php echo $fsc->almacen->url();?>">Almacén</a>:
                        <select name="almacen" class="form-control">
                           <?php $loop_var1=$fsc->almacen->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                           <option value="<?php echo $value1->codalmacen;?>"<?php if( $value1->is_default() ){ ?> selected=""<?php } ?>><?php echo $value1->nombre;?></option>
                           <?php } ?>

                        </select>
                     </div>
                  </div>
                  <div class="col-sm-2">
                     <div class="form-group">
                        <a href="<?php echo $fsc->divisa->url();?>">Divisa</a>:
                        <select name="divisa" class="form-control">
                           <?php $loop_var1=$fsc->divisa->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                           <option value="<?php echo $value1->coddivisa;?>"<?php if( $value1->is_default() ){ ?> selected=""<?php } ?>><?php echo $value1->descripcion;?></option>
                           <?php } ?>

                        </select>
                     </div>
                  </div>
                  <div class="col-sm-3">
                     <div class="form-group">
                        Tasa de conversión
                        <input type="text" name="tasaconv" class="form-control" placeholder="(predeterminada)" autocomplete="off"/>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="container-fluid" style="margin-top: 10px;">
      <div class="row">
         <div class="col-sm-6">
            <button class="btn btn-sm btn-default" type="button" onclick="window.location.href='<?php echo $fsc->url();?>';">
               <span class="glyphicon glyphicon-refresh"></span>&nbsp; Reiniciar
            </button>
         </div>
         <div class="col-sm-6 text-right" id="cuentas">
               <a href="#" class="btn btn-sm btn-success" onclick="add_cuenta();return false;">
                  <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                  <span class="hidden-xs">&nbsp; Añadir Cuentas</span>
               </a>

            <button class="btn btn-sm btn-primary" type="button" onclick="$('#modal_guardar').modal('show');">
               <span class="glyphicon glyphicon-floppy-disk"></span>&nbsp; Guardar...
            </button>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <br/>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-6">
            <div class="form-group">
               Observaciones:
               <textarea class="form-control" name="observaciones" rows="3"></textarea>
            </div>
         </div>

      </div>
   </div>
   
   <div class="modal fade" id="modal_guardar">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               <h4 class="modal-title">Guardar como...</h4>

            </div>
            <div class="modal-body">
               <?php $loop_var1=$fsc->tipos_a_guardar(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

               <div class="radio">
                  <label>
                     <input type="radio" name="tipo" value="<?php echo $value1['tipo'];?>"<?php if( $value1['tipo']==$fsc->tipo ){ ?> checked="checked"<?php } ?>/>
                     <?php echo $value1['nombre'];?>

                  </label>
               </div>
               <?php } ?>

               <div class="form-group">
                  Núm. proveedor:
                  <input class="form-control" type="text" name="numproveedor" autocomplete="off"/>
                  <p class="help-block">
                     Si quieres, puedes guardar el número de documento del proveedor
                  </p>
               </div>
               <div class="form-group">
                  <a href="<?php echo $fsc->forma_pago->url();?>">Forma de pago</a>:
                  <select name="forma_pago" class="form-control">
                  <?php $loop_var1=$fsc->forma_pago->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                     <?php if( $fsc->proveedor_s->codpago==$value1->codpago ){ ?>

                     <option value="<?php echo $value1->codpago;?>" selected=""><?php echo $value1->descripcion;?></option>
                     <?php }else{ ?>

                     <option value="<?php echo $value1->codpago;?>"><?php echo $value1->descripcion;?></option>
                     <?php } ?>

                  <?php } ?>

                  </select>
               </div>
               <?php if( !$fsc->proveedor_s->acreedor ){ ?>

               <div class="checkbox">
                  <label>
                     <input type="checkbox" name="stock" value="TRUE" checked="checked"/>
                     Añadir al stock
                  </label>
               </div>
               <div class="checkbox">
                  <label>
                     <input type="checkbox" name="costemedio" value="TRUE"<?php if( FS_COST_IS_AVERAGE ){ ?> checked="checked"<?php } ?>/>
                     Actualizar precio de coste de los artículos
                  </label>
               </div>
               <?php } ?>

            </div>
            <div class="modal-footer">
               <div class="btn-group">
                  <button class="btn btn-sm btn-primary" type="button" onclick="this.disabled=true;this.form.submit();" title="Guardar y volver a empezar">
                     <span class="glyphicon glyphicon-floppy-disk"></span>&nbsp; Guardar
                  </button>
                  <button class="btn btn-sm btn-info" type="button" onclick="this.disabled=true;document.f_new_albaran.redir.value='TRUE';this.form.submit();" title="Guardar y ver documento">
                     <span class="glyphicon glyphicon-eye-open"></span>
                  </button>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>

<div class="modal" id="modal_articulos">
   <div class="modal-dialog" style="width: 99%; max-width: 1000px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Buscar artículos</h4>
            <p class="help-block">
               <span class="glyphicon glyphicon-info-sign"></span>
               Coloca el puntero sobre un precio para ver la fecha en la que fue actualizado.
            </p>
         </div>
         <div class="modal-body">
            <form id="f_buscar_articulos" name="f_buscar_articulos" action="<?php echo $fsc->url();?>" method="post" class="form">
               <input type="hidden" name="codproveedor" value="<?php echo $fsc->proveedor_s->codproveedor;?>"/>
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-sm-4">
                        <div class="input-group">
                           <input class="form-control" type="text" name="query" autocomplete="off"/>
                           <span class="input-group-btn">
                              <button class="btn btn-primary" type="submit">
                                 <span class="glyphicon glyphicon-search"></span>
                              </button>
                           </span>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <select class="form-control" name="codfamilia" onchange="buscar_articulos()">
                           <option value="">Cualquier familia</option>
                           <option value="">------</option>
                           <?php $loop_var1=$fsc->familia->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                           <option value="<?php echo $value1->codfamilia;?>"><?php echo $value1->nivel;?><?php echo $value1->descripcion;?></option>
                           <?php } ?>

                        </select>
                     </div>
                     <div class="col-sm-4">
                        <select class="form-control" name="codfabricante" onchange="buscar_articulos()">
                           <option value="">Cualquier fabricante</option>
                           <option value="">------</option>
                           <?php $loop_var1=$fsc->fabricante->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                           <option value="<?php echo $value1->codfabricante;?>"><?php echo $value1->nombre;?></option>
                           <?php } ?>

                        </select>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-2">
                        <div class="checkbox-inline">
                           <label>
                              <input type="checkbox" name="con_stock" value="TRUE" onchange="buscar_articulos()"/>
                              sólo con stock
                           </label>
                        </div>
                     </div>
                     <div class="col-sm-3">
                        <div class="checkbox-inline">
                           <label>
                              <input type="checkbox" name="solo_proveedor" value="TRUE" onchange="buscar_articulos()"/>
                              sólo de este proveedor
                           </label>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <ul class="nav nav-tabs" id="nav_articulos" style="display: none;">
            <li id="li_mis_articulos">
               <a href="#" id="b_mis_articulos">Mi catálogo</a>
            </li>
            <li id="li_nuevo_articulo">
               <a href="#" id="b_nuevo_articulo">
                  <span class="glyphicon glyphicon-plus"></span>&nbsp; Nuevo
               </a>
            </li>
         </ul>
         <div id="search_results"></div>
         <div id="nuevo_articulo" class="modal-body" style="display: none;">
            <form name="f_nuevo_articulo" action="<?php echo $fsc->url();?>" method="post" class="form">
               <input type="hidden" name="codproveedor" value="<?php echo $fsc->proveedor_s->codproveedor;?>"/>
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-sm-3">
                        <div class="form-group">
                           Referencia:
                           <div class="input-group">
                              <input class="form-control" type="text" name="referencia" maxlength="18" autocomplete="off"/>
                              <span class="input-group-btn" title="Borrar">
                                 <button class="btn btn-default" type="button" onclick="document.f_nuevo_articulo.referencia.value='';document.f_nuevo_articulo.refproveedor.focus();">
                                    <span class="glyphicon glyphicon-edit"></span>
                                 </button>
                              </span>
                           </div>
                           <p class="help-block">Dejar en blanco para asignar una referencia automática.</p>
                        </div> 
                     </div>
                     <div class="col-sm-3">
                        <div class="form-group">
                           Ref. Proveedor:
                           <input class="form-control" type="text" name="refproveedor" maxlength="25" autocomplete="off"/>
                        </div>
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                           Descripción:
                           <textarea name="descripcion" rows="1" class="form-control"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-3">
                        <div class="form-group">
                           <a href="<?php echo $fsc->impuesto->url();?>"><?php  echo FS_IVA;?></a>:
                           <select name="codimpuesto" class="form-control">
                              <?php $loop_var1=$fsc->impuesto->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                              <option value="<?php echo $value1->codimpuesto;?>"<?php if( $value1->is_default() ){ ?> selected=""<?php } ?>><?php echo $value1->descripcion;?></option>
                              <?php } ?>

                           </select>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           Precio compra:
                           <input type="text"  name="coste" value="0" class="coste form-control" autocomplete="off"/>
                        </div>
                     </div>

                     <div class="col-sm-4">
                        <div class="form-group">
                           Precio venta:
                           <input type="text" name="pvp" value="0" class="pvp form-control" autocomplete="off"/>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">
                           Código de barras:
                           <div class="input-group">
                              <span class="input-group-addon">
                                 <span class="glyphicon glyphicon-barcode"></span>
                              </span>
                              <input class="form-control" type="text" name="codbarras" maxlength="18" autocomplete="off"/>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <a href="<?php echo $fsc->familia->url();?>">Familia</a>:
                           <select name="codfamilia" class="form-control">
                              <option value="">Ninguna</option>
                              <option value="">-------</option>
                              <?php $loop_var1=$fsc->familia->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                              <option value="<?php echo $value1->codfamilia;?>"><?php echo $value1->nivel;?><?php echo $value1->descripcion;?></option>
                              <?php } ?>

                           </select>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <a href="<?php echo $fsc->fabricante->url();?>">Fabricante</a>:
                           <select name="codfabricante" class="form-control">
                              <option value="">Ninguno</option>
                              <option value="">-------</option>
                              <?php $loop_var1=$fsc->fabricante->all(); $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                              <option value="<?php echo $value1->codfabricante;?>"><?php echo $value1->nombre;?></option>
                              <?php } ?>

                           </select>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row">
                     <div class="col-sm-8">
                        <label class="checkbox-inline">
                           <input type="checkbox" name="secompra" value="TRUE" checked=""/> Se compra
                        </label> &nbsp;
                        <label class="checkbox-inline">
                           <input type="checkbox" name="sevende" value="TRUE" checked=""/> Se vende
                        </label> &nbsp;
                        <label class="checkbox-inline">
                           <input type="checkbox" name="nostock" value="TRUE"/> No controlar stock
                        </label> &nbsp;
                        <label class="checkbox-inline">
                           <input type="checkbox" name="publico" value="TRUE"/>
                           <span class="glyphicon glyphicon-globe"></span> Público
                        </label>
                     </div>
                     <div class="col-sm-4 text-right">
                        <button class="btn btn-sm btn-primary" type="submit" onclick="new_articulo();return false;">
                           <span class="glyphicon glyphicon-floppy-disk"></span>&nbsp; Guardar y seleccionar
                        </button>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?php }elseif( !$fsc->user->get_agente() ){ ?>

<div class="container">
   <div class="row">
      <div class="col-sm-12">
         <div class="page-header">
            <h1>
               <span class="glyphicon glyphicon-exclamation-sign"></span>
               No tienes un empleado asociado
            </h1>
         </div>
      </div>
      <p class="help-block">
         No tienes un emleado asociado a tu <a href="<?php echo $fsc->user->url();?>">usuario</a>.
         Habla con el administrador para que te asigne un empleado.
      </p>
   </div>
</div>
<?php }else{ ?>

<script type="text/javascript">
   function acreedores_help()
   {
      alert('Los acreedores son todos aquellos proveedores a los que no les compramos mercancias. Por ejemplo: proveedor de internet, teléfono, bancos...');
      return false;
   }
   $(document).ready(function() {
      $("#modal_proveedor").modal('show');
      document.f_nueva_compra.ac_proveedor.focus();
      $("#ac_proveedor").autocomplete({
         serviceUrl: '<?php echo $fsc->url();?>',
         paramName: 'buscar_proveedor',
         onSelect: function (suggestion) {
            if(suggestion)
            {
               if(document.f_nueva_compra.proveedor.value != suggestion.data)
               {
                  document.f_nueva_compra.proveedor.value = suggestion.data;
                  document.f_nueva_compra.nuevo_proveedor.value = '';
                  document.f_nueva_compra.nuevo_cifnif.value = '';
               }
            }
         }
      });


   });
</script>

<form name="f_nueva_compra" class="form" action="<?php echo $fsc->url();?>" method="post">
   <input type="hidden" name="proveedor"/>
   <div class="modal" id="modal_proveedor">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               <h4 class="modal-title">
                  <span class="glyphicon glyphicon-search"></span>
                  &nbsp; Selecciona el proveedor o acreedor
               </h4>
               <p class="help-block">
                  Busca y selecciona el proveedor o acrredor. Si lo deseas puedes crear uno nuevo usando el recuadro en azul.
               </p>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <div class="input-group">
                     <input class="form-control" type="text" name="ac_proveedor" id="ac_proveedor" placeholder="Buscar" autocomplete="off"/>
                     <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit" onclick="this.disabled=true;this.form.submit();">
                           <span class="glyphicon glyphicon-share-alt"></span>
                        </button>
                     </span>
                  </div>
               </div>
            </div>
            <div class="modal-body bg-info">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="form-group">
                        Nuevo proveedor:
                        <input type="text" name="nuevo_proveedor" class="form-control" placeholder="Nombre" autocomplete="off"/>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-3">
                     <select name="nuevo_tipoidfiscal" class="form-control">
                        <?php $tiposid=$this->var['tiposid']=fs_tipos_id_fiscal();?>

                        <?php $loop_var1=$tiposid; $counter1=-1; if($loop_var1) foreach( $loop_var1 as $key1 => $value1 ){ $counter1++; ?>

                        <option value="<?php echo $value1;?>"><?php echo $value1;?></option>
                        <?php } ?>

                     </select>
                  </div>
                  <div class="col-sm-9">
                     <div class="form-group">
                        <input type="text" name="nuevo_cifnif" class="form-control" autocomplete="off"/>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-12">
                     <div class="checkbox">
                        <label>
                           <input type="checkbox" name="acreedor" value="TRUE"/>
                           Es un <b>acreedor</b>
                           <a href="#" onclick="return acreedores_help()">
                              <span class="glyphicon glyphicon-question-sign"></span>
                           </a>
                        </label>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>

<div class="container" style="margin-top: 10px; margin-bottom: 100px;">
   <div class="row">
      <div class="col-lg-12">
         <h1>Paso 1:</h1>
         <p>Selecciona el proveedor o acreedor al que quieres realizar la compra.</p>
         <a href="#" class="btn btn-block btn-default" data-toggle="modal" data-target="#modal_proveedor">Selecciona el proveedor o acreedor</a>
         <h2>Paso 2:</h2>
         <p>Introduce línea a línea todos los artículos de la compra.</p>
         <h2>Paso 3:</h2>
         <p>Pulsa el botón guardar.</p>
      </div>
   </div>
</div>
<?php } ?>

<form id="f_buscar_subcuentas" name="f_buscar_subcuentas" class="form">
   <input type="hidden" name="fecha"/>
   <input type="hidden" name="tipo"/>
   <input type="hidden" name="numlinea"/>
   <div class="modal" id="modal_subcuentas">
      <div class="modal-dialog" style="width: 99%; max-width: 1000px;">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               <h4 class="modal-title">Buscar subcuentas</h4>
            </div>
            <div class="modal-body">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-xs-8">
                        <div class="input-group">
                           <input class="form-control" type="text" name="query" onkeyup="buscar_subcuentas();" autocomplete="off" autofocus="" />
                           <span class="input-group-btn">
                              <button class="btn btn-primary" type="submit">
                                 <span class="glyphicon glyphicon-search"></span>
                              </button>
                           </span>
                        </div>
                     </div>
                     <div class="col-xs-4">
                        <a href="#" class="btn btn-sm btn-block btn-danger" onclick="select_subcuenta('','','')">
                           <span class="glyphicon glyphicon-remove"></span>
                           <span class="hidden-xs">&nbsp; ninguna</span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            <div id="subcuentas"></div>
         </div>
      </div>
   </div>
</form>
<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("footer") . ( substr("footer",-1,1) != "/" ? "/" : "" ) . basename("footer") );?>