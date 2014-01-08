<?php session_start();
include('../Clases/class.php');
conexion::conectar();
$Busqueda=$_GET['q'];
$querySelect="select * 
		from farm_catalogoproductos fcp
		inner join farm_catalogoproductosxestablecimiento fcpe
		on fcpe.IdMedicina=fcp.Id
		where (Nombre like '%$Busqueda%' or Codigo='$Busqueda') 
                and fcpe.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]." 
                and IdModalidad=".$_SESSION["IdModalidad"];
	$resp=pg_query($querySelect);
while($row=pg_fetch_array($resp)){
	$Nombre=htmlentities($row["nombre"])." - ".$row["concentracion"]." - ".$row["formafarmaceutica"];
	$IdMedicina=$row["idmedicina"];

?>
<li onselect="this.text.value = '<?php echo $Nombre;?>'; $('IdMedicina').value = '<?php echo $IdMedicina;?>';"> 
	<span><?php echo "<i>".$IdMedicina."</i>";?></span>
	<strong><?php echo $Nombre;?></strong>
</li>
<?php
}
conexion::desconectar();
?>