<?php

require_model('agente.php');
require_model('articulo.php');
require_model('cliente.php');
require_model('factura_cliente.php');
require_model('asiento.php');
require_model('concepto_partida.php');
require_model('divisa.php');
require_model('ejercicio.php');
require_model('impuesto.php');
require_model('partida.php');
require_model('subcuenta.php');

class ventas_facturas extends fs_controller
{
   public $agente;
   public $articulo;
   public $buscar_lineas;
   public $cliente;
   public $codagente;
   public $codserie;
   public $desde;
   public $estado;
   public $factura;
   public $hasta;
   public $huecos;
   public $lineas;
   public $mostrar;
   public $num_resultados;
   public $offset;
   public $order;
   public $resultados;
   public $serie;
   public $total_resultados;
   public $total_resultados_comision;
   public $total_resultados_txt;
   public $listacliente;
   public $datosfactura;
   public $asiento;
   public $concepto;
   public $divisa;
   public $ejercicio;
   public $impuesto;
   public $subcuenta;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Facturas', 'ventas');
   }
   
   protected function private_core()
   {
      $this->agente = new agente();
      $this->factura = new factura_cliente();
      $this->huecos = array();
      $this->serie = new serie();
      $this->listacliente = new linea_factura_cliente();
      //------------------------------------------------------
      $this->asiento = new asiento();
      $this->concepto = new concepto_partida();
      $this->divisa = new divisa();
      $this->ejercicio = new ejercicio();
      $this->impuesto = new impuesto();
      $this->lineas = array();
      $this->subcuenta = new subcuenta();

      if(isset($_REQUEST['geturl'])==1){
         $this->template = FALSE;
         header('Content-Type: application/json');
         echo json_encode($this->datosfactura = $this->listacliente->all_factura_cli());
      }
      $this->mostrar = 'todo';
      if( isset($_GET['mostrar']) )
      {
         $this->mostrar = $_GET['mostrar'];
         setcookie('ventas_fac_mostrar', $this->mostrar, time()+FS_COOKIES_EXPIRE);
      }
      else if( isset($_COOKIE['ventas_fac_mostrar']) )
      {
         $this->mostrar = $_COOKIE['ventas_fac_mostrar'];
      }
      
      $this->offset = 0;
      if( isset($_REQUEST['offset']) )
      {
         $this->offset = intval($_REQUEST['offset']);
      }
      
      $this->order = 'fecha DESC';
      if( isset($_GET['order']) )
      {
         if($_GET['order'] == 'fecha_desc')
         {
            $this->order = 'fecha DESC';
         }
         else if($_GET['order'] == 'fecha_asc')
         {
            $this->order = 'fecha ASC';
         }
         else if($_GET['order'] == 'vencimiento_desc')
         {
            $this->order = 'vencimiento DESC';
         }
         else if($_GET['order'] == 'vencimiento_asc')
         {
            $this->order = 'vencimiento ASC';
         }
         else if($_GET['order'] == 'total_desc')
         {
            $this->order = 'total DESC';
         }
         
         setcookie('ventas_fac_order', $this->order, time()+FS_COOKIES_EXPIRE);
      }
      else if( isset($_COOKIE['ventas_fac_order']) )
      {
         $this->order = $_COOKIE['ventas_fac_order'];
      }
      
      if( isset($_POST['buscar_lineas']) )
      {
         $this->buscar_lineas();
      }
      else if( isset($_REQUEST['buscar_cliente']) )
      {
         $this->buscar_cliente();
      }
      else if( isset($_GET['ref']) )
      {
         $this->template = 'extension/ventas_facturas_articulo';
         
         $articulo = new articulo();
         $this->articulo = $articulo->get($_GET['ref']);
         
         $linea = new linea_factura_cliente();
         $this->resultados = $linea->all_from_articulo($_GET['ref'], $this->offset);
      }
      else
      {
         $this->share_extension();
         $this->huecos = $this->factura->huecos();
         $this->cliente = FALSE;
         $this->codagente = '';
         $this->codserie = '';
         $this->desde = '';
         $this->estado = '';
         $this->hasta = '';
         $this->num_resultados = '';
         $this->total_resultados = array();
         $this->total_resultados_comision = 0;
         $this->total_resultados_txt = '';
         
         if( isset($_GET['delete']) )
         {
            $this->delete_factura();
         }
         else
         {
            if( !isset($_GET['mostrar']) AND (isset($_REQUEST['codagente']) OR isset($_REQUEST['codcliente']) OR isset($_REQUEST['codserie'])) )
            {
               
               $this->mostrar = 'buscar';
            }
            
            if( isset($_REQUEST['codcliente']) )
            {
               if($_REQUEST['codcliente'] != '')
               {
                  $cli0 = new cliente();
                  $this->cliente = $cli0->get($_REQUEST['codcliente']);
               }
            }
            
            if( isset($_REQUEST['codagente']) )
            {
               $this->codagente = $_REQUEST['codagente'];
            }
            
            if( isset($_REQUEST['codserie']) )
            {
               $this->codserie = $_REQUEST['codserie'];
            }
            
            if( isset($_REQUEST['desde']) )
            {
               $this->desde = $_REQUEST['desde'];
               $this->hasta = $_REQUEST['hasta'];
               $this->estado = $_REQUEST['estado'];
            }
         }
         
         $order2 = '';
         if( substr($this->order, -4) == 'DESC' )
         {
            $order2 = ', hora DESC, numero DESC';
         }
         else
         {
            $order2 = ', hora ASC, numero ASC';
         }
         
         if($this->mostrar == 'sinpagar')
         {
            $this->resultados = $this->factura->all_sin_pagar($this->offset, FS_ITEM_LIMIT, $this->order.$order2);
            
            if($this->offset == 0)
            {
               /// calculamos el total, pero desglosando por divisa
               $this->total_resultados = array();
               $this->total_resultados_txt = 'Suma total de esta página:';
               foreach($this->resultados as $fac)
               {
                  if( !isset($this->total_resultados[$fac->coddivisa]) )
                  {
                     $this->total_resultados[$fac->coddivisa] = array(
                         'coddivisa' => $fac->coddivisa,
                         'total' => 0
                     );
                  }
                  
                  $this->total_resultados[$fac->coddivisa]['total'] += $fac->total;
               }
            }
         }
         else if($this->mostrar == 'buscar')
         {
            $this->buscar($order2);
         }
         else
            $this->resultados = $this->factura->all($this->offset, FS_ITEM_LIMIT, $this->order.$order2);
      }
      if( isset($_POST['fecha']) AND isset($_POST['concepto']) ){
         $this->nuevo_asiento();
      }
   }
   private function nuevo_asiento()
   {
      $continuar = TRUE;

      $eje0 = $this->ejercicio->get_by_fecha($_POST['fecha']);
      if(!$eje0)
      {
         $this->new_error_msg('Ejercicio no encontrado.');
         $continuar = FALSE;
      }

      $div0 = $this->divisa->get('PEN');
      if(!$div0)
      {
         $this->new_error_msg('Divisa no encontrada.');
         $continuar = FALSE;
      }

      if($continuar)
      {
         $this->asiento->codejercicio = $eje0->codejercicio;
         $this->asiento->concepto = $_POST['concepto'];
         $this->asiento->fecha = $_POST['fecha'];
         $this->asiento->importe = floatval($_POST['importe']);

         if( $this->asiento->save() )
         {
            $numlineas = intval($_POST['numlineas']);
            for($i=1; $i <= $numlineas; $i++)
            {
               if( isset($_POST['codsubcuenta_'.$i]) )
               {
                  if( $_POST['codsubcuenta_'.$i] != '' AND $continuar)
                  {
                     $sub0 = $this->subcuenta->get_by_codigo($_POST['codsubcuenta_'.$i], $eje0->codejercicio);
                     if($sub0)
                     {
                        $partida = new partida();
                        $partida->idasiento = $this->asiento->idasiento;
                        $partida->coddivisa = $div0->coddivisa;
                        $partida->tasaconv = $div0->tasaconv;
                        $partida->idsubcuenta = $sub0->idsubcuenta;
                        $partida->codsubcuenta = $sub0->codsubcuenta;
                        $partida->debe = floatval($_POST['debe_'.$i]);
                        $partida->haber = floatval($_POST['haber_'.$i]);
                        $partida->idconcepto = $this->asiento->idconcepto;
                        $partida->concepto = $this->asiento->concepto;
                        $partida->documento = $this->asiento->documento;
                        $partida->tipodocumento = $this->asiento->tipodocumento;

                        if( isset($_POST['codcontrapartida_'.$i]) )
                        {
                           if( $_POST['codcontrapartida_'.$i] != '')
                           {
                              $subc1 = $this->subcuenta->get_by_codigo($_POST['codcontrapartida_'.$i], $eje0->codejercicio);
                              if($subc1)
                              {
                                 $partida->idcontrapartida = $subc1->idsubcuenta;
                                 $partida->codcontrapartida = $subc1->codsubcuenta;
                                 $partida->cifnif = $_POST['cifnif_'.$i];
                                 $partida->iva = floatval($_POST['iva_'.$i]);
                                 $partida->baseimponible = floatval($_POST['baseimp_'.$i]);
                              }
                              else
                              {
                                 $this->new_error_msg('Subcuenta '.$_POST['codcontrapartida_'.$i].' no encontrada.');
                                 $continuar = FALSE;
                              }
                           }
                        }

                        if( !$partida->save() )
                        {
                           $this->new_error_msg('Imposible guardar la partida de la subcuenta '.$_POST['codsubcuenta_'.$i].'.');
                           $continuar = FALSE;
                        }
                     }
                     else
                     {
                        $this->new_error_msg('Subcuenta '.$_POST['codsubcuenta_'.$i].' no encontrada.');
                        $continuar = FALSE;
                     }
                  }
               }
            }

            if( $continuar )
            {
               $this->asiento->concepto = '';

               $this->new_message("<a href='".$this->asiento->url()."'>Asiento</a> guardado correctamente!");
               $this->new_change('Asiento '.$this->asiento->numero, $this->asiento->url(), TRUE);


            }
            else
            {
               if( $this->asiento->delete() )
               {
                  $this->new_error_msg("¡Error en alguna de las partidas! Se ha borrado el asiento.");
               }
               else
                  $this->new_error_msg("¡Error en alguna de las partidas! Además ha sido imposible borrar el asiento.");
            }
         }
         else
         {
            $this->new_error_msg("¡Imposible guardar el asiento!");
         }
      }
   }
   public function url($busqueda = FALSE)
   {
      if($busqueda)
      {
         $codcliente = '';
         if($this->cliente)
         {
            $codcliente = $this->cliente->codcliente;
         }
         
         $url = $this->url()."&mostrar=".$this->mostrar
                 ."&query=".$this->query
                 ."&codserie=".$this->codserie
                 ."&codagente=".$this->codagente
                 ."&codcliente=".$codcliente
                 ."&desde=".$this->desde
                 ."&estado=".$this->estado
                 ."&hasta=".$this->hasta;
         
         return $url;
      }
      else
      {
         return parent::url();
      }
   }
   
   private function buscar_cliente()
   {
      /// desactivamos la plantilla HTML
      $this->template = FALSE;
      
      $cli0 = new cliente();
      $json = array();
      foreach($cli0->search($_REQUEST['buscar_cliente']) as $cli)
      {
         $json[] = array('value' => $cli->nombre, 'data' => $cli->codcliente);
      }
      
      header('Content-Type: application/json');
      echo json_encode( array('query' => $_REQUEST['buscar_cliente'], 'suggestions' => $json) );
   }
   
   public function paginas()
   {
      $url = $this->url(TRUE);
      $paginas = array();
      $i = 0;
      $num = 0;
      $actual = 1;
      
      if($this->mostrar == 'sinpagar')
      {
         $total = $this->total_sinpagar();
      }
      else if($this->mostrar == 'buscar')
      {
         $total = $this->num_resultados;
      }
      else
      {
         $total = $this->total_registros();
      }
      
      /// añadimos todas la página
      while($num < $total)
      {
         $paginas[$i] = array(
             'url' => $url."&offset=".($i*FS_ITEM_LIMIT),
             'num' => $i + 1,
             'actual' => ($num == $this->offset)
         );
         
         if($num == $this->offset)
         {
            $actual = $i;
         }
         
         $i++;
         $num += FS_ITEM_LIMIT;
      }
      
      /// ahora descartamos
      foreach($paginas as $j => $value)
      {
         $enmedio = intval($i/2);
         
         if( ($j>1 AND $j<$actual-5 AND $j!=$enmedio) OR ($j>$actual+5 AND $j<$i-1 AND $j!=$enmedio) )
         {
            unset($paginas[$j]);
         }
      }
      
      if( count($paginas) > 1 )
      {
         return $paginas;
      }
      else
      {
         return array();
      }
   }
   
   public function buscar_lineas()
   {
      /// cambiamos la plantilla HTML
      $this->template = 'ajax/ventas_lineas_facturas';
      
      $this->buscar_lineas = $_POST['buscar_lineas'];
      $linea = new linea_factura_cliente();
      
      if( isset($_POST['codcliente']) )
      {
         $this->lineas = $linea->search_from_cliente2($_POST['codcliente'], $this->buscar_lineas, $_POST['buscar_lineas_o']);
      }
      else
      {
         $this->lineas = $linea->search($this->buscar_lineas);
      }
   }
   
   private function share_extension()
   {
     
      $extensiones = array(
          array(
              'name' => 'facturas_cliente',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_cliente',
              'type' => 'button',
              'text' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> &nbsp; Facturas',
              'params' => ''
          ),
          array(
              'name' => 'facturas_agente',
              'page_from' => __CLASS__,
              'page_to' => 'admin_agente',
              'type' => 'button',
              'text' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> &nbsp; Facturas de cliente',
              'params' => ''
          ),
          array(
              'name' => 'facturas_articulo',
              'page_from' => __CLASS__,
              'page_to' => 'ventas_articulo',
              'type' => 'tab_button',
              'text' => '<span class="glyphicon glyphicon-list" aria-hidden="true"></span> &nbsp; Facturas de cliente',
              'params' => ''
          ),
      );
      foreach($extensiones as $ext)
      {
         $fsext0 = new fs_extension($ext);
         if( !$fsext0->save() )
         {
            $this->new_error_msg('Imposible guardar los datos de la extensión '.$ext['name'].'.');
         }
      }
   }
   
   public function total_sinpagar()
   {
      $data = $this->db->select("SELECT COUNT(idfactura) as total FROM facturascli WHERE pagada = false;");
      if($data)
      {
         return intval($data[0]['total']);
      }
      else
         return 0;
   }
   
   private function total_registros()
   {
      $data = $this->db->select("SELECT COUNT(idfactura) as total FROM facturascli;");
      if($data)
      {
         return intval($data[0]['total']);
      }
      else
         return 0;
   }
   
   private function buscar($order2)
   {
      $this->resultados = array();
      $this->num_resultados = 0;
      $query = $this->agente->no_html( mb_strtolower($this->query, 'UTF8') );
      $sql = " FROM facturascli ";
      $where = 'WHERE ';
      
      if($this->query != '')
      {
         $sql .= $where;
         if( is_numeric($query) )
         {
            $sql .= "(codigo LIKE '%".$query."%' OR numero2 LIKE '%".$query."%' "
                    . "OR observaciones LIKE '%".$query."%' OR cifnif LIKE '".$query."%')";
         }
         else
         {
            $sql .= "(lower(codigo) LIKE '%".$query."%' OR lower(numero2) LIKE '%".$query."%' "
                    . "OR lower(cifnif) LIKE '".$query."%' "
                    . "OR lower(observaciones) LIKE '%".str_replace(' ', '%', $query)."%')";
         }
         $where = ' AND ';
      }
      
      if($this->codagente != '')
      {
         $sql .= $where."codagente = ".$this->agente->var2str($this->codagente);
         $where = ' AND ';
      }
      
      if($this->cliente)
      {
         $sql .= $where."codcliente = ".$this->agente->var2str($this->cliente->codcliente);
         $where = ' AND ';
      }
      
      if($this->codserie != '')
      {
         $sql .= $where."codserie = ".$this->agente->var2str($this->codserie);
         $where = ' AND ';
      }
      
      if($this->desde != '')
      {
         $sql .= $where."fecha >= ".$this->agente->var2str($this->desde);
         $where = ' AND ';
      }
      
      if($this->hasta != '')
      {
         $sql .= $where."fecha <= ".$this->agente->var2str($this->hasta);
         $where = ' AND ';
      }
      
      if($this->estado == 'pagadas')
      {
         $sql .= $where."pagada";
         $where = ' AND ';
      }
      else if($this->estado == 'impagadas')
      {
         $sql .= $where."pagada = false";
         $where = ' AND ';
      }
      else if($this->estado == 'anuladas')
      {
         $sql .= $where."anulada = true";
         $where = ' AND ';
      }
      
      $data = $this->db->select("SELECT COUNT(idfactura) as total".$sql);
      if($data)
      {
         $this->num_resultados = intval($data[0]['total']);
         
         $data2 = $this->db->select_limit("SELECT *".$sql." ORDER BY ".$this->order.$order2, FS_ITEM_LIMIT, $this->offset);
         if($data2)
         {
            foreach($data2 as $d)
            {
               $this->resultados[] = new factura_cliente($d);
            }
         }
         
         $data2 = $this->db->select("SELECT coddivisa,SUM(total) as total".$sql." GROUP BY coddivisa");
         if($data2)
         {
            $this->total_resultados_txt = 'Suma total de los resultados:';
            
            foreach($data2 as $d)
            {
               $this->total_resultados[] = array(
                   'coddivisa' => $d['coddivisa'],
                   'total' => floatval($d['total'])
               );
            }
         }
         
         if($this->codagente !== '')
         {
            /// calculamos la comisión del empleado
            $data2 = $this->db->select("SELECT SUM(neto*porcomision/100) as total".$sql);
            if($data2)
            {
               $this->total_resultados_comision = floatval($data2[0]['total']);
            }
         }
      }
   }
   
   private function delete_factura()
   {
      $fact = $this->factura->get($_GET['delete']);
      if($fact)
      {
         /// ¿Sumamos stock?
         $art0 = new articulo();
         foreach($fact->get_lineas() as $linea)
         {
            if( is_null($linea->idalbaran) )
            {
               $articulo = $art0->get($linea->referencia);
               if($articulo)
               {
                  $articulo->sum_stock($fact->codalmacen, $linea->cantidad);
               }
            }
         }
         
         if( $fact->delete() )
         {
            $this->new_message("Factura de venta ".$fact->codigo." eliminada correctamente.", TRUE);
            $this->clean_last_changes();
         }
         else
            $this->new_error_msg("¡Imposible eliminar la factura!");
      }
      else
         $this->new_error_msg("Factura no encontrada.");
   }
}
