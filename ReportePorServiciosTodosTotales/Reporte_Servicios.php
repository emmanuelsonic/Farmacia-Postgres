<?php session_start();
if(!isset($_SESSION["nivel"])){ 
echo "ERROR_SESSION";
}else{
if($_SESSION["Reportes"]!=1){?>
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
include('Funciones.php');
$query=new queries;
conexion::conectar();

$IdEstablecimiento=$_SESSION["IdEstablecimiento"];
$IdModalidad=$_SESSION["IdModalidad"];

//html { font: 100%/2.5 Arial, Helvetica, sans-serif; }
$FechaInicio=explode('-',$_REQUEST["fechaInicio"]);
$FechaFin=explode('-',$_REQUEST["fechaFin"]);
$FechaInicio2=$FechaInicio[2].'-'.$FechaInicio[1].'-'.$FechaInicio[0];
$FechaFin2=$FechaFin[2].'-'.$FechaFin[1].'-'.$FechaFin[0];
$FechaInicio=$_REQUEST["fechaInicio"];
$FechaFin=$_REQUEST["fechaFin"];
/**********************INFORMACION DE REPORTE**********************************/
if(isset($_GET["IdSubServicio"])){$IdSubEspecialidad=$_GET["IdSubServicio"];}else{$IdSubEspecialidad=0;} //IdSubServicioxEstablecimiento
if(isset($_GET["IdTerapeutico"])){$IdGrupoTerapeutico=$_GET["IdTerapeutico"];}else{$IdGrupoTerapeutico=0;}
if(isset($_GET["IdMedicina"])){$IdMedicina=$_GET["IdMedicina"];}else{$IdMedicina=0;}
/******************************************************************************/

//     GENERACION DE EXCEL
	$NombreExcel='Servicios_Todos_Totales_'.$nick.'_'.date('d_m_Y__h_i_s A');
	$nombrearchivo = "../ReportesExcel/".$NombreExcel.".xls";
	$punteroarchivo = fopen($nombrearchivo, "wb") or die("El archivo de reporte no pudo crearse");
//***********************
//LIBREOFFICE
	$nombrearchivo2 = "../ReportesExcel/".$NombreExcel.".ods";
	$punteroarchivo2 = fopen($nombrearchivo2, "w+") or die("El archivo de reporte no pudo crearse");

//***********************

    $reporte2='<table width=100%>
<th>SERVICIO</th><th scope="col">Total</th><th scope="col">Satis.</th><th scope="col">Insat.</th> <th  scope="col">Costo($)</th>';


	$Total=0;
	$TotalRecetasGlobal=0;
	$TotalSatisGlobal=0;
	$TotalInsatGlobal=0;
	
//*************************************
//******************************* QUERIES Y RECORRIDOS
$respServicios=Servicios($IdSubEspecialidad,$IdGrupoTerapeutico,$IdMedicina,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad);
if($rowServicios=pg_fetch_array($respServicios)){
do{
	$IdSubEspecialidad=$rowServicios["IdSubServicioxEstablecimiento"];
	$NombreSubEspecialidad=$rowServicios["NombreSubServicio"];
	$Ubicacion=$rowServicios["Ubicacion"];


		$SubTotalServicio=0;
			        $TotalRecetas=0;
					$TotalSatis=0;
					$TotalInsat=0;
					$TotalConsumo=0;
			
			

	 
	$nombreTera=NombreTera($IdGrupoTerapeutico,$IdSubEspecialidad,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad);
	if($grupos=pg_fetch_array($nombreTera)){
	do{
		$NombreTerapeutico=$grupos["GrupoTerapeutico"];
		$IdTerapeutico=$grupos["IdTerapeutico"];
			$SubTotal=0;
			        $SubTotalRecetas=0;
					$SubTotalSatis=0;
					$SubTotalInsat=0;
					$SubTotalConsu=0;

			
			
		$resp1=QueryExterna($IdTerapeutico,$IdMedicina,$IdSubEspecialidad,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad);
			while($row=pg_fetch_array($resp1)){
		$GrupoTerapeutico=$IdTerapeutico;
		$Medicina=$row["IdMedicina"];
		$codigoMedicina=$row["Codigo"];
		$NombreMedicina=$row["Nombre"];
		$concentracion=$row["Concentracion"];
		$presentacion=$row["FormaFarmaceutica"].' - '.$row["Presentacion"];
		
		$Nrecetas=0;//conteo de recetas
		$consumo=0;
		
		
		$respuesta=ObtenerReporteGrupoTerapeutico($IdTerapeutico,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad,$IdEstablecimiento,$IdModalidad);
			
				if($row2=pg_fetch_array($respuesta)){ /* verificacion de datos */
		$precioActual=0;
		
		//$IdReceta=$row2["IdReceta"];
		$IdReceta=0;
		$Divisor=$row2["Divisor"];//Divisor de conversionCosto
		$UnidadMedida=$row2["Descripcion"];//Tipo de unidad de Medida
		$satisfechas=0;
		$insatisfechas=0;
		
	
		
		/*Obtencion de recetas satifechas e insatisfechas globales parametros ...,0,0)*/
		$sat=ObtenerRecetasSatisfechas($IdReceta,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad,0,0,$IdEstablecimiento,$IdModalidad);
		$insat=ObtenerRecetasInsatisfechas($IdReceta,$Medicina,$FechaInicio,$FechaFin,$IdSubEspecialidad,0,0,$IdEstablecimiento,$IdModalidad);
			$Nrecetas=$sat+$insat;
		//***********
		
		//***********
	/*
	$Cantidad_Total=SumatoriaMedicamento($Medicina,$IdSubEspecialidad,$FechaInicio,$FechaFin);
		$CantidadReal=$Cantidad_Total/$Divisor;
	$Ano=date('Y');
	$Precio=ObtenerPrecioMedicina($Medicina,$Ano);
	$Monto=$CantidadReal*$Precio;
	*/

		$respSum=SumatoriaMedicamento($Medicina,$IdSubEspecialidad,$FechaInicio,$FechaFin,$IdEstablecimiento,$IdModalidad);
		
		if($rowSum=pg_fetch_array($respSum)){
		    $CantidadReal=0;
		    $Monto=0;
		    $Lotes="";
		    do{
			$CantidadReal+=$rowSum["TotalMedicamento"];

	if($respDivisor=pg_fetch_array(ValorDivisor($Medicina,$IdEstablecimiento,$IdModalidad))){
		$Divisor=$respDivisor[0];

		if($CantidadReal < 1){
			//Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
		   $TransformaEntero=number_format($CantidadReal*$Divisor,0,'.',',');
			$CantidadTransformada=$TransformaEntero.'/'.$Divisor;
		}else{
			//Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
			$CantidadReal=number_format($CantidadReal,2,'.',',');	
		$CantidadBase=explode('.',$CantidadReal);
		
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
	   $CantidadIntro=$CantidadReal;
		$CantidadIntro=number_format($CantidadIntro,2,'.',',');
	}

			$Monto+=$rowSum["Costo"];
			$Lotes.=$rowSum["Lote"]."<br> $".$rowSum["PrecioLote"]."<br><br>";
			
		    }while($rowSum=pg_fetch_array($respSum));
		
		}
		

		$PrecioNuevo=$Lotes;
		$MontoNuevo=number_format($Monto,3,'.',',');
			
			$SubTotal+=$Monto;
			
			        $SubTotalRecetas+=$Nrecetas;
					$SubTotalSatis+=$sat;
					$SubTotalInsat+=$insat;
					$SubTotalConsu+=$CantidadReal;

	if($respDivisor=pg_fetch_array(ValorDivisor($Medicina,$IdEstablecimiento,$IdModalidad))){
		$Divisor=$respDivisor[0];

		if($CantidadReal < 1){
			//Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
		   $TransformaEntero=number_format($CantidadReal*$Divisor,0,'.',',');
			$CantidadTransformada=$TransformaEntero.'/'.$Divisor;
		}else{
			//Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
			$CantidadReal=number_format($CantidadReal,2,'.',',');	
		$CantidadBase=explode('.',$CantidadReal);
		
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
	   $CantidadIntro=$CantidadReal;
		$CantidadIntro=number_format($CantidadIntro,2,'.',',');
	}
							
			
			

				}//if row2
			}//while externo

    
		  
			        $TotalRecetas+=$SubTotalRecetas;
					$TotalSatis+=$SubTotalSatis;
					$TotalInsat+=$SubTotalInsat;
					$TotalConsumo+=$SubTotalConsu;
		$SubTotalServicio+=$SubTotal;
	}while($grupos=pg_fetch_array($nombreTera));//while de nombreTera


    $reporte2.='<tr class="FONDO2" style="background:#CCCCCC;">
      <td align="right"><strong>['.$Ubicacion.'] '.strtoupper($NombreSubEspecialidad).':</strong></td>
	  <td align="right">'.$TotalRecetas.'</td>
	  <td align="right">'.$TotalSatis.'</td>
	  <td align="right">'.$TotalInsat.'</td>
      <td align="right">'.number_format($SubTotalServicio,3,'.',',').'</td>
    </tr>';

	$Total+=$SubTotalServicio;
	$TotalRecetasGlobal+=$TotalRecetas;
	$TotalSatisGlobal+=$TotalSatis;
	$TotalInsatGlobal+=$TotalInsat;
	
	}/*else{
		//SI NO HAY MEDICINA CONSUMIDA POR LA ESPECIALIDAD
$reporte.='<tr class="FONDO2" style="background:#CCCCCC;">
			  <td colspan="11" align="center">
		&nbsp;<strong>No Hay consumo de Grupo Terapeutico</strong></td>
			</tr>
				<tr class="FONDO2">
			<th width="37" scope="col">Codigo</th>
			  <th width="182" scope="col">Medicamento</th>
			  <th width="61" scope="col">Concen.</th>
			  <th width="54" scope="col">Prese.</th>
			  <th width="54" scope="col">Recetas</th>
			  <th width="50" scope="col">Satis.</th>
			  <th width="70" scope="col">No Satis.</th>
			  <th width="63">Unidad de Medida</th>
			  <th width="78" scope="col">Consumo</th>
			  <th width="135" scope="col">Precio[$]</th>
			  <th width="136" scope="col">Monto[$]</th>
			</tr>
						<tr class="FONDO2">
			          <td style="vertical-align:middle">&nbsp;-----</td>
			          <td align="left" style="vertical-align:middle">-----</td>
			          <td align="center" style="vertical-align:middle">-----</td>
			          <td align="center" style="vertical-align:middle">-----</td>
			          <td align="center" style="vertical-align:middle">-----</td>
			          <td align="center" style="vertical-align:middle">-----</td>
			          <td align="center" style="vertical-align:middle">-----</td>
			          <td align="center" style="vertical-align:middle">-----</td>
			          <td align="right" style="vertical-align:middle">0.00</td>
			          <td align="right" style="vertical-align:middle">0.00</td>
			          <td align="right" style="vertical-align:middle">0.00</td>
			        </tr>';	
	}*/
	}while($rowServicios=pg_fetch_array($respServicios));//While Servicios
}//Comprueba Datos de Servicio

   


    $reporte2.='<tr class="FONDO2" style="background:#CCCCCC;">
      <td align="right"><em><strong> Total Global:</strong></em></td>
	  <td align="right"><strong>'.$TotalRecetasGlobal.'</strong></td>
	  <td align="right"><strong>'.$TotalSatisGlobal.'</strong></td>
	  <td align="right"><strong>'.$TotalInsatGlobal.'</strong></td>
       <td align="right"><strong>'.number_format($Total,3,'.',',').'</strong></td>
    </tr>
	</table>';

//CIERRE DE ARCHIVO EXCEL
	fwrite($punteroarchivo,$reporte2);
	fclose($punteroarchivo);
//CIERRE ODS
	fwrite($punteroarchivo2,$reporte2);
	fclose($punteroarchivo2);

//***********************


echo '<table>
	<tr>
		
		<td align="right" style="vertical-align:middle;">
		
		<a href="'.$nombrearchivo.'"> <H5>OFFICE <img src="../images/excel.gif"></H5></a>
		</td>
		
		<td align="center" style="vertical-align:middle;">
		
		<a href="'.$nombrearchivo2.'"> <H5>LIBRE OFFICE <img src="../images/ods.png"></H5></a>
		</td>
	</tr>
	<tr>
		<td colspan=2>
		'.$reporte2.'
		</td>
	</tr>
</table>';
conexion::desconectar();
}//Fin de IF nivel == 1

}//Fin de IF isset de Nivel
?>