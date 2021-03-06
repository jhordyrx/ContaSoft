<?php if(!class_exists('raintpl')){exit;}?><?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("header") . ( substr("header",-1,1) != "/" ? "/" : "" ) . basename("header") );?>

<script type="text/javascript" src="modulos/peru/view/js/provincias.js"></script>
<script type="text/javascript" src="modulos/core/view/js/nueva_caja_bancos.js"></script>
<script>

    function buscar_subcuentas()
    {
        if(document.f_buscar_subcuentas.query.value == '')
        {
            $("#subcuentas").html('');
        }
        else
        {
            var datos = 'query='+document.f_buscar_subcuentas.query.value;
            datos += "&fecha="+$('#fecha').val();
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
                    $("#subcuentas tr").each(function(){
                        var elemento = $(this).find("td").eq(1).text().trim().substring(0,1);
                        if(elemento!='1'&&elemento!='4'){
                            $(this).remove();
                        }

                    });
                }
            });
        }
    }

    function show_buscar_subcuentas(num, tipo)
    {


        document.f_buscar_subcuentas.tipo.value = tipo;
        document.f_buscar_subcuentas.numlinea.value = num;
        document.f_buscar_subcuentas.query.value = '';
        $("#subcuentas").html('');
        $("#modal_subcuentas").modal('show');
        document.f_buscar_subcuentas.query.focus();
    }

    function select_subcuenta(codsubcuenta, saldo, descripcion)
    {
        var num = document.f_buscar_subcuentas.numlinea.value;
        if(document.f_buscar_subcuentas.tipo.value == 'subcuenta')
        {
            $("#codsubcuenta_"+num).val(codsubcuenta);
            $("#desc_"+num).val( Base64.decode(descripcion) );
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
    $(document).ready(function() {
        $('#guardar_principal').on('click', function () {

            $('#lineas_albaran tr').each(function () {

                var propiedad = $(this).find("td").eq(0).find('input:checked').val();
                var deudor=0;
                var acreedor=0;
                var saldo=$(this).find("td").eq(5).find('.form-control').val();
                if (propiedad == 'ent') {
                    deudor=saldo;
                } else {
                    acreedor=saldo
                }
                var descripcion=$(this).find("td").eq(1).find('.form-control').val();
                var nombre=$(this).find("td").eq(2).find('.form-control').val();
                var codigo=$(this).find("td").eq(3).find('.form-control').val();

                console.log('deudor',deudor);
                console.log('acreedor',acreedor);
                console.log('descripcion',descripcion);
                console.log('nombre',nombre);
                console.log('codigo',codigo);

            });
        });
    });
</script>
<input type="hidden" id="numlineas" name="numlineas" value="0"/>
<form id="f_new_albaran" class="form" name="f_new_albaran" action="" method="post">

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1 style="margin-top: 5px;">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;
                    <a href="#">Libro Caja y Bancos</a>
                </h1>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    Fecha:
                    <input type="text" id="fecha" name="fecha" class="form-control datepicker" value="<?php echo $fsc->today();?>" autocomplete="off"/>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    Hora:
                    <input type="text" name="hora" class="form-control" value="<?php echo $fsc->hour();?>" autocomplete="off"/>
                </div>
            </div>
        </div>
    </div>

    <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#lineas" aria-controls="lineas" role="tab" data-toggle="tab">
                    <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                    <span class="hidden-xs">&nbsp; Reporte</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="lineas">
                <div class="table-responsive">

                    <table  class="table table-condensed">
                        <thead>
                        <tr>
                            <th class="text-left" width="100">Tipo</th>
                            <th class="text-left" width="200">Descripcion</th>
                            <th class="text-left" >Nombres ó Rason Social</th>
                            <th class="text-left" width="140">Codigo</th>
                            <th class="text-left"  >Denominacion</th>
                            <th class="text-left" width="100">Saldo</th>
                            <th width="50"></th>
                        </tr>
                        </thead>
                        <tbody id="lineas_albaran"></tbody>
                        <tbody >
                        <tr class="info">
                            <td colspan="4">
                                <a href="#" class="btn btn-sm btn-default" title="Añadir sin buscar" onclick="return add_linea_libre()">
                                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                                </a>
                            </td>
                            <td colspan="1">
                                <div class="form-control text-right">Totales</div>
                            </td>
                            <td class="irpf">
                                <div id="airpf" class="form-control text-right" style="font-weight: bold;">0.00</div>
                            </td>
                            <td>
                                <input type="text" name="atotal" id="atotal" class="form-control text-right" style="font-weight: bold;"
                                       value="0" onchange="recalcular()" autocomplete="off"/>
                            </td>
                            <td>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" style="margin-top: 10px;">
        <div class="row">

            <div class="col-sm- text-right">
                <button id="guardar_principal" class="btn btn-sm btn-primary" type="button" >
                    <span class="glyphicon glyphicon-floppy-disk"></span> &nbsp; Guardar...
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <br/>
            </div>
        </div>

    </div>


</form>
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