<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../../signIn.php';
</script>
<?php
}else{
if(isset($_SESSION["IdFarmacia2"])){
$IdFarmacia=$_SESSION["IdFarmacia2"];
}
$nivel=$_SESSION["nivel"];
if(($nivel!=1 and $nivel!=2 and $nivel!=4)){?>
<script language="javascript">
window.location='../Principal/index.php?Permiso=1';
</script>
<?php
}else{
$tipoUsuario=$_SESSION["tipo_usuario"];
$nombre=$_SESSION["nombre"];
$nivel=$_SESSION["nivel"];
$nick=$_SESSION["nick"];
require('../../Clases/class.php');
//******Generacion del combo principal

function generaSelect2(){ //creacioon de combo para las Regiones
	conexion::conectar();
	$consulta=mysql_query("select IdFarmacia,Farmacia
							from mnt_farmacia");
	conexion::desconectar();
	// Voy imprimiendo el primer select compuesto por los paises
	echo "<select name='IdFarmacia' id='IdFarmacia'>";
	echo "<option value='0'>[Consumo General ...]</option>";
	while($registro=mysql_fetch_row($consulta)){
		echo "<option value='".$registro[0]."'>".$registro[1]."</option>";
	}
	echo "</select>";
}
?>
<html>
<head>
<title>Reporte por Farmacias</title>
<script language="javascript"  src="../calendar.js"> </script>
<script language="javascript" src="IncludeFiles/ReporteFarmacias.js"></script>
<link rel="stylesheet" type="text/css" href="../../default.css" media="screen" />
<style type="text/css">
<!--
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
.style1 {color:#0000CC; font-size:11px; font-family:Arial, Helvetica, sans-serif}
#Layer6 {position:absolute;
	left:25px;
	top:105px;
	width:955px;
	height:30px
}
#Layer3 {position:absolute;
	left:2px;
	top:190px;
	width:1001px;
	height:30px;
	z-index:6;
}
#Layer1 {	position:absolute;
	left:115px;
	top:268px;
	width:826px;
	height:192px;
	z-index:1;
}
#Reporte {
	position:absolute;
	left:3px;
	top:504px;
	width:998px;
	height:184px;
	z-index:1;
}
-->
</style>
</head>

<body>
<div id="Layer71">
  <div id="Layer41"><img src="../../images/paisanito.jpg" alt="" width="195" height="94" /></div>
  <span class="style4">Ministerio de Salud P&uacute;blica y Asistencia Social </span></div>
<div class="style1" id="Layer6" align="center">
  <?php
encabezado::top($IdFarmacia,$tipoUsuario,$nick,$nombre);
?>
</div>
<div id="Layer3" align="center">
  <?php if($nivel==1){?>
  <script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menu_.js">'+'</scr'+'ipt>');/*img src="MenuImages/Menu.gif" moduleid="Default (Project)\Menu_off.xws"*/</script>
  <?php }elseif($nivel==4){?>
  <script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menudigitador.js">'+'</scr'+'ipt>');/*img src="MenuImages/MenuConsultaExterna.gif" moduleid="MenuConExt (Project)\MenuConsultaExterna_off.xws"*/</script>
  <?php }else{?>
  <script webstyle4>document.write('<scr'+'ipt src="../../xaramenu.js">'+'</scr'+'ipt>');document.write('<scr'+'ipt src="../../MenuImages/menucoadmin.js">'+'</scr'+'ipt>');/*img src="MenuImages/MenuCoAdmin.gif" moduleid="MenuCoAdmin (Project)\MenuCoAdmin_off.xws"*/</script>
  <?php }?>
</div>
<div id="Layer1">
  <table width="816" border="0">
    <tr class="MYTABLE">
      <td colspan="5" align="center"><strong>CONSUMO DE MEDICAMENTOS POR FARMACIA </strong></td>
    </tr>
    <tr>
      <td colspan="5" class="FONDO"><br></td>
    </tr>
    <tr>
      <td width="280" class="FONDO"><strong>Farmacia: </strong></td>
      <td width="673" colspan="4" class="FONDO"><?php generaSelect2(); ?></td>
    </tr>
    <tr>
      <td width="280" class="FONDO"><strong>Grupo Terapeutico: </strong></td>
      <td width="673" colspan="4" class="FONDO"><div id="ComboTerapeutico"><select name="IdTerapeutico" id="IdTerapeutico" onChange=" CargarCombo(this.id,this.value);">
        <option value="0">[Seleccione ...]</option>
		<?php conexion::conectar();
		$resp=queries::ComboGrupoTerapeutico();
		while($row=mysql_fetch_array($resp)){
		?>
		<option value="<?php echo $row[0];?>"><?php echo $row[1];?></option>
		<?php }?>
      </select>
	  </div>	  </td>
    </tr>
    <tr>
      <td class="FONDO"><strong>Medicina:</strong></td>
      <td colspan="4" class="FONDO"><div id="ComboMedicina"><select name="IdMedicina" id="IdMedicina" disabled="disabled">
        <option value="0">[Seleccione ...]</option>
      </select>
	  </div>	  </td>
    </tr>
    <tr>
      <td class="FONDO"><strong>Fecha de Inicio: </strong></td>
      <td colspan="4" class="FONDO"><input type="text" name="fechaInicio" id="fechaInicio" readonly="true" onClick="scwShow (this, event);" /></td>
    </tr>
    <tr>
      <td class="FONDO"><strong>Fecha de Finalizaci&oacute;n: </strong></td>
      <td colspan="4" class="FONDO"><input type="text" name="fechaFin" id="fechaFin" readonly="true" onClick="scwShow (this, event);"/></td>
    </tr>
    <tr>
      <td colspan="5" class="FONDO">&nbsp;</td>
    </tr>
    <tr class="MYTABLE">
      <td colspan="5" align="right"><input type="button" id="generar" name="generar" value="Generar Reporte" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099" onClick="javascript:Valida();"></td>
    </tr>
    <!-- <tr class="MYTABLE">
      <td colspan="5" align="right">	
<input type="button" id="Imprimir" name="imprimir" value="Imprimir" onClick="javascript:Imprimir();">
		</td>
    </tr> -->
  </table>
</div>
<div id="Reporte" align="center"></div>
</body>
</html>
<?php
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel
?>