<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../signIn.php';
</script>
<?php
}else{
if(isset($_SESSION["IdFarmacia2"])){
$IdFarmacia=$_SESSION["IdFarmacia2"];
}
$nivel=$_SESSION["nivel"];
if(($_SESSION["Reportes"]!=1)){?>
<script language="javascript">
window.location='../Principal/index.php?Permiso=1';
</script>
<?php
}else{

$tipoUsuario=$_SESSION["tipo_usuario"];
$nombre=$_SESSION["nombre"];
$nivel=$_SESSION["nivel"];
$nick=$_SESSION["nick"];
require('../Clases/class.php');
$conexion=new conexion;
//******Generacion del combo principal
function generaSelect(){ //creacioon de combo para las Regiones
	$conexion=new conexion;
	$conexion->conectar();
	$consulta=mysql_query("select * from mnt_farmacia");
	$conexion->desconectar();
	// Voy imprimiendo el primer select compuesto por los paises
	echo "<select name='select1' id='select1' onChange='cargaContenido8(this.id)' onmouseover=\"Tip('Selecci&oacute;n de Farmacia')\" onmouseout=\"UnTip()\">";
	echo "<option value='0'>SELECCIONE UNA FARMACIA</option>";
	while($registro=mysql_fetch_row($consulta)){
		if($registro[1]!="--"){
		echo "<option value='".$registro[0]."'>".$registro[1]."</option>";
		}
	}
	echo "</select>";
}
//**********
//********** VALIDACION DE FECHAS*********
  /* $fechas = array();
   $fechas = explode("-",$fecha0);
   $ano = intval($fechas[0]);
   $mes = intval($fechas[1]);
   $dia = intval($fechas[2]);*/
//*****************


?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../default.css" media="screen" />
<title>...:::Reporte Existencias:::...</title>
<script language="javascript"  src="../calendar.js"> </script>
<script type="text/javascript" src="FiltroExistencias.js"></script>
<style type="text/css">
<!--
#Layer6 {position:absolute;
	left:25px;
	top:105px;
	width:955px;
	height:30px;
	z-index:2;
}
#Layer1 {
	position:absolute;
	left:113px;
	top:290px;
	width:826px;
	height:192px;
	z-index:1;
}
.style1 {color:#0000CC; font-size:11px; font-family:Arial, Helvetica, sans-serif}
#Layer3 {	position:absolute;
	left:2px;
	top:190px;
	width:1001px;
	height:30px;
	z-index:6;
}
.style4 {font-size: 24px}
#Layer41 {position:absolute;
	left:-199px;
	top:-39px;
	width:55px;
	height:31px;
	z-index:7;
}
#Layer71 {position:absolute;
	left:303px;
	top:39px;
	width:596px;
	height:23px;
	z-index:5;
}
-->
</style>
<script language="javascript" src="reporte.js"></script>
<script language="javascript">
function confirmacion(){
var resp=confirm('Desea Cancelar esta Acci�n?');
if(resp==1){
window.location='../IndexReportes.php';
}
}//confirmacion
function valida(form){

if(form.select1.value==0){
	var Resp=confirm('Desea realizar un reporte General de Existencias?');
	if(Resp==1){
		var FechaInicio=document.getElementById('fechaInicio').value;
		var FechaFin=document.getElementById('fechaFin').value;
		if(FechaInicio=='' || FechaFin==''){
			alert('Debe seleccionar un periodo');
			return(false);
		}else{
			window.location="reporteTotal.php?FechaInicio="+FechaInicio+"&FechaFin="+FechaFin;
			return(false);
		}
	}else{
	//form.select1.focus();
		return(false);
	}
}

if(form.select2.value==0){
alert('Seleccione una Area');
return(false);
}

if(form.fechaInicio.value==''){
alert('Debe seleccionar una fecha de inicio');
return(false);
}

if(form.fechaFin.value==''){
alert('Debe seleccionar una fecha de finalizaci�n');
return(false);
}

//****OBTENER FECHA ACTUAL
	var mydate=new Date();
	var year=mydate.getFullYear();//a�o actual
		var yearA=mydate.getFullYear()-1;
	var month=mydate.getMonth()+1;//mes actual 
	if(month < 10){
	month='0' + month;
	}

//******
var aux1;
var aux2;
var fechaFin;
var fechaInicio;
fechaFin=form.fechaFin.value;
fechaInicio=form.fechaInicio.value;

aux1 = fechaFin.split("-");
aux2 = fechaInicio.split("-");

//FECHA DE DB=2008-02-19
var DiaInicio=aux2[2];//OBTENCION DE 
var DiaFin=aux1[2];   //DIAS FORMATO ##
var anoInicio=aux2[0];//OBTENCION DE 
var anoFin=aux1[0];   //A�OS
//los meses menores a 10 van con 0x
var mesInicio=aux2[1];//OBTENCION DE
var mesFin=aux1[1];	  //MESES EN FORMATO ##

anoFin=parseFloat(anoFin);
anoInicio=parseFloat(anoInicio);
mesFin=parseFloat(mesFin);
mesInicio=parseFloat(mesInicio);
DiaFin=parseFloat(DiaFin);
DiaInicio=parseFloat(DiaInicio);
month=parseFloat(month);

if(year<anoInicio || year<anoFin){
alert('Los a�os no pueden ser mayor al a�o actual');
return(false);
}

if((month<mesInicio || month<mesFin)&&(yearA<anoInicio || yearA<anoFin)){
alert('La fecha no puede ser mayor al mes actual');
return(false);
}//meses

if((anoFin<anoInicio || mesFin<mesInicio) && (anoFin < anoInicio) ){
alert('La fecha de Finalizacion no puede ser menor a la de inicio');
return(false);
}//validacion de MESES y A�OS para evitar que la fecha de inicio sea mayor a la de finalizacion

if(DiaFin<DiaInicio){
	if((mesFin==mesInicio) && (anoInicio == anoFin)){
	alert('El dia de Finalizacion no puede ser menor al de inicio');
	return(false);	
	}
}//DIAFIN

}//valida
</script>
</head>
<body>
<script language="javascript" src="../../tooltip/wz_tooltip.js"></script>
<form action="Reporte_Existencias.php" method="post" name="formulario" onSubmit="return valida(this)">
<div class="style1" id="Layer6" align="center">
  <?php
