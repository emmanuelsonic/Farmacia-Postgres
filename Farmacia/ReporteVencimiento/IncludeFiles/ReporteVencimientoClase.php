<?php

class ReporteVencimiento {

    function ObtenerInformacionVencimientoProximo($IdTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {

        $SQL = "select Codigo,farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion, farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.IdLote
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinaexistenciaxarea.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
					where farm_lotes.FechaVencimiento between '$FechaInicio' and '$FechaFin'
                                        and farm_catalogoproductos.IdMedicina=$IdMedicina
                                        and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                        and farm_lotes.IdModalidad=$IdModalidad
                                        and farm_medicinaexistenciaxarea.IdEstablecimiento=$IdEstablecimiento
                                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
                                        
					group by farm_catalogoproductos.IdMedicina
                                        
			union

	select Codigo,farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion, farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_entregamedicamento
					on farm_entregamedicamento.IdLote=farm_lotes.IdLote
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_entregamedicamento.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida

					where farm_lotes.FechaVencimiento between '$FechaInicio' and '$FechaFin'
                                        and farm_catalogoproductos.IdMedicina=$IdMedicina
                                        and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                        and farm_lotes.IdModalidad=$IdModalidad
                                        and farm_entregamedicamento.IdEstablecimiento=$IdEstablecimiento
                                        and farm_entregamedicamento.IdModalidad=$IdModalidad
                                        
                                        group by farm_catalogoproductos.IdMedicina";

        $resp = mysql_query($SQL);
        return($resp);
    }

    function ObtenerVencimientoProximo($IdTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {

        if ($IdMedicina != 0) {
            $comp2 = "and farm_catalogoproductos.IdMedicina=" . $IdMedicina;
        } else {
            $comp2 = "";
        }



        $querySelect = "select farm_catalogoproductos.Nombre,Concentracion,FormaFarmaceutica,Presentacion,sum(farm_medicinaexistenciaxarea.Existencia) as Existencia, PrecioLote,
					farm_lotes.Lote,farm_lotes.FechaVencimiento,farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.IdLote
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinaexistenciaxarea.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
					where farm_lotes.FechaVencimiento between '$FechaInicio' and '$FechaFin'
                                        $comp2
                                        and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                        and farm_lotes.IdModalidad=$IdModalidad
                                        and farm_medicinaexistenciaxarea.IdEstablecimiento=$IdEstablecimiento
                                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
					group by farm_catalogoproductos.IdMedicina
			union

			select Nombre,Concentracion,FormaFarmaceutica,Presentacion,sum(farm_entregamedicamento.Existencia) as Existencia, PrecioLote,
					farm_lotes.Lote,farm_lotes.FechaVencimiento,farm_unidadmedidas.Descripcion,
					farm_unidadmedidas.UnidadesContenidas as Divisor
					from farm_lotes
					inner join farm_entregamedicamento
					on farm_entregamedicamento.IdLote=farm_lotes.IdLote
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_entregamedicamento.IdMedicina
					inner join farm_unidadmedidas
					on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
                                        
					where farm_lotes.FechaVencimiento between '$FechaInicio' and '$FechaFin'
                                        $comp2
                                        and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                        and farm_lotes.IdModalidad=$IdModalidad
                                        and farm_entregamedicamento.IdEstablecimiento=$IdEstablecimiento
                                        and farm_entregamedicamento.IdModalidad=$IdModalidad

                                        group by farm_catalogoproductos.IdMedicina";
        $resp = mysql_query($querySelect);
        return($resp);
    }

//ObtenerVencimientoProximo

    function GrupoTerapeutico($IdTerapeutico) {
        if ($IdTerapeutico != 0) {
            $comp = "and IdTerapeutico=" . $IdTerapeutico;
        } else {
            $comp = "";
        }
        $SQL = "select IdTerapeutico,GrupoTerapeutico 
                from mnt_grupoterapeutico 
                where GrupoTerapeutico <> '--' 
                " . $comp;
        $resp = mysql_query($SQL);
        return($resp);
    }

    function MedicinasGrupo($IdTerapeutico,$IdMedicina, $IdEstablecimiento, $IdModalidad) {
        if($IdMedicina!=0){
            $comp=" and fcpe.IdMedicina=$IdMedicina";
        }else{
            $comp="";
        }
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
        $resp = mysql_query($SQL);
        return($resp);
    }

    function ObtenerLotes($IdMedicina, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
        $SQL = "select Lote,FechaVencimiento
		from farm_lotes l
		inner join farm_entregamedicamento fem
		on fem.IdLote=l.IdLote
		where FechaVencimiento between '$FechaInicio' and  '$FechaFin'
		and IdMedicina=" . $IdMedicina . "
                and l.IdEstablecimiento=$IdEstablecimiento
                and l.IdModalidad=$IdModalidad
                and fem.IdEstablecimiento=$IdEstablecimiento
                and fem.IdModalidad=$IdModalidad
                
		order by FechaVencimiento";
        $resp = mysql_query($SQL);
        return($resp);
    }

    function ValorDivisor($IdMedicina, $IdEstablecimiento, $IdModalidad) {
        $SQL = "select DivisorMedicina 
                from farm_divisores 
                where IdMedicina=$IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        $resp = mysql_query($SQL);
        return($resp);
    }

}

//ReporteTransferencias
?>