<?php session_start();
$IdReceta=$_GET['IdReceta'];
$IdMedicinaOrigen=$_GET["IdMedicina"];
$Fecha=$_GET["Fecha"];
echo $Fecha;
?>
<html>
<head>
<title>Emergente...</title>
<link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
<title>...:::Introduccion de Recetas:::...</title><script language="javascript" src="IncludeFiles/IntroRecetas.js"></script><script language="javascript"  src="IncludeFiles/calendar.js"> </script><script type="text/javascript" src="IncludeFiles/FiltroEspecialidad.js"></script>

<!-- AUTOCOMPLETAR --><script type="text/javascript" src="scripts/prototype.js"></script><script type="text/javascript" src="scripts/autocomplete.js"></script>
	<link rel="stylesheet" type="text/css" href="styles/autocomplete.css" />
<!-- -->

<script language="JavaScript" src="../noCeros.js"></script>

<script language="javascript">
    <!--
    var nav4 = window.Event ? true : false;
    function acceptNum(evt,Objeto){	
      // NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57, 46 = '.'
        var key = nav4 ? evt.which : evt.keyCode;	
        if( !( (key >= 48 && key <= 57) || key < 13 ) )
        {
            if (!(key == 13 ))
            {
                return Saltos(key,Objeto);
            }
            return Saltos(evt,Objeto);
        }
      // NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57, 46 = '.'
      // var key = nav4 ? evt.which : evt.keyCode;	
      // return ((key == 13) || (key >= 48 && key <= 57) || key == 45);
      // return ((key < 13) || (key >= 48 && key <= 57));
    }
</script> 

</head>

<body onLoad="CargarDetalle('<?php echo $IdReceta;?>',<?php echo $IdMedicinaOrigen;?>);document.getElementById('Cantidad').focus();">
<table width="643" border="1">
	<tr class="MYTABLE"><td align="center" colspan="4"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalle de Receta No. <em><?php echo $IdReceta;?></em></strong></td><td align="right"><a style="color:#CCFF00; cursor:move;" onClick="window.close();">Cerrar &nbsp;<img src="../images/cerrar.jpg"></a></td></tr>
	
	<tr class="FONDO2">
	<td width="241" align="center" colspan="5">
		<table width="100%">
		<tr class="FONDO2">
		<td><strong>Cantidad:</strong></td>
		<td><input type="text" id="Cantidad" name="Cantidad" size="6" onKeyPress="return acceptNum(event,this.id);" onblur="NoCero(this.id);"></td>
		</tr>
		<tr>
		<td><strong>Medicamento</strong>
		<input type="hidden" id="IdMedicina" name="IdMedicina">
		<input type="hidden" id="IdMedicinaOrigen" name="IdMedicinaOrigen" value="<?php echo $IdMedicinaOrigen;?>">
		<input type="hidden" id="IdReceta" name="IdReceta" value="<?php echo $IdReceta;?>">
                <input type="hidden" id="Fecha" name="Fecha" value="<?php echo $Fecha;?>">
		</td>
		<td>
		<input type="text" id="NombreMedicina" name="NombreMedicina" onKeyPress="return Saltos(event,this.id); Limpieza(event,this.value);" size="80">
		<input type="hidden" id="ExistenciaTotal" name="ExistenciaTotal">
		</td></tr>
		<tr><td colspan="2" align="right"><input type="button" id="Agregar" name="Agregar" value="Agregar Medicamento" onClick="valida2();"></td></tr>
		</table>
	</td></tr>
	<tr><td colspan="5">

<div id="<?php echo $IdReceta;?>" style='border:solid;  overflow:scroll;  height:315; width:850;'></div>

</td></tr>
	<tr class="MYTABLE"><td colspan="5"><div id="Respuesta">&nbsp;</div></td></tr>
</table><script>
			new Autocomplete('NombreMedicina', function() { 
				return 'respuesta2.php?q=' + this.value; 
			});

	</script>
</body>
</html>
