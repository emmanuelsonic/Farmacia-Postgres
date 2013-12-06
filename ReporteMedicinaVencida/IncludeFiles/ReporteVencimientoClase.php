<?php

class ReporteVencimiento {

    function ObtenerInformacionVencimientoProximo($IdTerapeutico, $IdArea, $IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {

        if ($IdArea != 0) {
            $comp = " and farm_medicinavencida.IdArea=" . $IdArea;
        } else {
            $comp = "";
        }

        $SQL = "select Codigo,farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion, farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_medicinavencida
					on farm_medicinavencida.IdLote=farm_lotes.IdLote
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinavencida.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
					where farm_medicinavencida.Fecha between '$FechaInicio' and '$FechaFin'
                                        and farm_catalogoproductos.IdMedicina=$IdMedicina
                                        and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                        and farm_lotes.IdModalidad=$IdModalidad
                                        
					group by farm_catalogoproductos.IdMedicina";

        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerVencimientoProximo($IdTerapeutico, $IdArea, $IdMedicina, $FechaInicio, $FechaFin) {
        if ($IdArea != 0) {
            $comp = " and farm_medicinavencida.IdArea=" . $IdArea;
        } else {
            $comp = "";
        }
        if ($IdMedicina != 0) {
            $comp2 = "and farm_catalogoproductos.IdMedicina=" . $IdMedicina;
        } else {
            $comp2 = "";
        }



        $querySelect = "select farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion,sum(farm_medicinavencida.Existencia) as Existencia, PrecioLote,
					farm_lotes.Lote,farm_lotes.FechaVencimiento,farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_medicinavencida
					on farm_medicinavencida.IdLote=farm_lotes.IdLote
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinavencida.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
					where farm_medicinavencida.Fecha between '$FechaInicio' and '$FechaFin'
				$comp
				$comp2
					group by farm_catalogoproductos.IdMedicina
			";
        $resp = pg_query($querySelect);
        return($resp);
    }

//ObtenerVencimientoProximo

    function GrupoTerapeutico($IdTerapeutico) {
        if ($IdTerapeutico != 0) {
            $comp = "and IdTerapeutico=" . $IdTerapeutico;
        } else {
            $comp = "";
        }
        $SQL = "select IdTerapeutico,GrupoTerapeutico from mnt_grupoterapeutico where GrupoTerapeutico <> '--' " . $comp;
        $resp = pg_query($SQL);
        return($resp);
    }

    function MedicinasGrupo($IdTerapeutico,$IdMedicina, $IdEstablecimiento,$IdModalidad) {
        if($IdMedicina!=0){$comp=" and fcpe.IdMedicina=$IdMedicina";}else{$comp="";}
        $SQL = "select fcp.*
	from farm_catalogoproductos fcp
	inner join farm_catalogoproductosxestablecimiento fcpe
	on fcpe.IdMedicina=fcp.IdMedicina
	where IdTerapeutico=" . $IdTerapeutico . "
        $comp
	and fcpe.Condicion='H'
	and fcpe.IdEstablecimiento=" . $IdEstablecimiento . "
        and fcpe.IdModalidad=$IdModalidad
	order by fcp.Codigo";
        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerLotes($IdMedicina, $FechaInicio, $FechaFin) {
        $SQL = "select distinct Lote,FechaVencimiento
		from farm_lotes l
		inner join farm_medicinavencida fem
		on fem.IdLote=l.IdLote
		where Fecha between '$FechaInicio' and  '$FechaFin'
		and IdMedicina=" . $IdMedicina . "
		order by FechaVencimiento";
        $resp = pg_query($SQL);
        return($resp);
    }

    function ValorDivisor($IdMedicina) {
        $SQL = "select DivisorMedicina from farm_divisores where IdMedicina=" . $IdMedicina;
        $resp = pg_query($SQL);
        return($resp);
    }

}

//ReporteTransferencias
?>