<?php session_start();
require('../Clases/class.php');
$IdReceta=$_GET["IdReceta"];
$IdMedicina=$_GET["IdMedicina"];
$Bandera=$_GET["Bandera"];
$IdArea=$_SESSION["IdArea"];

conexion::conectar();
if($Bandera=='SI'){$IdEstado='';}else{$IdEstado='I';}

if($IdEstado==''){
	$queryUpdate="update farm_medicinarecetada set IdEstado='' 
		where IdReceta='$IdReceta' and IdMedicina='$IdMedicina'";

}else{

	$queryUpdate="update farm_medicinarecetada set IdEstado='I' 
		where IdReceta='$IdReceta' and IdMedicina='$IdMedicina'";
}//ELSE

pg_query($queryUpdate); 


conexion::desconectar();
?> 
