<?php 
include('../Clases/class.php');
conexion::conectar();
$Busqueda=$_GET['q'];
$querySelect="select IdTerapeutico,Codigo, Nombre, Concentracion, FormaFarmaceutica,farm_catalogoproductos.id as IdMedicina,Presentacion, fum.id as IdUnidadMedida, fum.Descripcion
			from farm_catalogoproductos
			inner join farm_unidadmedidas fum
			on fum.Id=farm_catalogoproductos.IdUnidadMedida
where (Nombre like '%$Busqueda%' or Codigo='$Busqueda') and (IdTerapeutico = 0)";

	$resp=pg_query($querySelect);
while($row=pg_fetch_array($resp)){
	$IdTerapeutico=$row["idterapeutico"];
	$Nombre=$row["nombre"]." - ".$row["concentracion"]." - ".$row["formafarmaceutica"]." - ".$row["presentacion"];
	$Nombre1=$row["nombre"];
	$IdMedicina=$row["idmedicina"];
	$Concentracion=$row["concentracion"];
	$Presentacion=$row["presentacion"];
	$Codigo=strtoupper($row["codigo"]);
	$IdUnidadMedida=$row["idunidadmedida"];
	$Descripcion=$row["descripcion"];
?>


<li onselect="this.text.value = '<?php echo htmlentities($Nombre1);?>'; $('CodigoMedicamento').value = '<?php echo $Codigo;?>';$('IdMedicina').value = '<?php echo $IdMedicina;?>';  $('concentracion').value='<?php echo $Concentracion;?>'; $('presentacion').value='<?php echo $Presentacion;?>';PegaCombo(<?php echo $IdMedicina;?>,<?php echo $IdUnidadMedida;?>,'<?php echo $Descripcion;?>'),ComboTerapeutico(<?php echo $IdTerapeutico;?>);" > 

	<span><?php echo $IdMedicina;?></span>
	<strong><?php echo htmlentities($Nombre);?></strong>
</li>
<?php
}
conexion::desconectar();
?>
