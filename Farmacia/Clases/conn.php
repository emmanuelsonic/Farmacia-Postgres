<?php
class conexion2 {
	//public $coneccion;
    function conectar()
      {
         $coneccion=pg_connect("host=192.168.100.253 port=5432 dbname=SIAP user=postgres password=b4s3s14p");
         return $coneccion;
      }
      
	function consulta($sql)
    {
        $coneccion=$this->conectar();
        if(!$coneccion)
			return 0; //Si no se pudo conectar
        else
        {
			//Valor es resultado de base de dato y Consulta es la Consulta a realizar
            $resultado=pg_query($coneccion,$sql);
            return $resultado;// retorna si fue afectada una fila
        }
    }
	  
	function desconectar()
	{
		pg_close();
	}

}
?>