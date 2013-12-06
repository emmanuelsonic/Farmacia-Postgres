<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<li onselect="this.text.value = 'Error de Sesion!'; window.location='../signIn.php'"><strong>ERROR_SESSION</strong></li>
<?php }else{
include('../Clases/class.php');
conexion::conectar();
$Busqueda=$_GET['q'];
$IdArea=$_GET["IdArea"];

$querySelect="select distinct Nombre, Concentracion, fcp.id as IdMedicina, 
                    FormaFarmaceutica,Presentacion, Descripcion,UnidadesContenidas,
                    DivisorMedicina
			from farm_catalogoproductos fcp
			inner join farm_catalogoproductosxestablecimiento fcpe
			on fcpe.IdMedicina=fcp.Id
			inner join mnt_areamedicina
                        on mnt_areamedicina.IdMedicina=fcp.Id
			inner join farm_unidadmedidas fum
			on fum.Id=fcp.IdUnidadMedida
                        left join farm_divisores fd
                        on fcp.Id=fd.IdMedicina

where (Nombre like '%$Busqueda%' or Codigo='$Busqueda')
and Condicion='H'
and fcpe.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
and fcpe.IdModalidad=".$_SESSION["IdModalidad"]."
and mnt_areamedicina.Id = ".$IdArea."
and IdTerapeutico is not null";
	$resp=pg_query($querySelect);
while($row=pg_fetch_array($resp)){
	$Nombre=$row["nombre"]." - ".$row["concentracion"]." - ".$row["formafarmaceutica"]." - ".$row["presentacion"];
	$IdMedicina=$row["idmedicina"];
	$Descripcion="[".$row["descripcion"]."]";
        $UnidadesContenidas=$row["unidadescontenidas"];
        $Divisor=$row["divisormedicina"];
?>
<li onselect="this.text.value = '<?php echo htmlentities($Nombre);?>';$('IdMedicina').value='<?php echo $IdMedicina;?>';$('Descripcion').innerHTML='<?php echo $Descripcion;?>';$('UnidadesContenidas').value='<?php echo $UnidadesContenidas;?>';$('Divisor').value='<?php echo $Divisor;?>';"> 
	<span><?php echo $IdMedicina;?></span>
	<strong><?php echo htmlentities($Nombre);?></strong>
</li>
<?php
}
conexion::desconectar();
}//error sesion

?>