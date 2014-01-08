<?php session_start();
require('IncludeFiles/ClaseAvisoVencimiento.php');
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../default.css" media="screen" />
<title>...Medicamento proximo a vencer</title>
<script language="javascript">
function popUp(URL) {
day = new Date();
id = day.getTime();
//id=document.formulario.fecha.value;
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=620,height=500,left = 450,top = 450');");
}//popUp
</script>
</head>
<body>

<?php
conexion::conectar();

$puntero=new Aviso;

$reporte='<table width="100%" border="1">';
$reporte.='<tr>
  <td align="center" colspan=11><input type="button" id="cerrar" name="cerrar" value="Cerrar Aviso" onClick="self.close();"><br>
  <input type="button" id="imprimir" name="imprimir" value="Imprimir Lista" onClick="javascrip:popUp(\'imprimir.php\');">
  </td></tr>';
$reporte.='
			<tr class="MYTABLE">
				<td colspan="11" align="center">'.$_SESSION["NombreEstablecimiento"].'<br>
				<strong>REPORTE DE MEDICAMENTOS PROXIMOS A VENCER</strong> <br>
				</td></tr>
	
				<tr class="MYTABLE"><td align="right" colspan="11">Fecha de Emisi&oacute;n: '.$DateNow=date("d-m-Y").'
				</td>
		    </tr>

			</tr>';
$Total=0;
$respGrupos=$puntero->GrupoTerapeutico(0);
if($rowGrupo=pg_fetch_array($respGrupos)){

do{

//
$respMedicina=$puntero->MedicinasGrupo($rowGrupo["idterapeutico"],$_SESSION["IdEstablecimiento"]);
//*******************
$contador=0;
$SubTotal=0;
$SubTotalB=0;
if($rowMedicina=pg_fetch_array($respMedicina)){



do{


$resp=$puntero->ObtenerInformacionVencimientoProximo(0,$rowMedicina["IdMedicina"]);

while($row=pg_fetch_array($resp)){
$SubTotalB=1;
if($contador==0){
$reporte.='<tr class="MYTABLE">
<th align="center" style="vertical-align:middle;" colspan=7>'.$rowGrupo["grupoterapeutico"].'</th>';
$reporte.='<tr class="MYTABLE">
<th align="center" style="vertical-align:middle;">Codigo</th>
<th width="30%" align="center" style="vertical-align:middle;">Medicamento</th>
<th align="center" style="vertical-align:middle;">Existencias</th>
<th align="center" style="vertical-align:middle;">Unidad de Medida</th>
<th align="center" style="vertical-align:middle;">Lote</th>
<th align="center" style="vertical-align:middle;">Fecha de Vencimiento</th>
<th width="10%" align="center" style="vertical-align:middle;">Costo ($)</th>
</tr>';
}
$contador++;
	$Codigo=$row["codigo"];
	$NombreMedicina=htmlentities($row["nombre"]);
	    $Concentracion=$row["concentracion"];
	    $FormaFarmaceutica=htmlentities($row["formafarmaceutica"].' - '.$row["presentacion"]);
	
	$Divisor=$row["divisor"];
	$Descripcion=$row["descripcion"];
	
	$respDetalleMedicina=$puntero->ObtenerVencimientoProximo(0,$rowMedicina["idmedicina"]);
		$Existencias=0;$CodigoLote=''; $Vencimiento='';$Costo=0;
	while($rowDetalle=pg_fetch_array($respDetalleMedicina)){
	$Existencias+=$rowDetalle["existencia"];
		
	$CodigoLote.=strtoupper($rowDetalle["lote"])."<br>";
		
	$VencimientoT=$rowDetalle["fechavencimiento"];
	$tmp=explode('-',$VencimientoT);
	$Vencimiento.=$tmp[1]."/".$tmp[0]."<br>";
		$Costo+=($Existencias/$Divisor)*$rowDetalle["preciolote"];
	}
	
$TotalExistencia=$Existencias;
	if($respDivisor=pg_fetch_array($puntero->ValorDivisor($rowMedicina["idmedicina"]))){
		$Divisor=$respDivisor[0];

		if($TotalExistencia < 1){
			//Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
		   $TransformaEntero=number_format($TotalExistencia*$Divisor,0,'.',',');
			$CantidadTransformada=$TransformaEntero.'/'.$Divisor;
		}else{
			//Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
				
		$CantidadBase=explode('.',$TotalExistencia);
		
		    $Entero=$CantidadBase[0];//Faccion ENTERA
			if(!isset($CantidadBase[1])){
			   $Decimal=0;
			}else{
			   $Decimal=$CantidadBase[1];
			}
			
		    if($Decimal==0){$Decimal="";$Quebrado="";}else{
			
			$Quebrado=number_format(($Decimal/100)*$Divisor,0,'.',',');
			$Quebrado='['.$Quebrado.'/'.$Divisor.']';
		    }

			
		$CantidadTransformada=$Entero.' '.$Quebrado;
		}
	   $CantidadIntro=$CantidadTransformada;
		
	}else{
	   $CantidadIntro=$TotalExistencia;
		$CantidadIntro=$TotalExistencia/$Divisor;
	}


	$respLotes=$puntero->ObtenerLotes($rowMedicina["idmedicina"]);
            $respLotes2=$puntero->ObtenerLotes($rowMedicina["idmedicina"]);
	$CodigoLote="";
            $lote_temporal=pg_fetch_array($respLotes2);
	while($rowLotes=pg_fetch_array($respLotes)){
            $lote_temporal=pg_fetch_array($respLotes2);
            
            if($rowLotes[0]!=$lote_temporal[0]){
               // echo $rowLotes[0]."!=".$lote_temporal[0]."<br>";
                $CodigoLote.=$rowLotes[0]."<br>";
            }
           
	}

$reporte.='<tr class="FONDO">
<td align="center" style="vertical-align:middle;">'.$Codigo.'</td>
<td align="center" style="vertical-align:middle;">'.$NombreMedicina."-".$Concentracion." <br> ".$FormaFarmaceutica.'</td>
<td align="center" style="vertical-align:middle;">'.$CantidadIntro.'</td>
<td align="center" style="vertical-align:middle;">'.$Descripcion.'</td>
<td align="center" style="vertical-align:middle;">'.$CodigoLote.'</td>
<td align="center" style="vertical-align:middle;">'.$Vencimiento.'</td>
<td align="center" style="vertical-align:middle;">$ '.number_format($Costo,3,'.',',').'</td>
</tr>';
	$SubTotal+=$Costo;
 }//fin de while

}while($rowMedicina=pg_fetch_array($respMedicina));
}

if($SubTotalB!=0){
$reporte.='<tr class="FONDO"><td align="right" style="vertical-align:middle;" colspan=6>SubTotal: </td><td><strong>$ '.number_format($SubTotal,3,'.',',').'</strong></td></tr>';
}

$Total+=$SubTotal;
}while($rowGrupo=pg_fetch_array($respGrupos));//Grupo terapeutico

}else{

$reporte.="NO EXISTEN DATOS!";

}
$reporte.='<tr class="FONDO"><td align="right" style="vertical-align:middle;" colspan=6>Total: </td><td><strong>$ '.number_format($Total,3,'.',',').'</strong></td></tr>';
$reporte.="</table>";

echo $reporte;


conexion::desconectar();
?>

</body>
</html>
