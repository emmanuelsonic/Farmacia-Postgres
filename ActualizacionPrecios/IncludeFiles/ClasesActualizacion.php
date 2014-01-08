<?php
include($path.'../Clases/class.php');
class Actualizacion{

	function IniciarPantallaP1(){
		
		$query="select Id,GrupoTerapeutico 
				from mnt_grupoterapeutico
				where GrupoTerapeutico <> '--'";
		$resp=pg_query($query);
		return($resp);
	
	}//Iniciar Pantalla Paso1


	function IniciarPantallaP2($IdTerapeutico){
		
		$query="select Id,Codigo,Nombre,Concentracion,FormaFarmaceutica,Presentacion
				from farm_catalogoproductos
				where IdEstado='H'
				and IdTerapeutico=".$IdTerapeutico;
		$resp=pg_query($query);
		return($resp);
		
	}//Iniciar Pantalla Paso 2
	
	
	function ObtenerPrecioActual($IdMedicina,$Ano){
		$Ano2=$Ano-1;
		// La Query era hacia la tabla farm_preciosxano
		$query="select precioactual 
				from farm_catalogoproductos
				where id='$IdMedicina'
				and (Ano='$Ano' or Ano ='$Ano2')
				order by Ano desc
				limit 1";
		$resp=pg_fetch_row(pg_query($query));
		if($resp[0]!=NULL and $resp[0]!=''){$respuesta=$resp[0];}else{$respuesta=false;}
		return($respuesta);
		
	}//Obtener Precio Actual
	
	
	function ObtenerPrecio($IdMedicina,$Ano){
		$query="select Precio 
				from farm_preciosxano
				where IdMedicina='$IdMedicina'
				and Ano='$Ano'";
		$resp=pg_query($query);
		if($resp=pg_fetch_array($resp)){
			$respuesta=true;
		}else{
			$respuesta=false;
		}
		
		return($respuesta);
	}//Obtener PRecio para saber si existe
	
	
	function IntroducirPrecio($IdMedicina,$Precio,$Ano,$IdUsuarioReg){
		$query="insert into farm_preciosxano(IdMedicina,Precio,Ano,IdUsuarioReg,FechaHoraReg,IdUsuarioMod,FechaHoraMod) values('$IdMedicina','$Precio','$Ano','$IdUsuarioReg',current_timestamp,'$IdUsuarioReg',current_timestamp)";
		pg_query($query);
	}//Introducir PRecio
	
	
	function ActualizarPrecio($IdMedicina,$Precio,$Ano,$IdUsuarioReg){
		$query="update farm_preciosxano set Precio='$Precio',IdUsuarioMod='$IdUsuarioReg',FechaHoraMod=current_timestamp where IdMedicina='$IdMedicina' and Ano='$Ano'";
		pg_query($query);
		
	}//Actualizar el precio
	
	function ObtenerUnidadMedida($IdMedicina){
		$query="select UnidadesContenidas
				from farm_catalogoproductos
				inner join farm_unidadmedidas
				on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.IdUnidadMedida
				where IdMedicina=".$IdMedicina;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}
	
}//Clase Actualizacion
?>