<?php
include('../../Clases/class.php');

class IngresoEmpleados{
	function ObtenerIdEmpleado($IdTipoEmpleado,$IdEstablecimiento){
		$query="select Correlativo 
				from mnt_empleado
				where Id_Tipo_Empleado='$IdTipoEmpleado'
                                and Id_Establecimiento=".$IdEstablecimiento."
				order by Correlativo desc
				limit 1";
		$resp=pg_query($query);
		if($row=pg_fetch_row($resp)){
			$Correlativo=$row[0];
			$SiguienteCorrelativo=$Correlativo+1;
		}else{
			$SiguienteCorrelativo=1;
		}
		
		if($SiguienteCorrelativo < 10){ 
			$IdEmpleado=$IdTipoEmpleado."000".$SiguienteCorrelativo;
		}elseif($SiguienteCorrelativo<100){
			$IdEmpleado=$IdTipoEmpleado."00".$SiguienteCorrelativo;			
		}elseif($SiguienteCorrelativo<1000){
			$IdEmpleado=$IdTipoEmpleado."0".$SiguienteCorrelativo;
		}else{
			$IdEmpleado=$IdTipoEmpleado."".$SiguienteCorrelativo;	
		}
		
		$Respuesta=$IdEmpleado."/".$SiguienteCorrelativo;
		return($Respuesta);		
	}//Obtener IdEmpleado
	
	
    function VerificaCodigoFarmacia($CodigoFarmacia){
        $query="select * from mnt_empleado where Codigo_Farmacia='$CodigoFarmacia'";
        $resp=pg_query($query);
        if(pg_fetch_array($resp)){
            $respuesta=true;   
        }else{
            $respuesta=false;            
        }
        
        return($respuesta);
    }                            
    
	function GuardarEmpleado($IdEmpleado,$IdEstablecimiento,$IdTipoEmpleado,$NombreEmpleado,$Correlativo,$CodigoFarmacia,$IdPersonal){
		$query="insert into mnt_empleado(IdEmpleado,Id_Establecimiento,Id_Tipo_Empleado,NombreEmpleado,Correlativo,Codigo_Farmacia,IdUsuarioReg,FechaHoraReg) values('$IdEmpleado','$IdEstablecimiento','$IdTipoEmpleado','$NombreEmpleado','$Correlativo','$CodigoFarmacia','$IdPersonal',now())";
		pg_query($query);

	}	
	
	function ObtenerDatos($IdEmpleado,$IdEstablecimiento){
	   $SQL="select IdEmpleado,NombreEmpleado,Codigo_Farmacia
			from mnt_empleado
			where IdEmpleado='$IdEmpleado'
                        and Id_Establecimiento=".$IdEstablecimiento;
	   $resp=pg_query($SQL);
	   return($resp);
	}

	function Empleados($NombreEmpleado,$IdEstablecimiento){
	   $SQL="select * from mnt_empleado 
		where (Id_Tipo_Empleado=4 or Id_Tipo_Empleado=5)
		and NombreEmpleado like '%$NombreEmpleado%'
                and Id_Establecimiento=".$IdEstablecimiento."
		order by Codigo_Farmacia desc";
	   $resp=pg_query($SQL);
	  return($resp);
	}

	
}//Clase Ingreso Empleados
?>