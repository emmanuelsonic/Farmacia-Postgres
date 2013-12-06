<?php

session_start();
include("../Clases/class.php");
//

$link = conexion::Conecta();

$IdEstablecimiento = $_SESSION["IdEstablecimiento"];
$IdModalidad = $_SESSION["IdModalidad"];

if (isset($_GET["Enviar"]) and $_GET["Enviar"] == "si") {
    //
    $IdPersonal = $_GET['IdPersonal'];
    $IdPersonalD = $_GET['IdPersonalD'];

    $comentario = $_REQUEST["comentario"];

    $caracteres_prohibidos = array("'", "/", "<", ">", ";");
    $comentario = str_replace($caracteres_prohibidos, "*", $comentario);

    $insert = "insert into chat (comentario,fecha,IdPersonal,IdPersonalD,whosays,IdEstablecimiento, IdModalidad) 
                          values('" . htmlentities(utf8_decode($comentario)) . "',now(),'$IdPersonal','$IdPersonalD','$IdPersonal',$IdEstablecimiento, $IdModalidad)";
    //$update="update chat set IdEstado='R' where whosays='$IdPersonalD' and IdEstado<>'X'";

    if (trim($_REQUEST["comentario"]) != NULL) {
        $insert = pg_query($insert);
        //pg_query($update);
    }
    exit();
}

if (isset($_GET["Leer"]) and $_GET["Leer"] == "si") {
    $IdPersonal = $_GET['IdPersonal'];
    $IdPersonalD = $_GET['IdPersonalD'];

    header("Cache-Control: no-store, no-cache, must-revalidate");
    $select = "select comentario,(select Nombre from farm_usuarios where IdPersonal=chat.whosays) as fromusuario, fecha, IdPersonal 
                from chat 
                where ((whosays='$IdPersonal' and IdPersonalD='$IdPersonalD') or (whosays='$IdPersonalD' and IdPersonalD='$IdPersonal')) 
                and IdEstado <> 'X' 
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad
                order by id asc";

    $select = pg_query($select);
    //echo "<table>";
    while ($row = pg_fetch_array($select)) {
        if ($row["comentario"] != NULL) {

            if ($row["IdPersonal"] != $_SESSION["IdPersonal"]) {
                $bgcolor = "orange";
            } else {
                $bgcolor = "#3EA99F";
            }
            echo "<span style='background-color:" . $bgcolor . ";'><strong><span style='font-size:x-small;background-color:" . $bgcolor . ";'>" . htmlentities($row["fromusuario"]) . "</span> 
		</strong> - " . $row["comentario"] . "</span><br>";
        }
    }

    exit();
}

if (isset($_GET["Hash"]) and $_GET["Hash"] == "si") {

    $IdPersonal = $_GET['IdPersonal'];
    $IdPersonalD = $_GET['IdPersonalD'];

    header("Cache-Control: no-store, no-cache, must-revalidate");
    $max = "select max(id) 
            from chat
            and IdEstablecimiento=$IdEstablecimiento
            and IdModalidad=$IdModalidad";
    $max = pg_query($max);
    $max = pg_result($max, 0, 0);
    //
    $select = "select * 
                from chat 
                where id=" . $max . " 
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad
                limit 1";
    $select = pg_query($select);
    //
    $id = pg_result($select, 0, "id");
    $comentario = pg_result($select, 0, "comentario");
    $fecha = pg_result($select, 0, "fecha");
    //
    $hash = $id . $comentario . $fecha;
    if ($hash == NULL) {
        echo "vacio";
    } else {
        $hash = md5($id . $comentario . $fecha);
        echo $hash;
    }
    exit();
}


if (isset($_GET["Borrar"]) and $_GET["Borrar"] == "si") {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    //
    $IdPersonal = $_GET['IdPersonal'];
    $IdPersonalD = $_GET['IdPersonalD'];
    $insert = "update chat set IdEstado='X' 
                where ((whosays='$IdPersonal' and IdPersonalD='$IdPersonalD') or (whosays='$IdPersonalD' and IdPersonalD='$IdPersonal'))
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
    pg_query($insert);

    exit();
}

if (isset($_GET["Leido"]) and $_GET["Leido"] == "si") {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    //
    $IdPersonal = $_GET['IdPersonal'];
    $IdPersonalD = $_GET['IdPersonalD'];
    $update = "update chat set IdEstado='R' 
               where whosays='$IdPersonalD' 
               and IdEstado<>'X'
               and IdEstablecimiento=$IdEstablecimiento
               and IdModalidad=$IdModalidad";
    pg_query($update);

    exit();
}


if (isset($_GET["Nuevos"]) and $_GET["Nuevos"] == "si") {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    //
    $IdPersonal = $_GET['IdPersonal'];
    $IdPersonalD = $_GET['IdPersonalD'];

    $select2 = "select comentario,(select Nombre from farm_usuarios where IdPersonal=chat.whosays) as fromusuario, fecha 
                from chat 
                where (whosays='$IdPersonalD' and IdPersonalD='$IdPersonal') 
                and IdEstado = 'D'
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";

    $respNuevo = pg_query($select2);
    if ($rowNuevo = pg_fetch_array($respNuevo)) {
        $salida = 'S';
    } else {
        $salida = 'N';
    }
    echo $salida;
    exit();
}
?> 