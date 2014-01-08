<?php
include($path.'../Clases/class.php');
class ReporteFarmacias{

	function PacientesBM($IdNumeroExp,$FechaInicial,$FechaFinal,$IdFarmacia,$TipoExpediente,$IdEstablecimiento,$IdModalidad){

		if($IdNumeroExp!=""){$where="and IdNumeroExp='$IdNumeroExp'";}else{$where="";}
		if($TipoExpediente=='G'){$Expediente="and IdNumeroExp like'%-%'";}else{$Expediente="and IdNumeroExp not like '0%'";}
		if($IdFarmacia!=0){$comp="and IdFarmacia=".$IdFarmacia;}else{$comp="";}

		$SQL="select distinct IdNumeroExp, 
                    (select concat_ws(' ',PrimerNombre,SegundoNombre,TercerNombre,PrimerApellido,SegundoApellido) 
                        from mnt_datospaciente mdp 
                        inner join mnt_expediente 
                        on mnt_expediente.IdPaciente=mdp.IdPaciente 
                        where IdNumeroExp=shc.IdNumeroExp
                        and mnt_expediente.IdEstablecimiento=$IdEstablecimiento
                    ) as NombrePaciente
		from sec_historial_clinico shc
                inner join mnt_subservicioxestablecimiento mssxe
                on mssxe.IdSubServicioxEstablecimiento=shc.IdSubServicioxEstablecimiento
                inner join mnt_servicioxestablecimiento msxe
                on msxe.IdServicioxEstablecimiento = mssxe.IdServicioxEstablecimiento
		inner join farm_recetas fr
		on fr.IdHistorialClinico=shc.IdHistorialClinico
		
                where FechaConsulta between '$FechaInicial' and '$FechaFinal' 
		".$Expediente."
		".$where."
		".$comp."";
                // le quite este and para que muestre a todos los pacientes y no solo a 
                // los de BM and msxe.IdServicio='CONBMG'
		$resp=pg_query($SQL);
		return($resp);
	}

/*COMBOS*/
	function GruposTerapeuticos($IdTerapeutico){
		$Complemento="";
		if($IdTerapeutico!=0){$Complemento="and IdTerapeutico='$IdTerapeutico'";}
		$query="select IdTerapeutico, GrupoTerapeutico
				from mnt_grupoterapeutico
				where GrupoTerapeutico <> '--'
				".$Complemento;
		$resp=pg_query($query);
		return($resp);				
	}//GruposTerapeutics
	
	function MedicamentosPorGrupo($IdTerapeutico,$IdEstablecimiento,$IdModalidad){

		$query="select fcp.IdMedicina, Nombre, Concentracion,FormaFarmaceutica,Presentacion,Codigo
				from farm_catalogoproductos fcp
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=fcp.IdMedicina
				where IdTerapeutico='$IdTerapeutico'
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				order by Codigo";
		$resp=pg_query($query);
		return($resp);
	}
	
/************************************/

/*		CUERPO DEL REPORTE		*/
	
	function DatosMedicamentosPorGrupo($IdTerapeutico,$IdFarmacia,$IdMedicina,$IdEstablecimiento,$IdModalidad){
		$Complemento="";
		//pasar por farm_medicinarecetada para obtener exactamente el medicamento en la base de datos
		if($IdMedicina!=0){$Complemento="and farm_catalogoproductos.IdMedicina='$IdMedicina'";}
		$query="select farm_catalogoproductos.IdMedicina,Codigo, Nombre, Concentracion,FormaFarmaceutica,Presentacion,Descripcion,UnidadesContenidas
				from farm_catalogoproductos
				inner join farm_unidadmedidas
				on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida

				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina
				
				where IdTerapeutico='$IdTerapeutico'
				and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				".$Complemento."
				order by Codigo
				";
		$resp=pg_query($query);
		return($resp);
	}

