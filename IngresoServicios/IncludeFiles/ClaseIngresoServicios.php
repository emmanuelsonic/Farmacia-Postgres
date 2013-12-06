<?php
include('../../Clases/class.php');

class IngresoEmpleados{

    function VerificarServicio($CodigoServicio){
        $query="select * from mnt_subespecialidad where CodigoFarmacia='$CodigoServicio'";
        $resp=pg_query($query);
        if(pg_fetch_array($resp)){
            $respuesta=true;   
        }else{
            $respuesta=false;            
        }
        
        return($respuesta);
    }                            
    
	
	function IngresarServicio($CodigoServicio,$NombreServicio,$IdUsuarioReg,$IdEstablecimiento,$IdServicio){
		
		$NombreServicio2=strtoupper($NombreServicio);

		$query2="insert into mnt_subservicio (IdServicio,NombreSubServicio,IdEspecialidad,IdUsuarioReg,FechaHoraReg,CodigoFarmacia) values('$IdServicio','$NombreServicio2','1','$IdUsuarioReg',now(),'$CodigoServicio')";
		pg_query($query2);

		$Id=pg_insert_id();
	
		$SQL="insert into mnt_subservicioxestablecimiento (IdSubServicio,IdEstablecimiento,IdUsuarioReg,FechaHoraReg) values('$Id','$IdEstablecimiento','$IdUsuarioReg',now())";
		pg_query($SQL);
		

	}	
	
	
	
}//Clase Ingreso Empleados
?>