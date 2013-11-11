<?php session_start();
include('../Clases/class.php');
conexion::conectar();
$Busqueda=$_GET['q'];
$querySelect="
select * from fos_user_user
where username like '%$Busqueda%'  and id_area_mod_estab=".$_SESSION["IdModalidad"]." and id_establecimiento=".$_SESSION["IdEstablecimiento"];
	$resp=pg_query($querySelect);
while($row=pg_fetch_array($resp)){
	$Nombre=$row["firstname"]. ' ' .$row["lastname"];
	$Usuario=$row["username"];
	$IdPersonal=$row["id"];

?>
<li onselect="this.text.value = '<?php echo $Nombre;?>'; $('IdPersonal').value = '<?php echo $IdPersonal;?>'; MostrarDetalle(<?php echo $IdPersonal; ?>);"> 
	<span><?php echo "<i>".$Usuario."</i>";?></span>
	<strong><?php echo $Nombre;?></strong>
</li>
<?php
}
conexion::desconectar();
?>