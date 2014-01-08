<?php session_start();
include '../Clases/class.php';

$opcionSeleccionada = $_GET["valor"];

$IdEstablecimiento = $_SESSION["IdEstablecimiento"];
$IdModalidad = $_SESSION["IdModalidad"];

   switch($_GET["Combo"]){
	
	case "mnt_areafarmacia":
	$conexion=new conexion;	
	$conexion->conectar();
		
	$consulta=pg_query("SELECT distinct maf.IdArea,maf.Area
				FROM mnt_areafarmacia maf
                                inner join mnt_areafarmaciaxestablecimiento mafe
                                on mafe.IdArea=maf.IdArea
				inner join farm_recetas
				on farm_recetas.IdAreaOrigen=mafe.IdArea
				WHERE farm_recetas.IdFarmacia='$opcionSeleccionada'
				and mafe.IdArea <> '7'
				and mafe.IdEstablecimiento=$IdEstablecimiento
                                and mafe.IdModalidad=$IdModalidad
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
				and mafe.Habilitado='S'") or die(pg_error());
	
	$conexion->desconectar();
	
	// Comienzo a imprimir el select
	echo "<select name='area' id='area' >";
	echo "<option value='0'>TODAS LAS AREAS</option>";
	while($registro=pg_fetch_row($consulta))
	{
		// Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
		$registro[1]=htmlentities($registro[1]);
		// Imprimo las opciones del select
		echo "<option value='".$registro[0]."'>".$registro[1]."</option>";
	}			
	echo "</select>";

       break;


	
	case "farm_catalogoproductos":
	$conexion=new conexion;	
	$conexion->conectar();
	$consulta=pg_query("SELECT farm_catalogoproductos.IdMedicina,farm_catalogoproductos.Nombre,Concentracion,farm_catalogoproductos.FormaFarmaceutica,Presentacion,Codigo
			FROM farm_catalogoproductos
			inner join mnt_grupoterapeutico 
			on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
			inner join farm_catalogoproductosxestablecimiento fcpe
			on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina
			WHERE mnt_grupoterapeutico.IdTerapeutico='$opcionSeleccionada' 
			and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad
			order by farm_catalogoproductos.Codigo") or die(pg_error());
	
	$conexion->desconectar();
	
	// Comienzo a imprimir el select
	echo "<select name='IdMedicina' id='IdMedicina'>";
	echo "<option value='0'>TODAS LAS MEDICINAS</option>";
	while($registro=pg_fetch_row($consulta))
	{
		// Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
		$registro[1]=htmlentities($registro[1]);
		// Imprimo las opciones del select
		echo "<option value='".$registro[0]."'>".$registro[5].' '.$registro[1].", ".$registro[2].' - '.$registro[4]."</option>";
	}			
	echo "</select>";
	break;
   }	
	

?>