	function ConsumoMedicamento($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$Bandera,$Expediente){
		switch($IdFarmacia){
		   case 0:
			$Complemento="";
		   break;
		   case 1:
			$Complemento="and IdFarmacia=1";
		   break;
		   case 2:
			$Complemento="and IdFarmacia=2";
		   break;
		   case 3:
			$Complemento="and IdFarmacia=3";
		   break;
		   case 4:
			$Complemento="and IdFarmacia=4";
		   break;
		}//switch
		if($Bandera==1){$ConsumoReal="and farm_medicinarecetada.IdEstado<>'I'";}else{$ConsumoReal="";}

	   $SQL="select farm_medicinarecetada.IdMedicina,sum(CantidadDespachada)/UnidadesContenidas as Total,
		farm_lotes.IdLote,UnidadesContenidas,Lote,PrecioLote,
		(sum(CantidadDespachada)/UnidadesContenidas)*PrecioLote as Costo

		from farm_recetas
		inner join farm_medicinarecetada
		on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
		inner join farm_medicinadespachada
		on farm_medicinadespachada.IdMedicinaRecetada=farm_medicinarecetada.IdMedicinaRecetada
		inner join farm_lotes
		on farm_lotes.IdLote=farm_medicinadespachada.IdLote
		inner join farm_catalogoproductos
		on farm_medicinarecetada.IdMedicina=farm_catalogoproductos.IdMedicina
		inner join farm_unidadmedidas
		on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
		inner join sec_historial_clinico
		on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
		where Fecha between '".$FechaInicial."' and '".$FechaFinal."'
		and farm_medicinarecetada.IdMedicina=".$IdMedicina."
		and IdNumeroExp='$Expediente'
		".$Complemento."
		".$ConsumoReal."
		group by farm_medicinarecetada.IdMedicina,farm_lotes.IdLote";
		
		$resp=pg_query($SQL);
		return($resp);
	}
	
	
	function ObtenerPrecio($IdMedicina,$Ano){
		$query="select Precio
				from farm_preciosxano
				where IdMedicina='$IdMedicina'
				and Ano	='$Ano'";
		$resp=pg_fetch_array(pg_query($query));
		if($resp[0]!=NULL){$Respuesta=$resp[0];}else{$Respuesta=0;}
		return($Respuesta);
	}
	
//*********************************************************************************
	
	function TotalRecetas($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$Expediente){
		switch($IdFarmacia){
			case 0:
				$Complemento="";
			break;

			case 1:
				$Complemento="and IdFarmacia=1";
			break;
			case 2:
				$Complemento="and IdFarmacia=2";
			break;
			case 3:
				$Complemento="and IdFarmacia=3";
			break;
		   case 4:
				$Complemento="and IdFarmacia=4";
		   break;
		}//switch
		
		$query="select count(farm_medicinarecetada.IdMedicina) as Total
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico shc
				on shc.IdHistorialClinico=farm_recetas.IdHistorialClinico
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and IdNumeroExp='$Expediente'
				and farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				".$Complemento;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}
	
	function TotalSatisfechas($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$Expediente){
		switch($IdFarmacia){
			case 0:
				$Complemento="";
			break;

			case 1:
				$Complemento="and IdFarmacia=1";
			break;
			case 2:
				$Complemento="and IdFarmacia=2";
			break;
			case 3:
				$Complemento="and IdFarmacia=3";
			break;
		   case 4:
				$Complemento="and IdFarmacia=4";
		   break;
		}//switch
		$query="select count(farm_medicinarecetada.IdMedicina) as Total
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico shc
				on shc.IdHistorialClinico=farm_recetas.IdHistorialClinico
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and IdNumeroExp='$Expediente'
				and farm_medicinarecetada.IdEstado<>'I'
				".$Complemento;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}
	
	function TotalInsatisfechas($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$Expediente){
		switch($IdFarmacia){
			case 0:
				$Complemento="";
			break;

			case 1:
				$Complemento="and IdFarmacia=1";
			break;
			case 2:
				$Complemento="and IdFarmacia=2";
			break;
			case 3:
				$Complemento="and IdFarmacia=3";
			break;
		   case 4:
				$Complemento="and IdFarmacia=4";
		   break;
		}//switch
		$query="select count(farm_medicinarecetada.IdMedicina) as Total
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico shc
				on shc.IdHistorialClinico=farm_recetas.IdHistorialClinico
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and IdNumeroExp='$Expediente'
				and farm_medicinarecetada.IdEstado='I'
				".$Complemento;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
		
	}
	
	function IngresoPorGrupo($IdTerapeutico,$IdFarmacia,$FechaInicial,$FechaFinal,$Expediente, $IdEstablecimiento, $IdModalidad){
		//Se verifica que medicamento tiene registros en farm_medicinarecetada
		$inner=""; $where="";
			switch($IdFarmacia){
			case 0:
				$Complemento="";
			break;

			case 1:
				$Complemento="and IdFarmacia=1";
			break;
			case 2:
				$Complemento="and IdFarmacia=2";
			break;
			case 3:
				$Complemento="and IdFarmacia=3";
			break;
			case 4:
				$Complemento="and IdFarmacia=4";
			break;
		}//switch

		$query="select distinct farm_medicinarecetada.IdMedicina
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				inner join farm_catalogoproductos
				on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
				inner join mnt_grupoterapeutico
				on mnt_grupoterapeutico.IdTerapeutico=farm_catalogoproductos.IdTerapeutico
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina
				inner join sec_historial_clinico shc on shc.IdHistorialClinico=farm_recetas.IdHistorialClinico
				where farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and mnt_grupoterapeutico.IdTerapeutico='$IdTerapeutico'
				and IdNumeroExp='$Expediente'
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
				".$Complemento;
		$resp=pg_query($query);
		return($resp);
	}//Ingreso por Grupo
	
	
	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="select DivisorMedicina 
                 from farm_divisores 
                 where IdMedicina=".$IdMedicina." 
                 and IdEstablecimiento=$IdEstablecimiento
                 and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
	   return($resp);
    	}
        

        
        
        
/*************************************/
}//Clase Reporte Farmacias
?>