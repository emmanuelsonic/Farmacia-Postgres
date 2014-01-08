<?php session_start();
if(!isset($_SESSION["nivel"])){?>
<script language="javascript">
window.location='../signIn.php';
</script>
<?php
}else{
$IdArea=$_SESSION["IdArea"];
require('include/conexion.php');
require('include/funciones.php');
require('include/pagination.class.php');
require('include/RecetasClass.php');
$FechaD=new ClassFechaAtras;
$Classquery=new Classquery;

//****obtencion de fechas validas de recetas (3 dias habiles)

$selectNombreFecha="select dayname(curdate()) as NombreFechaActual";
$NombreDiaActual = pg_query($selectNombreFecha, $link);
$rowNombre=pg_fetch_array($NombreDiaActual);
$NombreFecha=$rowNombre["NombreFechaActual"];
$FechaAtras=$FechaD->ObtenerFechaAtras($NombreFecha,$link);
$FechaAdelante=$FechaD->Adelante($NombreFecha,$link);
//***
$items = 10;
$page = 1;

if(isset($_GET['page']) and is_numeric($_GET['page']) and $page = $_GET['page'])
		$limit = " LIMIT ".(($page-1)*$items).",$items";
	else
		$limit = " LIMIT $items";

if(isset($_GET['q']) and !eregi('^ *$',$_GET['q'])){
		$q = sql_quote($_GET['q']); //para ejecutar consulta
		$busqueda = htmlentities($q); //para mostrar en pantalla
		$Bandera=1;
			
//and month(farm_recetas.Fecha)=month(CURDATE())  Esta sentencia va si las recetas de un mes no pueden dar en otro mes
//a pesar que la vida de una receta sean 3 dias...Ej. 29/02/2008 --->  01/03/2008
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,$FechaAtras,$FechaAdelante,$q);
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,$FechaAtras,$FechaAdelante,$q);
}else{
$Bandera=0;
$sqlStr=$Classquery->ObtenerQuery($Bandera,$IdArea,$FechaAtras,$FechaAdelante,"");
$sqlStrAux=$Classquery->ObtenerQueryTotal($Bandera,$IdArea,$FechaAtras,$FechaAdelante,"");
}
$aux = pg_Fetch_Assoc(pg_query($sqlStrAux,$link));
$query = pg_query($sqlStr.$limit, $link);
?>	<p><?php
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
					$p->target("buscador_recetas.php?q=".urlencode($q));
				else
					$p->target("buscador_recetas.php");
			$p->currentPage($page);
			$p->show();
			echo "\t<table class=\"registros\">\n";
while($row = pg_fetch_assoc($query)){
	if(isset($page)){
	$Id=$row["IdReceta"]; 	
	
echo "<tr class=\"titulos\"><td>TIPO RECETA</td><td>No. EXPEDIENTE</td><td>NOMBRE PACIENTE</td><td>FECHA DE PREPARACION</td></tr>\n";			$r=0;
	
	if($row["IdEstado"]=='RL' || $row["IdEstado"]=='RN'){$Rep=" REPETITIVA";}else{$Rep="DEL DIA";}
echo "\t\t<tr class=\"row$r\"><td align=\"center\"><a href=\"#\">".$Rep."</a></td><td align=\"center\"><a href=\"#\">".htmlentities($row['IdNumeroExp'])."</a></td><td align=\"center\"><a href=\"#\">".strtoupper (htmlentities($row['NOMBRE']))."</a></td><td align=\"center\"><a href=\"#\">".$row["FechaPreparada"]."</a></td><td align=\"center\"></tr>";

echo '<tr><td align="center" colspan="4">
<table width="827" boder="1">
<tr><td align="center" colspan="6"><strong>RECETA PREPARADA Y NO ENTREGADA</</td></tr>
<tr class="MYTABLE"><td align="center">CANTIDAD</td><td align="center">MEDICAMENTO</td><td align="center">CONCENTRACION</td><td align="center">PRESENTACION</td><td align="center">DOSIS</td><td align="center">SATISFECHO</td></tr>';
$DetalleReceta=ClassQuery::MedicinaReceta($Id,$IdArea);
$DetalleReceta=pg_query($DetalleReceta,$link);
while($rowDetalle=pg_fetch_array($DetalleReceta)){
$Cantidad=$rowDetalle["Cantidad"];$Nombre=$rowDetalle["Nombre"];$Concentracion=$rowDetalle["Concentracion"];$Presentacion=$rowDetalle["FormaFarmaceutica"];
$Dosis=$rowDetalle["Dosis"];$IdEstado=$rowDetalle["IdEstado"];
if($IdEstado=='' || $IdEstado=='S'){$Satisfecho="SI";$Colore="";}else{$Satisfecho="NO";$Colore='style="background-color:#FF6633"';}

echo '<tr '.$Colore.' class="FONDO2"><td align="center">'.$Cantidad.'</td><td align="center">'.$Nombre.'</td><td align="center">'.$Concentracion.'</td><td align="center">'.$Presentacion.'</td><td>'.$Dosis.'</td><td align="center">'.$Satisfecho.'</td></tr>';


}//while
echo '</table>';
echo '</td></tr>';

	}//IF
		 
          if($r%2==0)++$r;else--$r;
        }
			echo "\t</table>\n";
			$p->show();
}
 
}//Fin de IF isset de Nivel ?>