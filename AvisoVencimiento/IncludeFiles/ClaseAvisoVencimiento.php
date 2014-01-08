<?php
require('../Clases/class.php');

class Aviso{

	
function ObtenerInformacionVencimientoProximo($IdTerapeutico,$IdMedicina){

$SQL="select Codigo,farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion, farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.Id
					inner join farm_catalogoproductos
					on farm_catalogoproductos.Id=farm_medicinaexistenciaxarea.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
					where left(farm_lotes.FechaVencimiento,7) between left(adddate(curdate(),interval 1 month),7) and left(adddate(curdate(),interval 4 month),7)
and farm_catalogoproductos.Id=$IdMedicina
					group by farm_catalogoproductos.Id
			union

	select Codigo,farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion, farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_entregamedicamento
					on farm_entregamedicamento.IdLote=farm_lotes.Id
					inner join farm_catalogoproductos
					on farm_catalogoproductos.Id=farm_entregamedicamento.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida

					where left(farm_lotes.FechaVencimiento,7) between left(adddate(curdate(),interval 1 month),7) and left(adddate(curdate(),interval 4 month),7)
and farm_catalogoproductos.Id=$IdMedicina

				group by farm_catalogoproductos.Id";

	$resp=pg_query($SQL);
	return($resp);

}


	function ObtenerVencimientoProximo($IdTerapeutico,$IdMedicina){

if($IdMedicina!=0){$comp2="and farm_catalogoproductos.Id=".$IdMedicina;}else{$comp2="";}



		$querySelect="select farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion,sum(farm_medicinaexistenciaxarea.Existencia) as Existencia, PrecioLote,
					farm_lotes.Lote,farm_lotes.FechaVencimiento,farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.Id
					inner join farm_catalogoproductos
					on farm_catalogoproductos.Id=farm_medicinaexistenciaxarea.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
					where left(farm_lotes.FechaVencimiento,7) between left(adddate(curdate(),interval 1 month),7) and left(adddate(curdate(),interval 4 month),7)
$comp2
					group by farm_catalogoproductos.Id
			union

			select Nombre,Concentracion,FormaFarmaceutica,Presentacion,sum(farm_entregamedicamento.Existencia) as Existencia, PrecioLote,
					farm_lotes.Lote,farm_lotes.FechaVencimiento,farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_entregamedicamento
					on farm_entregamedicamento.IdLote=farm_lotes.Id
					inner join farm_catalogoproductos
					on farm_catalogoproductos.Id=farm_entregamedicamento.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
$comp2
					where left(farm_lotes.FechaVencimiento,7) between left(adddate(curdate(),interval 1 month),7) and left(adddate(curdate(),interval 4 month),7)


				group by farm_catalogoproductos.Id";
		$resp=pg_query($querySelect);
		return($resp);
	}//ObtenerVencimientoProximo


function GrupoTerapeutico($IdTerapeutico){
	if($IdTerapeutico!=0){$comp="and IdTerapeutico=".$IdTerapeutico;}else{$comp="";}
   $SQL="select id as IdTerapeutico,GrupoTerapeutico from mnt_grupoterapeutico where GrupoTerapeutico <> '--' ".$comp;
	$resp=pg_query($SQL);
	return($resp);
}

function MedicinasGrupo($IdTerapeutico,$IdEstablecimiento){
   $SQL="select fcp.*
	from farm_catalogoproductos fcp
	inner join farm_catalogoproductosxestablecimiento fcpe
	on fcpe.IdMedicina=fcp.Id
	where fcpe.Id=".$IdTerapeutico."
	and Condicion='H'
	and fcp.IdEstablecimiento=".$IdEstablecimiento;
   $resp=pg_query($SQL);
   return($resp);
}


	function ValorDivisor($IdMedicina){
	   $SQL="select DivisorMedicina from farm_divisores where IdMedicina=".$IdMedicina;
	   $resp=pg_query($SQL);
	   return($resp);
    	}

	function ObtenerLotes($IdMedicina){
	$SQL="select distinct Lote
		from farm_lotes l
		inner join farm_entregamedicamento fem
		on fem.IdLote=l.IdLote
		where left(l.FechaVencimiento,7) between left(adddate(curdate(),interval 1 month),7) and left(adddate(curdate(),interval 4 month),7)
		and IdMedicina=".$IdMedicina."
                ";
	$resp=pg_query($SQL);
	return($resp);
	}
	



}//Clase Aviso


?>