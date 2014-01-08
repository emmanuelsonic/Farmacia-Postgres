<?php

function Farmacia($IdFarmaciaN) {
    $SQL = "select Farmacia from mnt_farmacia where IdFarmacia=" . $IdFarmaciaN;
    $resp = pg_fetch_array(pg_query($SQL));
    return($resp[0]);
}

function Servicios($IdSubEspecialidad, $IdTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {

    if ($IdTerapeutico != 0) {
        $comp = "and fcp.IdTerapeutico=" . $IdTerapeutico;
    } else {
        $comp = "";
    }
    if ($IdMedicina != 0) {
        $comp2 = "and fcp.IdMedicina=" . $IdMedicina;
    } else {
        $comp2 = "";
    }
    if ($IdFarmacia != 0) {
        $comp3 = "and fr.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp3 = "";
    }
    if ($IdSubEspecialidad != 0) {
        $comp4 = "and msse.IdSubServicioxEstablecimiento=" . $IdSubEspecialidad;
    } else {
        $comp4 = "";
    };


    $querySelect = "select distinct msse.IdSubServicioxEstablecimiento, NombreSubServicio
				from mnt_subservicio mss
                                inner join mnt_subservicioxestablecimiento msse
                                on msse.IdSubServicio=mss.IdSubServicio
				inner join sec_historial_clinico shc
				on shc.IdSubServicioxEstablecimiento=msse.IdSubServicioxEstablecimiento
				inner join farm_recetas fr
				on fr.IdHistorialClinico=shc.IdHistorialClinico
                                
                                inner join mnt_servicioxestablecimiento mse
                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                                
				inner join farm_medicinarecetada fmr
				on fmr.IdReceta=fr.IdReceta
				inner join farm_catalogoproductos fcp
				on fcp.IdMedicina=fmr.IdMedicina
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=fcp.IdMedicina

				where mse.IdServicio='CONEXT'
				and (fr.IdEstado='E' or fr.IdEstado='ER')
				and fr.Fecha between '$FechaInicio' and '$FechaFin'
				" . $comp . "
				" . $comp2 . "
				" . $comp3 . "
                                " . $comp4 . "
                                and msse.IdEstablecimiento=$IdEstablecimiento
                                and msse.IdModalidad=$IdModalidad
                                and shc.IdEstablecimiento=$IdEstablecimiento
                                and shc.IdModalidad=$IdModalidad
                                and mse.IdEstablecimiento=$IdEstablecimiento
                                and mse.IdModalidad=$IdModalidad
                                and fr.IdEstablecimiento=$IdEstablecimiento
                                and fr.IdModalidad=$IdModalidad
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
				order by NombreSubServicio";

    $resp = pg_query($querySelect);
    return($resp);
}

//Servicios

function NombreTera($grupoTerapeutico, $IdSubEspecialidad, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    if ($IdFarmacia != 0) {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    if ($grupoTerapeutico != 0) {
        $comp2 = "and mnt_grupoterapeutico.IdTerapeutico = '$grupoTerapeutico'";
    } else {
        $comp2 = "";
    }

    $querySelect = "select distinct mnt_grupoterapeutico.IdTerapeutico, GrupoTerapeutico
            from mnt_grupoterapeutico
            inner join farm_catalogoproductos
            on farm_catalogoproductos.IdTerapeutico = mnt_grupoterapeutico.IdTerapeutico
            inner join farm_medicinarecetada
            on farm_medicinarecetada.IdMedicina = farm_catalogoproductos.IdMedicina
            inner join farm_recetas
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            where (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'
            and sec_historial_clinico.IdSubServicioxEstablecimiento = '$IdSubEspecialidad'
            " . $comp . "
            " . $comp2 . "
            and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
            and sec_historial_clinico.IdModalidad=$IdModalidad
            and farm_recetas.IdEstablecimiento=$IdEstablecimiento
            and farm_recetas.IdModalidad=$IdModalidad
            and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
            and farm_medicinarecetada.IdModalidad=$IdModalidad
            
            order by mnt_grupoterapeutico.IdTerapeutico";

    $resp = pg_query($querySelect);
//
    return($resp);
}

//nombreTera

function QueryExterna($IdTerapeutico, $IdMedicina, $IdSubEspecialidad, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
//******todos los grupos terapeuticos
    if ($IdFarmacia != 0) {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }
    if ($IdTerapeutico != 0) {
        $comp2 = "and mnt_grupoterapeutico.IdTerapeutico=" . $IdTerapeutico;
    } else {
        $comp2 = "";
    }
    if ($IdMedicina != 0) {
        $comp3 = "and farm_catalogoproductos.IdMedicina=" . $IdMedicina;
    } else {
        $comp3 = "";
    }

    $querySelect = "select distinct farm_catalogoproductos.IdMedicina, Codigo, Nombre, Concentracion, FormaFarmaceutica, Presentacion
            from farm_catalogoproductos
            inner join mnt_grupoterapeutico
            on mnt_grupoterapeutico.IdTerapeutico = farm_catalogoproductos.IdTerapeutico
            inner join farm_medicinarecetada
            on farm_medicinarecetada.IdMedicina = farm_catalogoproductos.IdMedicina
            inner join farm_recetas
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            where sec_historial_clinico.IdSubServicioxEstablecimiento = '$IdSubEspecialidad'
            and (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and Fecha between '$FechaInicio' and '$FechaFin'
            " . $comp . "
            " . $comp2 . "
            " . $comp3 . "
            and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
            and sec_historial_clinico.IdModalidad=$IdModalidad
            and farm_recetas.IdEstablecimiento=$IdEstablecimiento
            and farm_recetas.IdModalidad=$IdModalidad
            and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
            and farm_medicinarecetada.IdModalidad=$IdModalidad
            
            order by farm_catalogoproductos.Codigo";


    $resp = pg_query($querySelect);
    return($resp);
}

//queryExterna

function ObtenerReporteGrupoTerapeutico($GrupoTerapeutico, $IdMedicina, $FechaInicio, $FechaFin, $IdSubEspecialidad, $IdEstablecimiento, $IdModalidad) {
//**Query para un GrupoTerapeutico especifico y una Medicina Especifica
//Del Query Elimine mnt_medicinarecetada.Cantidad, a la par de farm_medicinarecetada.*,

    $selectQuery = "select distinct farm_recetas.IdReceta, farm_unidadmedidas.Descripcion, farm_unidadmedidas.UnidadesContenidas as Divisor
            from farm_recetas
            inner join farm_medicinarecetada
            on farm_medicinarecetada.IdReceta = farm_recetas.IdReceta
            inner join farm_catalogoproductos
            on farm_catalogoproductos.IdMedicina = farm_medicinarecetada.IdMedicina
            inner join mnt_grupoterapeutico
            on mnt_grupoterapeutico.IdTerapeutico = farm_catalogoproductos.IdTerapeutico
            inner join farm_unidadmedidas
            on farm_catalogoproductos.IdUnidadMedida = farm_unidadmedidas.IdUnidadMedida
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            where mnt_grupoterapeutico.IdTerapeutico = '$GrupoTerapeutico'
            and farm_medicinarecetada.IdMedicina = '$IdMedicina'
            and (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and Fecha between '$FechaInicio' and '$FechaFin'
            and sec_historial_clinico.IdSubServicioxEstablecimiento = '$IdSubEspecialidad'
            and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
            and sec_historial_clinico.IdModalidad=$IdModalidad
            and farm_recetas.IdEstablecimiento=$IdEstablecimiento
            and farm_recetas.IdModalidad=$IdModalidad
            and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
            and farm_medicinarecetada.IdModalidad=$IdModalidad
            order by farm_catalogoproductos.IdMedicina";
    $resp = pg_query($selectQuery);
    return($resp);
}

//fin de ObtenerReporteGrupoTerapeutico

function ObtenerConsumoTotalMedicamento(
$IdMedicina, $FechaInicio, $FechaFin, $IdSubEspecialidad, $IdFarmacia) {
    if ($IdFarmacia != 0) {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select sum(Cantidad)as Total
            from farm_medicinarecetada
            inner join farm_recetas
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            where IdSubServicio = '$IdSubEspecialidad'
            and IdMedicina = '$IdMedicina'
            and farm_recetas.Fecha between '$FechaInicio' and '$FechaFin'
            " . $comp . "";
    $resp = pg_fetch_array(pg_query($querySelect));
    return($resp[0]);
}

//ObtenerConsumoTotalMedicamento

function ObtenerRecetasSatisfechas($IdMedicina, $FechaInicio, $FechaFin, $IdSubEspecialidad, $Bandera, $IdMedico, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {
    /* Bandera = IdSubEspeacialidad utilizado en reporte por especialidad */
    if ($IdFarmacia != 0) {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select count(farm_recetas.IdReceta) as TotalSatisfechas
            from farm_medicinarecetada
            inner join farm_recetas
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            where farm_medicinarecetada.IdMedicina = '$IdMedicina'
            and (farm_medicinarecetada.IdEstado = 'S' or farm_medicinarecetada.IdEstado = '')
            and (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and Fecha between '$FechaInicio' and '$FechaFin'
            and sec_historial_clinico.IdSubServicioxEstablecimiento = '$IdSubEspecialidad'
            " . $comp . "
            and farm_recetas.IdEstablecimiento=$IdEstablecimiento
            and farm_recetas.IdModalidad=$IdModalidad
            and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
            and farm_medicinarecetada.IdModalidad=$IdModalidad
            and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
            and sec_historial_clinico.IdModalidad=$IdModalidad";
    $resp = pg_fetch_array(pg_query($querySelect));

    return($resp[0]);
}

//satisfechas
//Para Insatisfechas
function ObtenerRecetasInsatisfechas($IdMedicina, $FechaInicio, $FechaFin, $IdSubEspecialidad, $Bandera, $IdMedico, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia != 0) {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select count(farm_recetas.IdReceta) as TotalInsatisfechas
            from farm_medicinarecetada
            inner join farm_recetas
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            where farm_medicinarecetada.IdMedicina = '$IdMedicina'
            and farm_medicinarecetada.IdEstado = 'I'
            and (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and Fecha between '$FechaInicio' and '$FechaFin'
            and sec_historial_clinico.IdSubServicioxEstablecimiento = '$IdSubEspecialidad'
            " . $comp . "
            and farm_recetas.IdEstablecimiento=$IdEstablecimiento
            and farm_recetas.IdModalidad=$IdModalidad
            and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
            and farm_medicinarecetada.IdModalidad=$IdModalidad
            and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
            and sec_historial_clinico.IdModalidad=$IdModalidad";
    $resp = pg_fetch_array(pg_query($querySelect));

    return($resp[0]);
}

//Insatisfechas

function verificaSatisfecha(
$IdMedicina, $IdReceta) {
    if ($IdReceta == 0) {
        $querySelect = "select * from farm_medicinarecetada where IdMedicina = '$IdMedicina' and (IdEstado = 'S' or IdEstado = '')";
    } else {
        $querySelect = "select * from farm_medicinarecetada where IdReceta = '$IdReceta' and IdMedicina = '$IdMedicina' and (IdEstado = 'S' or IdEstado = '')";
    }
    $resp = pg_query($querySelect);
    return($resp);
}

//verificaSatisfechos

function NumeroRecetasTotal(
$IdMedicina, $IdArea, $FechaInicio, $FechaFin) {
    $querySelect = "select count(farm_recetas.IdReceta)as TotalRecetas
            from farm_recetas
            inner join farm_medicinarecetada
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            where farm_medicinarecetada.IdMedicina = '$IdMedicina'
            and (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and Fecha between '$FechaInicio' and '$FechaFin'";
    $resp = pg_fetch_array(pg_query($querySelect));
    return($resp[0]);
}

function SumatoriaMedicamento($IdMedicina, $IdSubEspecialidad, $FechaInicio, $FechaFin, $IdFarmacia, $IdEstablecimiento, $IdModalidad) {

    if ($IdFarmacia != 0) {
        $comp = "and farm_recetas.IdFarmacia=" . $IdFarmacia;
    } else {
        $comp = "";
    }

    $querySelect = "select sum(CantidadDespachada)/UnidadesContenidas as TotalMedicamento, Lote, PrecioLote, 
                           (sum(CantidadDespachada)/UnidadesContenidas)*PrecioLote as Costo
            from farm_recetas
            inner join farm_medicinarecetada
            on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
            inner join sec_historial_clinico
            on sec_historial_clinico.IdHistorialClinico = farm_recetas.IdHistorialClinico

            inner join farm_catalogoproductos fcp
            on fcp.IdMedicina = farm_medicinarecetada.IdMedicina
            inner join farm_medicinadespachada md
            on md.IdMedicinaRecetada = farm_medicinarecetada.IdMedicinaRecetada
            inner join farm_lotes l
            on l.IdLote = md.IdLote
            inner join farm_unidadmedidas um
            on um.IdUnidadMedida = fcp.IdUnidadMedida

            where (farm_medicinarecetada.IdEstado = 'S' or farm_medicinarecetada.IdEstado = '')
            and sec_historial_clinico.IdSubServicioxEstablecimiento = '$IdSubEspecialidad'
            and farm_medicinarecetada.IdMedicina = '$IdMedicina'
            and (farm_recetas.IdEstado = 'E' or farm_recetas.IdEstado = 'ER')
            and Fecha between '$FechaInicio' and '$FechaFin'
            " . $comp . "
            and farm_recetas.IdEstablecimiento=$IdEstablecimiento
            and farm_recetas.IdModalidad=$IdModalidad
            and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
            and farm_medicinarecetada.IdModalidad=$IdModalidad
            and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
            and sec_historial_clinico.IdModalidad=$IdModalidad
            and md.IdEstablecimiento=$IdEstablecimiento
            and md.IdModalidad=$IdModalidad
            and l.IdEstablecimiento=$IdEstablecimiento
            and l.IdModalidad=$IdModalidad
            
            group by md.IdLote";
    $resp = pg_query($querySelect);
    return($resp);
}

function ObtenerPrecioMedicina(
$IdMedicina, $Ano) {
    $query = "select Precio
            from farm_preciosxano
            where IdMedicina = '$IdMedicina'
            and Ano = '$Ano'";
    $resp = pg_fetch_array(pg_query($query));
    if ($resp[0] != NULL) {
        $Respuesta = $resp[0];
    } else {
        $Respuesta = 0;
    }
    return($Respuesta);
}

function ValorDivisor($IdMedicina, $IdEstablecimiento, $IdModalidad) {
    $SQL = "select DivisorMedicina 
                from farm_divisores 
                where IdMedicina =  $IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
    $resp = pg_query($SQL);
    return($resp);
}

?>