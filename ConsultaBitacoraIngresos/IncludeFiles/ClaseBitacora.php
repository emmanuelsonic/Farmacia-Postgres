<?php

class Bitacora {

    function ExisteBitacora($FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
        $SQL = "select *
		from farm_bitacoraentregamedicamento
		where date(FechaHoraIngreso) between '$FechaInicio' and '$FechaFin'
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad
                    
                ";
        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerGrupos($IdTeraputico, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
        if ($IdTeraputico != 0) {
            $comp = "and fgt.IdTerapeutico=" . $IdTeraputico;
        } else {
            $comp = "";
        }

        $SQL = "select distinct fgt.IdTerapeutico,GrupoTerapeutico
		from mnt_grupoterapeutico fgt
		inner join farm_catalogoproductos fcp
		on fcp.IdTerapeutico=fgt.IdTerapeutico
		inner join farm_bitacoraentregamedicamento fbem
		on fbem.IdMedicina=fcp.IdMedicina
		
		where date(FechaHoraIngreso) between '$FechaInicio' and '$FechaFin'
                and fbem.IdEstablecimiento=$IdEstablecimiento
                and fbem.IdModalidad=$IdModalidad
		" . $comp;
        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerBitacora($IdTeraputico, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
        $SQL = "select fcp.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica,Presentacion,fbem.Existencia,Lote,Descripcion,UnidadesContenidas,
		date_format(date(FechaHoraIngreso),'%d-%m-%Y') as FechaIngreso,
		date_format(FechaHoraIngreso,'%l:%i:%s %p') as HoraIngreso,IdEntregaOrigen
		from farm_catalogoproductos fcp
		inner join farm_unidadmedidas fum
		on fum.IdUnidadMedida=fcp.IdUnidadMedida
		inner join farm_bitacoraentregamedicamento fbem
		on fbem.IdMedicina=fcp.IdMedicina
		inner join farm_lotes fl
		on fl.IdLote=fbem.IdLote
		left join farm_entregamedicamento fem
		on fem.IdEntrega=fbem.IdEntregaOrigen
		where date(FechaHoraIngreso) between '$FechaInicio' and '$FechaFin'
		and fcp.IdTerapeutico=" . $IdTeraputico . "
                and fbem.IdEstablecimiento=$IdEstablecimiento
                and fbem.IdModalidad=$IdModalidad
                and fl.IdEstablecimiento=$IdEstablecimiento
                and fl.IdModalidad=$IdModalidad
                and fem.IdEstablecimiento=$IdEstablecimiento
                and fem.IdModalidad=$IdModalidad
		order by Codigo,FechaIngreso";
        $resp = pg_query($SQL);
        return($resp);
    }

    function ValorDivisor($IdMedicina, $IdEstablecimiento, $IdModalidad) {
        $SQL = "select DivisorMedicina 
                from farm_divisores 
                where IdMedicina= $IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        $resp = pg_query($SQL);
        return($resp);
    }

}

?>