encabezado::top($IdFarmacia,$tipoUsuario,$nick,$nombre);

?>
  </div>
<div id="Layer1">
  <table width="816" border="0">
    <tr class="MYTABLE">
      <td colspan="5" align="center">&nbsp;<strong>EXISTENCIAS DE MEDICAMENTOS </strong></td>
      </tr>
			<tr><td colspan="5" class="FONDO"><br></td></tr>
    <tr>
      <td width="280" class="FONDO"><strong>Farmacia: </strong></td>
      <td width="673" colspan="4" class="FONDO"><?php generaSelect(); ?></td>
      </tr>
    <tr>
      <td class="FONDO"><strong>Area:</strong></td>
      <td colspan="4" class="FONDO"><select name="select2" id="select2" disabled="disabled">
        <option value="0">SELECCIONE UNA AREA</option>
      </select></td>
      </tr>
    <tr>
      <td class="FONDO"><strong><strong>Grupo Terapeutico:</strong></strong></td>
      <td colspan="4" class="FONDO"><select name="select3" id="select3" disabled="disabled">
        <option value="0">TODOS LOS GRUPOS TERAPEUTICOS</option>
      </select></td>
      </tr>
	   <tr>
      <td class="FONDO"><strong><strong>Medicina:</strong></strong></td>
      <td colspan="4" class="FONDO"><select name="select4" id="select4" disabled="disabled">
        <option value="0">TODAS LAS MEDICINAS</option>
      </select></td>
      </tr>
	  <tr>
      <td class="FONDO"><strong>Fecha de Inicio:</strong></td>
      <td colspan="4" class="FONDO"><input type="text" name="fechaInicio" id="fechaInicio" readonly="true" onClick="Tip('Fecha inicial para<br>la generaci&oacute;n del Reporte');scwShow (this, event);"  onBlur="UnTip()"/></td>
	  </tr>
     <td class="FONDO"><strong><strong>Fecha de Finalizaci&oacute;n: </strong></strong></td>
      <td colspan="4" class="FONDO"><input type="text" name="fechaFin" id="fechaFin" readonly="true" onClick="Tip('Fecha final para<br>la generaci&oacute;n del Reporte');scwShow (this, event);" onBlur="UnTip()"/></td>
      </tr>
	  	  <tr>
      <td colspan="5" class="FONDO">&nbsp;</td>
      </tr>
    <tr class="MYTABLE">
      <td colspan="5" align="right"><input type="submit" name="generar" value="Generar Reporte" onMouseOver="this.style.color='#009900';Tip('Generar Reportes<br><img src=\'../../images/cerrando.gif\'>',TEXTALIGN,'center')" onMouseOut="this.style.color='#000000';UnTip()" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099"></td>
    </tr>
  </table>
</div>
</form>
<div id="Layer3" align="center">
  <?php if($nivel==1){?>
<script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menu_.js">'+'</scr'+'ipt>');/*img src="MenuImages/Menu.gif" moduleid="Default (Project)\Menu_off.xws"*/</script>
  <?php }else{?>
  <script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menucoadmin.js">'+'</scr'+'ipt>');/*img src="MenuImages/MenuCoAdmin.gif" moduleid="MenuCoAdmin (Project)\MenuCoAdmin_off.xws"*/</script>
  <?php }?>
</div>
<div id="Layer71">
  <div id="Layer41"><img src="../../images/paisanito.jpg" alt="" width="195" height="94" /></div>
  <span class="style4">Ministerio de Salud P&uacute;blica y Asistencia Social </span></div>
</body>
</html>
<?php
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel
?>