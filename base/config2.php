<?php

if( !defined('FS_TMP_NAME') )
{
   define('FS_TMP_NAME', '');
}

if( !defined('FS_PATH') )
{
   define('FS_PATH', '');
}

if(FS_TMP_NAME != '' AND !file_exists('tmp/'.FS_TMP_NAME) )
{
   if( !file_exists('tmp') )
   {
      if( mkdir('tmp') )
      {
         file_put_contents('tmp/index.php', "<?php\necho 'acceso denegado ¡¡¡';");
      }
   }
   
   mkdir('tmp/'.FS_TMP_NAME);
}


if( file_exists('tmp/'.FS_TMP_NAME.'config2.ini') )
{
   $GLOBALS['config2'] = parse_ini_file('tmp/'.FS_TMP_NAME.'config2.ini');
   
   if( !isset($GLOBALS['config2']['cost_is_average']) )
   {
      $GLOBALS['config2']['cost_is_average'] = 1;
   }
   
   if( !isset($GLOBALS['config2']['nf0']) )
   {
      $GLOBALS['config2']['nf0'] = 2;
      $GLOBALS['config2']['nf1'] = '.';
      $GLOBALS['config2']['nf2'] = ' ';
      $GLOBALS['config2']['pos_divisa'] = 'right';
   }
   
   if( !isset($GLOBALS['config2']['nf0_art']) )
   {
      $GLOBALS['config2']['nf0_art'] = 4;
   }
   
   if( !isset($GLOBALS['config2']['homepage']) )
   {
      $GLOBALS['config2']['homepage'] = 'admin_home';
   }
   
   if( !isset($GLOBALS['config2']['check_db_types']) )
   {
      $GLOBALS['config2']['check_db_types'] = 0;
   }
   
   if( !isset($GLOBALS['config2']['stock_negativo']) )
   {
      $GLOBALS['config2']['stock_negativo'] = 0;
      $GLOBALS['config2']['ventas_sin_stock'] = 1;
   }
   
   if( !isset($GLOBALS['config2']['precio_compra']) )
   {
      $GLOBALS['config2']['precio_compra'] = 'coste';
      $GLOBALS['config2']['ip_whitelist'] = '*';
   }
   
   if( !isset($GLOBALS['config2']['iva']) )
   {
      $GLOBALS['config2']['iva'] = 'IVA';
      $GLOBALS['config2']['irpf'] = 'IRPF';
   }
   
   if( !isset($GLOBALS['config2']['libros_contables']) )
   {
      $GLOBALS['config2']['libros_contables'] = 1;
      $GLOBALS['config2']['foreign_keys'] = 1;
   }
   
   if( !isset($GLOBALS['config2']['new_codigo']) )
   {
      $GLOBALS['config2']['new_codigo'] = 'eneboo';
   }
   
   if( !isset($GLOBALS['config2']['factura']) )
   {
      $GLOBALS['config2']['factura'] = 'factura';
      $GLOBALS['config2']['facturas'] = 'facturas';
      $GLOBALS['config2']['numero2'] = 'número 2';
   }
   
   if( !isset($GLOBALS['config2']['factura_simplificada']) )
   {
      $GLOBALS['config2']['factura_simplificada'] = 'factura simplificada';
   }
   
   if( !isset($GLOBALS['config2']['factura_rectificativa']) )
   {
      $GLOBALS['config2']['factura_rectificativa'] = 'factura rectificativa';
   }
   
   if( !isset($GLOBALS['config2']['db_integer']) )
   {
      $GLOBALS['config2']['db_integer'] = 'INTEGER';
   }
   
   if( !isset($GLOBALS['config2']['serie']) )
   {
      $GLOBALS['config2']['serie'] = 'serie';
      $GLOBALS['config2']['series'] = 'series';
   }
}
else
{
   $GLOBALS['config2'] = array(
       'zona_horaria' => 'America/Lima',
   'nf0' => 2,
   'nf0art' => 2,
   'nf1' => '.',
   'nf2' => ' ',
   'pos_divisa' => 'left',
   'factura' => 'factura',
   'facturas' => 'facturas',
   'factura_simplificada' => 'factura simplificada',
   'factura_rectificativa' => 'factura rectificativa',
   'albaran' => 'albarán',
   'albaranes' => 'albaranes',
   'pedido' => 'pedido',
   'pedidos' => 'pedidos',
   'presupuesto' => 'presupuesto',
   'presupuestos' => 'presupuestos',
   'provincia' => 'provincia',
   'apartado' => 'apartado',
   'cifnif' => 'DNI/RUC',
   'iva' => 'IGV',
   'irpf' => 'IRPF',
   'numero2' => 'número 2',
   'serie' => 'serie',
   'series' => 'series',
   'cost_is_average' => 1,
   'precio_compra' => 'pvp',
   'homepage' => 'admin_home',
   'check_db_types' => 0,
   'stock_negativo' => 1,
   'ventas_sin_stock' => 1,
   'ip_whitelist' => '*',
   'libros_contables' => 1,
   'foreign_keys' => 1,
   'new_codigo' => 'new',
   'db_integer' => 'INTEGER',
   );
}

foreach($GLOBALS['config2'] as $i => $value)
{
   if($i == 'zona_horaria')
   {
      date_default_timezone_set($value);
   }
   else
   {
      define('FS_'.strtoupper($i), $value);
   }
}

if( !file_exists('modulos') )
{
   mkdir('modulos');
   chmod('modulos', octdec(777));
}

/// Cargamos la lista de modulos activos
$GLOBALS['modulos'] = array();
if( file_exists('tmp/enabled_plugins.list') )
{
   rename('tmp/enabled_plugins.list', 'tmp/'.FS_TMP_NAME.'enabled_plugins.list');
}

if( file_exists('tmp/'.FS_TMP_NAME.'enabled_plugins.list') )
{
   $list = explode(',', file_get_contents('tmp/'.FS_TMP_NAME.'enabled_plugins.list'));
   if($list)
   {
      foreach($list as $f)
      {
         if( file_exists('modulos/'.$f) )
         {
            $GLOBALS['modulos'][] = $f;
         }
      }
   }
}

/// cargamos las funciones de los modulos
foreach($GLOBALS['modulos'] as $plug)
{
   if( file_exists('modulos/'.$plug.'/functions.php') )
   {
      require_once 'modulos/'.$plug.'/functions.php';
   }
}