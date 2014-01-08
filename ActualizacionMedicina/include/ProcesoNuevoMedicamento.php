<?php
include('../../Clases/class.php');
include('ClaseNuevoMedicamento.php');
$Bandera=$_GET["Bandera"];
$new=new NuevoMedicamento;
/* Bandera que determina si es introduccion de nuevo medicamento o asignacion de especialidades*/

// conexion::conectar();
switch($Bandera){ 
case 1:
//SI YA ESTA EL MEDICMANETO DENTRO DE LA BASE DE DATOS
$IdMedicina=$_GET["id"];

//INTRODUCCION DE MEDICAMENTO
$codigo=strtoupper($_GET["codigo"]);
$nombre=strtoupper($_GET["nombre"]);

$concentracion=strtoupper($_GET["concentracion"]);
$FormaFarmaceutica=strtoupper($_GET["formafarmaceutica"]);
$presentacion=strtoupper($_GET["presentacion"]);

$new->ActualizarDatosGenerales($IdMedicina,$codigo,$nombre,$concentracion,$FormaFarmaceutica,$presentacion);


/*GENERALES DE CAMBIO POR SELECCION*/
$IdGrupo=$_GET["id"];
$IdUnidadMedida=$_GET["idunidadmedida"];
if($IdGrupo!=0){$new->ActualizarGrupo($IdGrupo,$IdMedicina);}
if($IdUnidadMedida!=0){$new->ActualizarUnidadMedida($IdUnidadMedida,$IdMedicina);}



break;

case 2:

//ASIGNACION DE ESPECIALIDADES
$IdMedicina=$_GET["id"];
$Especialidad=$_GET["especialidad"];
echo "<input type='hidden' id='IdMedicina2' name='IdMedicina2' value='".$IdMedicina."'>";
    $Nombre=$new->GetName($IdMedicina);
if($Especialidad==0){
echo $Nombre." - CON - TODAS LAS ESPECIALIDADES";
}else{
	$NombreEspecialidad=$new->GetEspecialidad($Especialidad);
echo $Nombre." - CON - ".$NombreEspecialidad;
}
break;

case 3:
$IdMedicina=$_GET["idmedicina"];
$IdArea=$_GET["idarea"];
$queryInsert="insert into mnt_areamedicina (idarea,idmedicina) values('$IdArea','$IdMedicina')";
pg_query($queryInsert);
$querySelect="select id 
			from mnt_areamedicina
			order by id desc
			limit 1";
$resp=pg_fetch_array(pg_query($querySelect));
echo 'Area Asignada<br><input type="hidden" id="IdAreaMedicina" name="IdAreaMedicina" value="'.$resp[0].'">';
break;

case 4:
$IdMedicina=$_GET["idmedicina"];
$IdArea=$_GET["idarea"];
$IdAreaMedicina=$_GET["id"];

$queryUpdate="update mnt_areamedicina set dispensada='$IdArea' where id='$IdAreaMedicina'";
pg_query($queryUpdate);
echo "OK";
break;
}//switch
conexion::desconectar();
?>