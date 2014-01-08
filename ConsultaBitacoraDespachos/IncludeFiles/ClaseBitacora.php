<?php

class Bitacora {

    function ExisteBitacora($FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
        $SQL = "select *
		from farm_bitacoramedicinaexistenciaxarea
		where date(FechaHoraIngreso) between '$FechaInicio' and '$FechaFin'
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad
                ";
        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerGrupos($IdTeraputico, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
        if ($IdTeraputico != 0) {
            $comp = "and fgt.IdTerapeutico=" . $IdTeraputico;
        } else {
            $comp = "";
        }
        if ($IdFarmacia != 0) {
            $comp2 = "and fbem.IdArea=" . $IdFarmacia;
        } else {
            $comp2 = "";
        }

        $SQL = "select distinct fgt.IdTerapeutico,GrupoTerapeutico
		from mnt_grupoterapeutico fgt
		inner join farm_catalogoproductos fcp
		on fcp.IdTerapeutico=fgt.IdTerapeutico
		inner join farm_bitacoramedicinaexistenciaxarea fbem
		on fbem.IdMedicina=fcp.IdMedicina
		
		where date(FechaHoraIngreso) between '$FechaInicio' and '$FechaFin'
                and fbem.IdEstablecimiento=$IdEstablecimiento
                and fbem.IdModalidad=$IdModalidad
                
		" . $comp . "
		" . $comp2 . "";
        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerBitacora($IdMedicina, $IdTeraputico, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
        if ($IdFarmacia != 0) {
            $comp = "and fbem.IdArea=" . $IdFarmacia;
        } else {
            $comp = "";
        }
        if ($IdMedicina != 0) {
            $comp2 = "and fcp.IdMedicina=" . $IdMedicina;
        } else {
            $comp2 = "";
        }

        $SQL = "select fcp.IdMedicina,Codigo,Nombre,Concentracion,FormaFarmaceutica,Presentacion,fbem.Existencia,Lote,Descripcion,UnidadesContenidas,
		Area,date_format(date(FechaHoraIngreso),'%d-%m-%Y') as FechaIngreso,
		date_format(FechaHoraIngreso,'%l:%i:%s %p') as HoraIngreso,IdExistenciaOrigen,IdTransferencia
		from farm_catalogoproductos fcp
		inner join farm_unidadmedidas fum
		on fum.IdUnidadMedida=fcp.IdUnidadMedida
		inner join farm_bitacoramedicinaexistenciaxarea fbem
		on fbem.IdMedicina=fcp.IdMedicina
		inner join farm_lotes fl
		on fl.IdLote=fbem.IdLote
		inner join mnt_areafarmacia maf
		on maf.IdArea=fbem.IdArea
		left join farm_medicinaexistenciaxarea fmexa
		on fmexa.IdExistencia=fbem.IdExistenciaOrigen
		where date(FechaHoraIngreso) between '$FechaInicio' and '$FechaFin'
		and fcp.IdTerapeutico=" . $IdTeraputico . "
                and fbem.IdEstablecimiento=$IdEstablecimiento
                and fbem.IdModalidad=$IdModalidad
                and fl.IdEstablecimiento=$IdEstablecimiento
                and fl.IdModalidad=$IdModalidad
                and fmexa.IdEstablecimiento=$IdEstablecimiento
                and fmexa.IdModalidad=$IdModalidad
		" . $comp . "
                " . $comp2 . "
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

    function Medicinas($IdTerapeutico, $IdEstablecimiento, $IdModalidad) {
        $query = "select fcp.IdMedicina,Nombre,Concentracion,FormaFarmaceutica
                    from farm_catalogoproductos fcp
                    inner join farm_catalogoproductosxestablecimiento fcpe
                    on fcpe.IdMedicina = fcp.IdMedicina
                    where fcp.IdTerapeutico=$IdTerapeutico
                    and fcpe.IdEstablecimiento=$IdEstablecimiento
                    and fcpe.IdModalidad=$IdModalidad";

        $resp = pg_query($query);
        return $resp;
    }

}

?>
