<?php

session_start();
include '../Clases/class.php';

$opcionSeleccionada = $_GET["valor"];

$IdEstablecimiento = $_SESSION["IdEstablecimiento"];
$IdModalidad = $_SESSION["IdModalidad"];

switch ($_GET["Combo"]) {

    case "mnt_areafarmacia":
        $conexion = new conexion;
        $conexion->conectar();
        $consulta = pg_query("SELECT mafe.IdArea,maf.Area
				FROM mnt_areafarmacia maf
                                inner join mnt_areafarmaciaxestablecimiento mafe
                                on mafe.IdArea=maf.Id
				inner join mnt_farmacia
				on mnt_farmacia.Id=maf.IdFarmacia
				WHERE mnt_farmacia.Id='$opcionSeleccionada'
				and maf.Id <> '7'
				and mafe.Habilitado='S'
                                and mafe.IdEstablecimiento=$IdEstablecimiento
                                and mafe.IdModalidad=$IdModalidad") or die(pg_error());

        $conexion->desconectar();

        // Comienzo a imprimir el select
        echo "<select name='area' id='area'>";
        echo "<option value='0'>TODAS LAS AREAS</option>";
        while ($registro = pg_fetch_row($consulta)) {
            // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
            $registro[1] = htmlentities($registro[1]);
            // Imprimo las opciones del select
            echo "<option value='" . $registro[0] . "'>" . $registro[1] . "</option>";
        }
        echo "</select>";

        break;



    case "farm_catalogoproductos":
        $conexion = new conexion;
        $conexion->conectar();
        $consulta = pg_query("SELECT farm_catalogoproductos.id as IdMedicina,farm_catalogoproductos.Nombre,Concentracion,farm_catalogoproductos.FormaFarmaceutica,Presentacion, Codigo
			FROM farm_catalogoproductos
			inner join mnt_grupoterapeutico 
			on mnt_grupoterapeutico.Id=farm_catalogoproductos.IdTerapeutico
			inner join farm_catalogoproductosxestablecimiento fcpe
			on fcpe.IdMedicina=farm_catalogoproductos.Id
			WHERE mnt_grupoterapeutico.Id='$opcionSeleccionada' 
			and fcpe.IdEstablecimiento=$IdEstablecimiento
                        and fcpe.IdModalidad=$IdModalidad
			order by farm_catalogoproductos.Codigo") or die(pg_error());

        $conexion->desconectar();

        // Comienzo a imprimir el select
        echo "<select name='IdMedicina' id='IdMedicina'>";
        echo "<option value='0'>TODAS LAS MEDICINAS</option>";
        while ($registro = pg_fetch_row($consulta)) {
            // Convierto los caracteres conflictivos a sus entidades HTML correspondientes para su correcta visualizacion
            $registro[1] = htmlentities($registro[1]);
            // Imprimo las opciones del select
            echo "<option value='" . $registro[0] . "'>" . $registro[5] . ' ' . $registro[1] . ", " . $registro[2] . ' - ' . $registro[4] . "</option>";
        }
        echo "</select>";
        break;
}
?>