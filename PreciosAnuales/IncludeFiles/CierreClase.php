<?php
include('../../Clases/class.php');
class Proceso{
	function Verificar($Ano){
		$query="select Nombre,DATE_FORMAT(date(FechaHoraReg),'%d-%m-%Y') as Fecha
				from farm_usuarios
				inner join farm_cierre
				on farm_cierre.IdUsuarioReg=farm_usuarios.IdPersonal
				
				where AnoCierre='$Ano'";
		$resp=pg_query($query);
		return($resp);
	}



	function ObtenerMedicinasGeneral(){
		$query="select IdMedicina 
				from farm_catalogoproductos
				where IdHospital <> 0";
		$resp=pg_query($query);
		return($resp);
		
	}//obtener medicina general


	function VerificaExistencia($IdMedicina,$Ano){
		$query="select * 
				from farm_preciosxano 
				where Ano = $Ano
				and IdMedicina=".$IdMedicina;
		$resp=pg_query($query);
		
		if($row=pg_fetch_array($resp)){
			return true;
		}else{
			return false;
		}
			
	}//Verifica Existencia


	function ObtenerPrecioAnterior($IdMedicina){
	
			$query="select * 
				from farm_preciosxano 
				where Ano = year(curdate())
				and IdMedicina=".$IdMedicina;
		$resp=pg_fetch_array(pg_query($query));
			
			return($resp["Precio"]);
	
	}

	function ConfigurarPrecio($IdMedicina,$Precio,$IdPersonal,$Ano){
		$query="insert into farm_preciosxano (IdMedicina,Precio,Ano,IdUsuarioReg,FechaHoraReg) values('$IdMedicina','$Precio','$Ano','$IdPersonal',now())";
		$resp=pg_query($query);
		
	}

	function VerificarPeriodo($Periodo){
		$query="select Nombre,DATE_FORMAT(date(FechaHoraReg),'%d-%m-%Y') as Fecha
				from farm_usuarios
				inner join farm_cierre
				on farm_cierre.IdUsuarioReg=farm_usuarios.IdPersonal
				
				where MesCierre='$Periodo'";
		$resp=pg_query($query);
		return($resp);
	}

	function CierreMes($Periodo,$IdPersonal){
		$query="insert into farm_cierre (MesCierre,IdUsuarioReg,FechaHoraReg) values('$Periodo','$IdPersonal',now())";
		pg_query($query);
		
	}//Cierre


	
}//clase	


?>