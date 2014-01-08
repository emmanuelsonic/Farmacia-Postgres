<?php
class NuevoMedicamento{
	function ActualizarDatosGenerales($IdMedicina,$codigo,$nombre,$concentracion,$FormaFarmaceutica,$presentacion){
		$query="update farm_catalogoproductos set codigo='$codigo', nombre='$nombre', concentracion='$concentracion', formafarmaceutica='$FormaFarmaceutica', presentacion='$presentacion' where id='$IdMedicina'";
		pg_query($query);
	}
	function ActualizarGrupo($IdGrupo,$IdMedicina){
		$query="update farm_catalogoproductos set idterapeutico='$IdGrupo' where id='$IdMedicina'";
		pg_query($query);
	}
	function ActualizarUnidadMedida($IdUnidadMedida,$IdMedicina){
		$query="update farm_catalogoproductos set idunidadmedida='$IdUnidadMedida' where id='$IdMedicina'";
		pg_query($query);
	}

}//Fin de Clase

?>