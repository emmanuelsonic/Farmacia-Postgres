<?php session_start();
$IdModalidad=$_SESSION["IdModalidad"];
$IdEstablecimiento=$_SESSION["IdEstablecimiento"];
if(isset($IdEstablecimiento)){

include('../Clases/class.php');
conexion::conectar();
switch($_GET["Bandera"]){
case 1:
   //Obtencion de usuarios introducidos en la DB
	$usuario=$_GET["usuario"];
	$SQL="select * from fos_user_user where nick ='".$usuario."'";
	$resp=pg_query($SQL);
	if($row=pg_fetch_row($resp)){
	   echo "SI";
	}else{
	   echo "NO";
	}
break;

case 2:
//Obtencion de areas de Farmacia
    
	if($_GET["IdFarmacia"]==3){$comp="limit 1";}else{$comp="";}
	if($_GET["Nivel"]==3){$supr=" and mnt_areafarmacia.Id <> 7";}else{$supr="";}
	$SQL=   "select mnt_areafarmacia.Id, mnt_areafarmacia.Area from mnt_areafarmacia 
                inner join mnt_areafarmaciaxestablecimiento on mnt_areafarmaciaxestablecimiento.IdArea=mnt_areafarmacia.Id
                where IdFarmacia=".$_GET["IdFarmacia"]." and mnt_areafarmaciaxestablecimiento.Habilitado='S' 
                and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad ".$comp." ".$supr ;
	$resp=pg_query($SQL);
	
	$out='<select id="area" name="area" tabindex="4">
	      <option value="0">Seleccione una Area</option>';
	while($row=pg_fetch_row($resp)){
	   $out.="<option value='".$row[0]."'>".$row[1]."</option>";
	}
	$out.="</select>";
	echo $out;

break;




case 3:

//datos
$usuario = $_GET["usuario"];
$pass = md5($_GET["pass"]);
$nombre = strtoupper($_GET["nombre"]);

$nivel = $_GET["nivel"];
$IdFarmacia = $_GET["IdFarmacia"];
$IdArea = $_GET["IdArea"];
$administracion = $_GET["administracion"];
$reportes = $_GET["reportes"];
$datos = $_GET["datos"];
//************
	$sec_id=pg_fetch_array(pg_query("select nextval('fos_user_user_id_seq')"));
	
	$queryInsert="insert into fos_user_user (id,username,password,firstname,IdFarmacia,nivel,Datos,Reportes,Administracion,primeraVez,IdArea,estadoCuenta,id_establecimiento,id_area_mod_estab) 
                                          values($sec_id[0],'$usuario','$pass','$nombre','$IdFarmacia','$nivel','$datos','$reportes','$administracion','2','$IdArea','H',".$IdEstablecimiento.",$IdModalidad)";
	
	pg_query($queryInsert);
break;





}//switch
conexion::desconectar();
}else{
echo "ERROR_SESSION";

}
// www.todo-pelicula.com#sthash.QuT4KXRl.dpuf
?>
