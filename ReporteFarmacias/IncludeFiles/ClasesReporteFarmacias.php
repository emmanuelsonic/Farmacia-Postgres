<?php
include($path.'../Clases/class.php');
class ReporteFarmacias{

/*COMBOS*/
	function GruposTerapeuticos($IdTerapeutico){
		$Complemento="";
		if($IdTerapeutico!=0){$Complemento="and Id='$IdTerapeutico'";}
		$query="select id as IdTerapeutico, GrupoTerapeutico
				from mnt_grupoterapeutico
				where GrupoTerapeutico <> '--'
				".$Complemento;
		$resp=pg_query($query);
		return($resp);				
	}//GruposTerapeutics
	
	function MedicamentosPorGrupo($IdTerapeutico,$IdEstablecimiento,$IdModalidad){

		$query="select farm_catalogoproductos.id as IdMedicina, Nombre, Concentracion,FormaFarmaceutica,Presentacion,Codigo
				from farm_catalogoproductos
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.Id
				where fcpe.Id='$IdTerapeutico'
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
		if($IdMedicina!=0){$Complemento="and farm_catalogoproductos.Id='$IdMedicina'";}
                
		$query="select farm_catalogoproductos.id as IdMedicina,Codigo, Nombre, Concentracion,FormaFarmaceutica,Presentacion,Descripcion,UnidadesContenidas
				from farm_catalogoproductos
				inner join farm_unidadmedidas
				on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida

				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.Id
				
				where fcpe.Id='$IdTerapeutico'
				and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				".$Complemento."
				order by Codigo
				";
		$resp=pg_query($query);
		return($resp);
	}

	function ConsumoMedicamento($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$Bandera,$IdEstablecimiento,$IdModalidad){
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
		on farm_medicinarecetada.IdReceta=farm_recetas.Id
		inner join farm_medicinadespachada
		on farm_medicinadespachada.IdMedicinaRecetada=farm_medicinarecetada.IdMedicinaRecetada
		inner join farm_lotes
		on farm_lotes.Id=farm_medicinadespachada.IdLote
		inner join farm_catalogoproductos
		on farm_medicinarecetada.IdMedicina=farm_catalogoproductos.Id
		inner join farm_unidadmedidas
		on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
		
		where Fecha between '".$FechaInicial."' and '".$FechaFinal."'
		and farm_medicinarecetada.IdMedicina=".$IdMedicina."
                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                and farm_medicinarecetada.IdModalidad=$IdModalidad
                and farm_medicinadespachada.IdEstablecimiento=$IdEstablecimiento
                and farm_medicinadespachada.IdModalidad=$IdModalidad
		".$Complemento."
		".$ConsumoReal."
		group by farm_medicinarecetada.IdMedicina,farm_lotes.Id";
		
		$resp=pg_query($SQL);
		return($resp);
	}
	
	
//Funciones de consumo antiguas
	function ConsumoMedicamento_old($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$Bandera){
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
		$query="select sum(farm_medicinarecetada.Cantidad) as Total
				from farm_recetas
				inner join farm_medicinarecetada
				on farm_medicinarecetada.IdReceta=farm_recetas.Id
				where Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				".$Complemento."
				".$ConsumoReal."				
				and IdMedicina='$IdMedicina'
				group by IdMedicina";
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);		
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
	
	function TotalRecetas($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$IdEstablecimiento,$IdModalidad){
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
		
		$query="select count(farm_medicinarecetada.IdMedicinaRecetada) as Total
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.Id=farm_medicinarecetada.IdReceta
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
				".$Complemento;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}
	
	function TotalSatisfechas($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$IdEstablecimiento,$IdModalidad){
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
				on farm_recetas.Id=farm_medicinarecetada.IdReceta
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and farm_medicinarecetada.IdEstado<>'I'
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
				".$Complemento;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}
	
	function TotalInsatisfechas($IdMedicina,$IdFarmacia,$FechaInicial,$FechaFinal,$IdEstablecimiento,$IdModalidad){
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
				on farm_recetas.Id=farm_medicinarecetada.IdReceta
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and farm_medicinarecetada.IdEstado='I'
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
				".$Complemento;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
		
	}
	
	function IngresoPorGrupo($IdTerapeutico,$IdFarmacia,$FechaInicial,$FechaFinal,$IdEstablecimiento,$IdModalidad){
		//Se verifica que medicamento tiene registros en farm_medicinarecetada
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
				on farm_recetas.Id=farm_medicinarecetada.IdReceta
				inner join farm_catalogoproductos
				on farm_catalogoproductos.Id=farm_medicinarecetada.IdMedicina
				inner join mnt_grupoterapeutico
				on mnt_grupoterapeutico.Id=farm_catalogoproductos.IdTerapeutico

				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.Id

				where farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and mnt_grupoterapeutico.Id='$IdTerapeutico'
                                    
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				".$Complemento;
		$resp=pg_query($query);
		return($resp);
	}//Ingreso por Grupo
	
	function InsatisfechasEstimadas($IdMedicina,$FechaInicial,$FechaFinal,$IdEstablecimiento,$IdModalidad){
	   $SQL="select *
		from farm_periododesabastecido
		where (FechaInicio between '$FechaInicial' and '$FechaFinal' or FechaFin between '$FechaInicial' and '$FechaFinal')
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad
		and IdMedicina=".$IdMedicina;
	   $resp=pg_query($SQL);
	   return ($resp);
	}
	
	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="select DivisorMedicina 
                    from farm_divisores 
                    where IdMedicina=$IdMedicina
                    and IdEstablecimiento=$IdEstablecimiento
                    and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
	   return($resp);
    	}
/*************************************/
}//Clase Reporte Farmacias
?>