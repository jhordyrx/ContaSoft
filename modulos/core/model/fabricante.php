<?php

class fabricante extends fs_model
{
   public $codfabricante;
   public $nombre;
   
   public function __construct($f=FALSE)
   {
      parent::__construct('fabricantes');
      if($f)
      {
         $this->codfabricante = $f['codfabricante'];
         $this->nombre = $f['nombre'];
      }
      else
      {
         $this->codfabricante = NULL;
         $this->nombre = '';
      }
   }
   
   protected function install()
   {
      $this->clean_cache();
      return "INSERT INTO ".$this->table_name." (codfabricante,nombre) VALUES ('OEM','OEM');";
   }
   
   public function url()
   {
      if( is_null($this->codfabricante) )
      {
         return "index.php?page=ventas_fabricantes";
      }
      else
         return "index.php?page=ventas_fabricante&cod=".urlencode($this->codfabricante);
   }
   
   public function nombre($len = 12)
   {
      if( mb_strlen($this->nombre) > $len )
      {
         return substr($this->nombre, 0, $len).'...';
      }
      else
      {
         return $this->nombre;
      }
   }
   
   public function get($cod)
   {
      $f = $this->db->select("SELECT * FROM ".$this->table_name." WHERE codfabricante = ".$this->var2str($cod).";");
      if($f)
      {
         return new fabricante($f[0]);
      }
      else
         return FALSE;
   }
   
   public function get_articulos($offset=0, $limit=FS_ITEM_LIMIT)
   {
      $articulo = new articulo();
      return $articulo->all_from_fabricante($this->codfabricante, $offset, $limit);
   }
   
   public function exists()
   {
      if( is_null($this->codfabricante) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE codfabricante = ".$this->var2str($this->codfabricante).";");
   }
   
   public function test()
   {
      $status = FALSE;
      
      $this->codfabricante = trim($this->codfabricante);
      $this->nombre = $this->no_html($this->nombre);
      
      if( strlen($this->codfabricante) < 1 OR strlen($this->codfabricante) > 8 )
      {
         $this->new_error_msg("Código de fabricante no válido. Deben ser entre 1 y 8 caracteres.");
      }
      else if( strlen($this->nombre) < 1 OR strlen($this->nombre) > 100 )
      {
         $this->new_error_msg("Descripción de fabricante no válida.");
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
            $sql = "UPDATE ".$this->table_name." SET nombre = ".$this->var2str($this->nombre).
                    " WHERE codfabricante = ".$this->var2str($this->codfabricante).";";
         }
         else
         {
            $sql = "INSERT INTO ".$this->table_name." (codfabricante,nombre) VALUES ".
                    "(".$this->var2str($this->codfabricante).
                    ",".$this->var2str($this->nombre).");";
         }
         
         return $this->db->exec($sql);
      }
      else
         return FALSE;
   }
   
   public function delete()
   {
      $this->clean_cache();
      $sql = "DELETE FROM ".$this->table_name." WHERE codfabricante = ".$this->var2str($this->codfabricante).";"
              . "UPDATE articulos SET codfabricante = NULL WHERE codfabricante = ".$this->var2str($this->codfabricante).";";
 
      return $this->db->exec($sql);
   }
   
   private function clean_cache()
   {
      $this->cache->delete('m_fabricante_all');
   }
   
  public function all()
   {
      $fablist = $this->cache->get_array('m_fabricante_all');
      if(!$fablist)
      {
         $data = $this->db->select("SELECT * FROM ".$this->table_name." ORDER BY nombre ASC;");
         if($data)
         {   
            foreach ($data as $d)
            {
             $fablist[] = new fabricante($d);
            }
         }
         $this->cache->set('m_fabricante_all', $fablist);
      }
      
      return $fablist;
   }
      
   public function search($query)
   {
      $fablist = array();
      $query = $this->no_html( mb_strtolower($query, 'UTF8') );
      
      $fabricantes = $this->db->select("SELECT * FROM ".$this->table_name." WHERE lower(nombre) LIKE '%".$query."%' ORDER BY nombre ASC;");
      if($fabricantes)
      {
         foreach($fabricantes as $f)
         {
            $fablist[] = new fabricante($f);
         }
      }
      
      return $fablist;
   }
}
