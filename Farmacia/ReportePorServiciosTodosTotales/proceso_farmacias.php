<?php session_start();

include '../Clases/class.php';

$opcionSeleccionada=$_GET["valor"];

$IdEstablecimiento=$_SESSION["IdEstablecimiento"];
$IdModalidad=$_SESSION["IdModalidad"];

    switch($_GET["Combo"]){

	
	case "farm_catalogoproductos":
		$tabla="farm_catalogoproductos";
	$conexion=new conexion;	
	$conexion->conectar();
	$consulta=pg_query("SELECT $tabla.id as IdMedicina,$tabla.Nombre,Concentracion,$tabla.FormaFarmaceutica, Presentacion, Codigo
				FROM $tabla
				inner join mnt_grupoterapeutico 
				on mnt_grupoterapeutico.Id=$tabla.IdTerapeutico
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=$tabla.Id
				WHERE mnt_grupoterapeutico.Id='$opcionSeleccionada' 
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				order by $tabla.Codigo") or die(pg_error());
	
	$conexion->desconectar();
	
	// Comienzo a imprimir el select
	echo "<select name='IdMedicina' id='IdMedicina'>";
	echo "<option value='0'>TODAS LAS MEDICINAS</option>";
	while($registro=pg_fetch_row($consulta))
	{
		// Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
		//$registro[1]=htmlentities($registro[1]);
		// Imprimo las opciones del select
		echo "<option value='".$registro[0]."'>".$registro[5].' '.htmlentities($registro[1])." ".$registro[2]." - ".htmlentities($registro[3]).'-'.htmlentities($registro[4])."</option>";
	}			
	echo "</select>";
	break;
    }

?>