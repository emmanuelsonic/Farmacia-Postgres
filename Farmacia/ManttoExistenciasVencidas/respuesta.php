<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<li onselect="this.text.value = 'Error de Sesion!'; window.location='../signIn.php'"><strong>ERROR_SESSION</strong></li>
<?php }else{
include('../Clases/class.php');
conexion::conectar();
$Busqueda=$_GET['q'];
$IdAreaOrigen=$_GET['IdAreaOrigen'];
$IdModalidad=$_SESSION["IdModalidad"];

$querySelect="select Nombre, Concentracion, fcp.Id as idmedicina, FormaFarmaceutica,Presentacion,Descripcion, DivisorMedicina,UnidadesContenidas
			from farm_catalogoproductos fcp
			inner join farm_catalogoproductosxestablecimiento fcpe
			on fcpe.IdMedicina=fcp.Id
			inner join farm_unidadmedidas fu
			on fu.Id = fcp.IdUnidadMedida
			left join farm_divisores fd
			ON ( fd.IdMedicina = fcp.Id AND fd.IdModalidad =".$_SESSION["IdModalidad"]." )

                        where (Nombre like '%$Busqueda%' or Codigo='$Busqueda')
                        and Condicion='H'
                        and fcpe.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
                        and fcpe.IdModalidad=$IdModalidad
                        and IdTerapeutico is not null";
	$resp=pg_query($querySelect);
while($row=pg_fetch_array($resp,null,PGSQL_ASSOC)){
	$Nombre=$row["nombre"]." - ".$row["concentracion"]." - ".$row["formafarmaceutica"]." - ".$row["presentacion"];
	$IdMedicina=$row["idmedicina"];

	$UnidadMedida=$row["descripcion"];
	$UnidadesContenidas=$row["unidadescontenidas"];

	if(($row["divisormedicina"]!=NULL and $row["divisormedicina"]!='') and ($IdAreaOrigen!=12 and $IdAreaOrigen!=0)){
		$Divisor=$row["divisormedicina"];
		$UnidadMedida="[unidades]";
	}else{$Divisor=0;}

?>
<li onselect="this.text.value = '<?php echo htmlentities($Nombre);?>';$('IdMedicina').value='<?php echo $IdMedicina;?>';Habilita(<?php echo $IdMedicina; ?>);$('UnidadMedida').innerHTML='<?php echo $UnidadMedida;?>'; $('Divisor').value=<?php echo $Divisor;?>; $('UnidadesContenidas').value=<?php echo $UnidadesContenidas;?>"> 
	<span><?php echo $IdMedicina;?></span>
	<strong><?php echo htmlentities($Nombre);?></strong>
</li>
<?php
}
conexion::desconectar();
}//error sesion

?>