<?php
session_start();
include('../Clases/class.php');
conexion::conectar();
$Busqueda = $_GET['q'];

$IdEstablecimiento = $_SESSION["IdEstablecimiento"];
$IdModalidad = $_SESSION["IdModalidad"];

$querySelect = "SELECT Codigo, Nombre, Concentracion, fcp.Id, FormaFarmaceutica, Presentacion
                FROM farm_catalogoproductos AS fcp
                INNER JOIN farm_catalogoproductosxestablecimiento fcpe
                ON fcpe.IdMedicina=fcp.Id
                WHERE (Nombre like '%$Busqueda%' or Codigo ='$Busqueda')
                AND Condicion='H'
                AND fcpe.IdEstablecimiento=$IdEstablecimiento
                AND fcpe.IdModalidad=$IdModalidad";
$resp = pg_query($querySelect);
while ($row = pg_fetch_array($resp)) {
    $Nombre = $row["nombre"] . " - " . $row["concentracion"] . " - " . $row["formafarmaceutica"] . " - " . $row["presentacion"];
    $IdMedicina = $row["id"];
    $Codigo = $row["codigo"];
    ?>
    <li onselect="this.text.value = '<?php echo htmlentities($Nombre); ?>';
            $('IdMedicina').value = '<?php echo $IdMedicina; ?>';
            valida();"> 
        <span><?php echo $Codigo; ?></span>
        <strong><?php echo htmlentities($Nombre); ?></strong>
    </li>
    <?php
}
conexion::desconectar();
?>