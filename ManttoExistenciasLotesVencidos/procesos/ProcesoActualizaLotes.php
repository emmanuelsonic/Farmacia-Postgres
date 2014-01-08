<?php
if(isset($_GET["Path"])){$path="../";}else{$path="";}
require($path.'../Clases/class2.php');
conexion::conectar();
function ActualizaDatosLotes($Lote,$PrecioLote,$Vencimiento,$LoteOld){
		if($Lote!=''){
			$Lote=strtoupper($Lote);
			$queryUpdate="update farm_lotes set Lote='$Lote' where IdLote='$LoteOld'";
			pg_query($queryUpdate);
		}
		if($PrecioLote!=0){
			$queryUpdate="update farm_lotes set PrecioLote='$PrecioLote' where IdLote='$LoteOld'";
			pg_query($queryUpdate);
		}
		
		if($Vencimiento!='Ventto.'){
			$queryUpdate="update farm_lotes set FechaVencimiento='$Vencimiento' where IdLote='$LoteOld'";
			pg_query($queryUpdate);
		}
		
}//funcion ActualizaDatosLotes


function ExisteLote($Lote){
   $SQL="select * from farm_lotes where Lote = '$Lote'";
   $resp=pg_query($SQL);
   return($resp);
}

function FechaVencimiento($LoteOld,$FechaVencimiento){

   $SQL1="select concat_ws('-',year(curdate()),month(curdate()),'25') as X";
	$t=pg_fetch_array(pg_query($SQL1));
 	$FechaVencimientoActual=$t[0];
   
     // $SQL="select if(datediff('$FechaVencimiento','$FechaVencimientoActual') < 0, 'N' ,'S') as Ok";
      $SQL2="select if(datediff('$FechaVencimiento','$FechaVencimientoActual') < 0,'N','S') as X";
     // $resp=pg_fetch_array(pg_query($SQL));
      $resp2=pg_fetch_array(pg_query($SQL2));
	 //$tx[0]=$resp[0];
	 $tx=$resp2["X"];
      return($tx);
  
}



$Lote=$_GET["Lote"];
$PrecioLote=$_GET["PrecioLote"];
$FechaVencimiento=$_GET["FechaVencimiento"];
$LoteOld=$_GET["LoteOld"]; //IdLote 

$Existe=ExisteLote($Lote);
$FechaMenor=FechaVencimiento($LoteOld,$FechaVencimiento);
$ok=true; $ok2=true; $ok3=true;

if($row=pg_fetch_array($Existe)){
   $salida= "NO~El lote introducido ya existe! \n Verifique el codigo del Lote!";
	$ok=false;
}

if($FechaMenor == 'N' ){
   $salida="NO2~La Fecha de vencimiento no puede ser menor a la actual! \n Verifique la fecha de vencimiento!";
	$ok=false;
}


//echo $FechaMenor;

if($ok==true){

   ActualizaDatosLotes($Lote,$PrecioLote,$FechaVencimiento,$LoteOld);
   $salida= "SI~";
}

echo $salida;

conexion::desconectar();
?>