<?php session_start();
$IdArea=$_SESSION["IdArea"];


require('../../Clases/class.php');
require('include/funciones.php');
require('include/pagination.class.php');
require('include/MedicosClass.php');
$Classquery=new Classquery;
conexion::conectar();
//****obtencion de fechas validas de recetas (3 dias habiles)
//***
$items = 15;
$page = 1;

if(isset($_GET['page']) and is_numeric($_GET['page']) and $page = $_GET['page'])
		$limit = " LIMIT ".(($page-1)*$items).",$items";
	else
		$limit = " LIMIT $items";

if(isset($_GET['q']) and !eregi('^ *$',$_GET['q'])){
		$q = $_GET['q']; //para ejecutar consulta
		$busqueda = htmlentities($q); //para mostrar en pantalla
		$Bandera=1;
			
//and month(farm_recetas.Fecha)=month(CURDATE())  Esta sentencia va si las recetas de un mes no pueden dar en otro mes
//a pesar que la vida de una receta sean 3 dias...Ej. 29/02/2008 --->  01/03/2008
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,$q,$_SESSION["IdEstablecimiento"]);
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,$q,$_SESSION["IdEstablecimiento"]);
}else{
$Bandera=0;
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,"",$_SESSION["IdEstablecimiento"]);
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,"",$_SESSION["IdEstablecimiento"]);
}
    //fecha de vida de una receta son 3 dias habiles
$query = pg_query($sqlStr.$limit);
$aux = Pg_Fetch_Assoc(pg_query($sqlStrAux));
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../default.css" media="screen" />
<title>...:::BUSQUEDA DE MEDICOS:::...</title>
<link rel="stylesheet" href="pagination.css" media="screen">
<link rel="stylesheet" href="style.css" media="screen">
<script src="include/buscador.js" type="text/javascript" language="javascript"></script>
<style type="text/css">
<!--
#Layer1 {
	position:absolute;
	left:75px;
	top:3px;
	width:826px;
	height:33px;
	z-index:1;
}

#resultados{
	position:absolute;
	left:57px;
	top:68px;
	width:854px;
	height:156px;
	z-index:2;
}
#Layer2 {
	position:absolute;
	left:400px;
	top:564px;
	width:58px;
	height:31px;
	z-index:3;
}
#Layer6 {	position:absolute;
	left:25px;
	top:105px;
	width:955px;
	height:30px;
	z-index:2;
}
.style1 {color:#0000CC; font-size:11px; font-family:Arial, Helvetica, sans-serif}
.style4 {font-size: 24px}
#Layer4 {	position:absolute;
	left:-199px;
	top:-39px;
	width:55px;
	height:31px;
	z-index:7;
}
#Layer7 {position:absolute;
	left:303px;
	top:39px;
	width:596px;
	height:23px;
	z-index:5;
}
#Layer3 {position:absolute;
	left:-1px;
	top:173px;
	width:836px;
	height:34px;
	z-index:6;
}
-->
</style>
<script language="javascript">
function inicio(){
document.form.q.focus();
}//inicio
function UbicarMedico(IdEspecialidad,NombreEspecialidad,IdMedico,NombreMedico){
window.opener.PegarMedico(IdEspecialidad,NombreEspecialidad,IdMedico,NombreMedico);

this.close();
}
</script>
</head>

<body onLoad="inicio()">
	
	<form name="form" action="../recetas/buscador_terapeutico.php" onSubmit="return buscar()">
      <div id="Layer1">
	 <table width="792">
	  <tr>
	   <td width="380" align="center">&nbsp;</td>
	   </tr>
	 <tr>
             <td><strong>Nombre del M&eacute;dico:</strong> 
	   <input type="text" id="q" name="q" value="<?php if(isset($q)) echo $busqueda;?>" onKeyUp="return buscar()" style="border-bottom-color:#000099; border-top-color:#000099; border-left-color:#000099; border-right-color:#000099" size="50">
		
      &nbsp;&nbsp;<input type="button" value="Buscar" id="boton" style="border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099">
      <span id="loading"></span></td>
	 </tr>
	</table>
	  </div>
    </form>
    
<div id="resultados" align="center">
	<?php
		if($aux['total'] and isset($busqueda)){
				//echo "{$aux['total']} Resultado".($aux['total']>1?'s':'')." que coinciden con tu b&uacute;squeda \"<strong>$busqueda</strong>\".";
echo "Resultados que coinciden con tu b&uacute;squeda \"<strong>$busqueda</strong>\".";
			}elseif($aux['total'] and !isset($q)){
				//echo "Total de registros: {$aux['total']}";
			}elseif(!$aux['total'] and isset($q)){
				echo"No hay registros que coincidan con tu b&uacute;squeda \"<strong>$busqueda</strong>\"";
			}
	?><br>
    <?php 
    if($aux['total']>0){
            $p = new pagination;
            $p->Items($aux['total']);
            $p->limit($items);
            if(isset($q))
                            $p->target("buscador_medico.php?q=".$q);
                    else
                            $p->target("buscador_medico.php");
            $p->currentPage($page);
            $p->show();
            echo "\t<table class=\"registros\">\n";
            echo "<tr class=\"titulos\"><td>CODIGO</td><td>NOMBRE DE MEDICO</td></tr>";
            $r=0;
            while($row = pg_fetch_assoc($query)){

            if(isset($page)){
            echo "\t\t<tr class=\"row$r\"><td align='center'>".$row["codigo_farmacia"]."</td>
            <td align=\"left\"><a href=\"#\" onclick=\"javascript:UbicarMedico(0,'0','".$row["id"]."','".htmlentities($row["nombreempleado"])."')\">".htmlentities($row["nombreempleado"])."</a></td>
            </tr>";
                    }//if

          if($r%2==0)++$r;else--$r;
        }
			echo "\t</table>\n";
			$p->show();
		}
	?>
</div>
</body>
</html>
