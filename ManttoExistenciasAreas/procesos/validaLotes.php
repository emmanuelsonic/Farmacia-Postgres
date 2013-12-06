<?php session_start();
if(!isset($_SESSION["IdPersonal"])){
  echo "ERROR_SESSION";
}else{
include("../../Clases/class.php");
conexion::conectar();



$SQL="select farm_catalogoproductos.Id,farm_catalogoproductos.Nombre,farm_catalogoproductos.FormaFarmaceutica,
					farm_catalogoproductos.Concentracion, mnt_areamedicina.IdArea
					from farm_catalogoproductos
					inner join mnt_areamedicina
					on mnt_areamedicina.IdMedicina=farm_catalogoproductos.Id
					where mnt_areamedicina.IdArea='".$_GET["area"]."'
					and farm_catalogoproductos.IdEstado='H'
					and IdTerapeutico=".$_GET["IdTerapeutico"];

$resp=pg_query($SQL);

$salida='';
$row=pg_fetch_array($resp);
$ultimo=pg_num_rows($resp);
$poss=0;
do{
$poss++;

	if($poss!=$ultimo){$cola='~';}else{$cola='';}
	$salida.=$row["id"]."".$cola;

}while($row=pg_fetch_array($resp));


echo $salida;





conexion::desconectar();
}
?>