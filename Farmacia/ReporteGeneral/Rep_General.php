<?php include('../Titulo/Titulo.php');
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

$IdEstablecimiento=$_SESSION["IdEstablecimiento"];
$IdModalidad=$_SESSION["IdModalidad"];

//******Generacion del combo principal

function generaSelect2($IdEstablecimiento,$IdModalidad){ //creacioon de combo para las Regiones
	conexion::conectar();
	$consulta=pg_query("select mssxe.IdSubServicioxEstablecimiento,NombreServicio,NombreSubServicio
			from mnt_subservicio mss
                        inner join mnt_subservicioxestablecimiento mssxe
                        on mssxe.IdSubServicio=mss.IdSubServicio
                        inner join mnt_servicioxestablecimiento msxe
                        on msxe.IdServicioxEstablecimiento = mssxe.IdServicioxEstablecimiento
			inner join mnt_servicio ms
			on ms.IdServicio = msxe.IdServicio
			
			where mssxe.IdEstablecimiento=$IdEstablecimiento
                        and mssxe.IdModalidad=$IdModalidad
                        and msxe.IdEstablecimiento=$IdEstablecimiento
                        and msxe.IdModalidad=$IdModalidad
			and CodigoFarmacia is not null
		");
	conexion::desconectar();
	// Voy imprimiendo el primer select compuesto por los paises
	echo "<select name='IdSubEspecialidad' id='IdSubEspecialidad'>";
	echo "<option value='0'>[General ...]</option>";
	while($registro=pg_fetch_row($consulta)){
		echo "<option value='".$registro[0]."'>[".$registro[1]."]  ".$registro[2]."</option>";
	}
	echo "</select>";
}
?>
<html>
<head>
<?php head();?>
<title>Reporte por Farmacias</title>
<script language="javascript"  src="../ReportesArchives/calendar.js"> </script>
<script language="javascript"  src="../ReportesArchives/validaFechas.js"> </script>
<script language="javascript" src="IncludeFiles/ReporteGeneral.js"></script>
<link rel="stylesheet" type="text/css" href="../default.css" media="screen" />

</head>

<body>
<?php Menu();?>
<br>

  <table width="816" border="0">
    <tr class="MYTABLE">
      <td colspan="5" align="center"><strong>COSTO POR SERVICIO</strong></td>
    </tr>
    <tr>
      <td colspan="5" class="FONDO"><br></td>
    </tr>
    <tr>
      <td width="280" class="FONDO"><strong>Especialidad/Servicio: </strong></td>
      <td width="673" colspan="4" class="FONDO"><?php generaSelect2($IdEstablecimiento,$IdModalidad); ?></td>
    </tr>
    <tr>
      
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
<br>
<div id="Reporte" align="center"></div>
</body>
</html>
<?php
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel
?>