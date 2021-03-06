<?php

class serie extends fs_model
{
   
   public $codserie;
   public $descripcion;
   public $siniva;
   public $irpf;
   public $codejercicio;
   public $numfactura;
   
   public function __construct($s=FALSE)
   {
      parent::__construct('series');
      if($s)
      {
         $this->codserie = $s['codserie'];
         $this->descripcion = $s['descripcion'];
         $this->siniva = $this->str2bool($s['siniva']);
         $this->irpf = floatval($s['irpf']);
         $this->codejercicio = $s['codejercicio'];
         $this->numfactura = max( array(1, intval($s['numfactura'])) );
      }
      else
      {
         $this->codserie = '';
         $this->descripcion = '';
         $this->siniva = FALSE;
         $this->irpf = 0;
         $this->codejercicio = NULL;
         $this->numfactura = 1;
      }
   }
   
   public function install()
   {
      $this->clean_cache();
      return "INSERT INTO ".$this->table_name." (codserie,descripcion,siniva,irpf) VALUES "
              . "('A','SERIE A',FALSE,'0'),('R','RECTIFICATIVAS',FALSE,'0');";
   }
   
   public function url()
   {
      if( is_null($this->codserie) )
      {
         return 'index.php?page=contabilidad_series';
      }
      else
         return 'index.php?page=contabilidad_series#'.$this->codserie;
   }
   
   public function is_default()
   {
      return ( $this->codserie == $this->default_items->codserie() );
   }
   public function get($cod)
   {
      $serie = $this->db->select("SELECT * FROM ".$this->table_name." WHERE codserie = ".$this->var2str($cod).";");
      if($serie)
      {
         return new serie($serie[0]);
      }
      else
         return FALSE;
   }
   
   public function exists()
   {
      if( is_null($this->codserie) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE codserie = ".$this->var2str($this->codserie).";");
   }
   
   public function test()
   {
      $status = FALSE;
      
      $this->codserie = trim($this->codserie);
      $this->descripcion = $this->no_html($this->descripcion);
      
      if($this->numfactura < 1)
      {
         $this->numfactura = 1;
      }
      
      if( !preg_match("/^[A-Z0-9]{1,2}$/i", $this->codserie) )
      {
         $this->new_error_msg("Código de serie no válido.");
      }
      else if( strlen($this->descripcion) < 1 OR strlen($this->descripcion) > 100 )
      {
         $this->new_error_msg("Descripción de serie no válida.");
      }
      else
         $status = TRUE;
      
      return $status;
   }
   
   public function save()
   {
      if( $this->test() )
      {
         $this->clean_cache();
         
         if( $this->exists() )
         {
            $sql = "UPDATE ".$this->table_name." SET descripcion = ".$this->var2str($this->descripcion)
                    .", siniva = ".$this->var2str($this->siniva)
                    .", irpf = ".$this->var2str($this->irpf)
                    .", codejercicio = ".$this->var2str($this->codejercicio)
                    .", numfactura = ".$this->var2str($this->numfactura)
                    ." WHERE codserie = ".$this->var2str($this->codserie).";";
         }
         else
         {
            $sql = "INSERT INTO ".$this->table_name." (codserie,descripcion,siniva,irpf,codejercicio,numfactura) VALUES "
                    . "(".$this->var2str($this->codserie)
                    . ",".$this->var2str($this->descripcion)
                    . ",".$this->var2str($this->siniva)
                    . ",".$this->var2str($this->irpf)
                    . ",".$this->var2str($this->codejercicio)
                    . ",".$this->var2str($this->numfactura).");";
         }
         
         return $this->db->exec($sql);
      }
      else
         return FALSE;
   }
   
   public function delete()
   {
      $this->clean_cache();
      return $this->db->exec("DELETE FROM ".$this->table_name." WHERE codserie = ".$this->var2str($this->codserie).";");
   }
   
   private function clean_cache()
   {
      $this->cache->delete('m_serie_all');
   }
   
   public function all()
   {
      $serielist = $this->cache->get_array('m_serie_all');
      if(!$serielist)
      {
         $series = $this->db->select("SELECT * FROM ".$this->table_name." ORDER BY codserie ASC;");
         if($series)
         {
            foreach($series as $s)
               $serielist[] = new serie($s);
         }
         $this->cache->set('m_serie_all', $serielist);
      }
      
      return $serielist;
   }
}
