<?php

class Obtencion {

    function ObtenerGrupos() {

        $selectGrupo = "select * from mnt_grupoterapeutico";
        $Grupo = pg_query($selectGrupo);

        return($Grupo);
    }

//Grupos

    function ObtenerDetalleMedicamentoPorGrupo($Farmacia, $area, $IdTerapeutico, $TipoFarmacia, $IdEstablecimiento, $IdModalidad) {
        if ($Farmacia == 4 or $TipoFarmacia == 1) {
            $querySelect = "select distinct farm_catalogoproductos.Codigo, farm_catalogoproductos.IdMedicina,farm_catalogoproductos.Nombre,
                            farm_catalogoproductos.FormaFarmaceutica,farm_catalogoproductos.Concentracion, Presentacion, UnidadesContenidas,Descripcion
                            from farm_catalogoproductos
                            inner join farm_entregamedicamento
                            on farm_entregamedicamento.IdMedicina=farm_catalogoproductos.IdMedicina
                            inner join farm_unidadmedidas
                            on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
                            where farm_catalogoproductos.IdTerapeutico='$IdTerapeutico'
                            and farm_entregamedicamento.IdEstablecimiento=$IdEstablecimiento
                            and farm_entregamedicamento.IdModalidad=$IdModalidad
                            
                            order by Codigo";
        } else {
            $querySelect = "select distinct farm_catalogoproductos.Codigo, farm_catalogoproductos.IdMedicina,farm_catalogoproductos.Nombre,
                            farm_catalogoproductos.FormaFarmaceutica,farm_catalogoproductos.Concentracion, Presentacion, 
                            farm_medicinaexistenciaxarea.IdArea, UnidadesContenidas,Descripcion
                            from farm_catalogoproductos
                            inner join farm_medicinaexistenciaxarea
                            on farm_medicinaexistenciaxarea.IdMedicina=farm_catalogoproductos.IdMedicina
                            inner join farm_unidadmedidas
                            on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
                            where farm_medicinaexistenciaxarea.IdArea='$area' 
                            and farm_catalogoproductos.IdTerapeutico='$IdTerapeutico'
                            and farm_medicinaexistenciaxarea.IdEstablecimiento=$IdEstablecimiento
                            and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
                            order by Codigo";
        }
        $resp = pg_query($querySelect);

        return($resp);
    }

//DetalleMEd

    function ObtenerExistencias($IdArea, $IdMedicina, $IdEstablecimiento, $IdModalidad) {

        $RespEx = pg_query("select sum(farm_medicinaexistenciaxarea.Existencia) as TotalExistencia
                                from farm_medicinaexistenciaxarea
                                inner join farm_lotes
                                on farm_lotes.IdLote=farm_medicinaexistenciaxarea.IdLote
                                where farm_medicinaexistenciaxarea.IdArea='$IdArea' 
                                and farm_medicinaexistenciaxarea.IdMedicina='$IdMedicina'
                                and farm_medicinaexistenciaxarea.Existencia<>0
                                and farm_medicinaexistenciaxarea.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
                                and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                and farm_lotes.IdModalidad=$IdModalidad
                                
                                group by IdMedicina asc");
        return($RespEx);
    }

//ObtenerExistencias

    function ObtenerExistenciasBodega($IdMedicina, $IdEstablecimiento, $IdModalidad) {

        $RespEx = pg_query("select sum(farm_entregamedicamento.Existencia) as TotalExistencia
                                from farm_entregamedicamento
                                inner join farm_lotes
                                on farm_lotes.IdLote=farm_entregamedicamento.IdLote
                                where farm_entregamedicamento.IdMedicina='$IdMedicina'
                                and farm_entregamedicamento.Existencia<>0
                                and farm_entregamedicamento.IdEstablecimiento=$IdEstablecimiento
                                and farm_entregamedicamento.IdModalidad=$IdModalidad
                                and farm_lotes.IdEstablecimiento=$IdEstablecimiento
                                and farm_lotes.IdModalidad=$IdModalidad
                                group by IdMedicina");
        return($RespEx);
    }

//ObtenerExistencias

    function ObtenerConsumo($IdMedicina, $IdArea, $Farmacia, $TipoFarmacia, $IdEstablecimiento, $IdModalidad) {
        if ($Farmacia == 4 or $TipoFarmacia == 1) {
            $SQL = "select sum(CantidadDespachada) as TotalConsumo
		from farm_medicinadespachada fmd
		inner join farm_medicinarecetada fmr
		on fmr.IdMedicinaRecetada=fmd.IdMedicinaRecetada
		inner join farm_recetas fr
		on fr.IdReceta=fmr.IdReceta
		
		where IdAreaOrigen='$IdArea'
		and IdMedicina='$IdMedicina'
		and left(FechaEntrega,7) = left(adddate(curdate(),interval -1 month),7)
                and fmd.IdEstablecimiento=$IdEstablecimiento
                and fmd.IdModalidad=$IdModalidad
                and fmr.IdEstablecimiento=$IdEstablecimiento
                and fmr.IdModalidad=$IdModalidad
                and fr.IdEstablecimiento=$IdEstablecimiento
                and fr.IdModalidad=$IdModalidad
                
                ";
        } else {
            $SQL = "select sum(CantidadDespachada) as TotalConsumo
		from farm_medicinadespachada fmd
		inner join farm_medicinarecetada fmr
		on fmr.IdMedicinaRecetada=fmd.IdMedicinaRecetada
		inner join farm_recetas fr
		on fr.IdReceta=fmr.IdReceta
		
		where IdAreaOrigen='$IdArea'
		and IdMedicina='$IdMedicina'
		and left(FechaEntrega,7) = left(adddate(curdate(),interval -1 month),7)
                and fmd.IdEstablecimiento=$IdEstablecimiento
                and fmd.IdModalidad=$IdModalidad
                and fmr.IdEstablecimiento=$IdEstablecimiento
                and fmr.IdModalidad=$IdModalidad
                and fr.IdEstablecimiento=$IdEstablecimiento
                and fr.IdModalidad=$IdModalidad
                ";
        }

        $resp = pg_fetch_array(pg_query($SQL));
        return($resp[0]);
    }

    function ObtenerPorcentaje($IdMedicina, $IdArea) {
        /* PORCENTAJE DEL MES PASADO */
        $querySelect = "select sum(farm_medicinarecetada.Cantidad)
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where farm_recetas.IdAreaOrigen='$IdArea'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdEstado='S'
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and left(farm_medicinarecetada.FechaEntrega,7)=left(adddate(curdate(),interval -1 month),7)
				group by farm_medicinarecetada.IdMedicina";

        /* PORCENTAJE DEL MES ACTUAL */
        $querySelect2 = "select sum(farm_medicinarecetada.Cantidad)
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where farm_recetas.IdAreaOrigen='$IdArea'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdEstado='S'
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and left(farm_medicinarecetada.FechaEntrega,7)=left(curdate(),7)
				group by farm_medicinarecetada.IdMedicina";

        /* EXISTENCIAS */
        $querySelect3 = "select sum(farm_medicinaexistenciaxarea.Existencia)
				from farm_medicinaexistenciaxarea
				where farm_medicinaexistenciaxarea.IdArea='$IdArea'
				and farm_medicinaexistenciaxarea.IdMedicina='$IdMedicina'";

        $resp1 = pg_fetch_array(pg_query($querySelect));
        $resp2 = pg_fetch_array(pg_query($querySelect2));
        $resp3 = pg_fetch_array(pg_query($querySelect3));
        $datos[0] = $resp1[0]; //mes anterior
        $datos[1] = $resp2[0]; //mes actual
        $datos[2] = $resp3[0]; //mes actual
        return($datos);
    }

//ObtenerPorcentaje

    function ObtenerPorcentajeBodega($IdMedicina) {
        /* PORCENTAJE DEL MES PASADO */
        $querySelect = "select sum(farm_medicinarecetada.Cantidad)
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdEstado='S'
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and left(farm_medicinarecetada.FechaEntrega,7)=left(adddate(curdate(),interval -1 month),7)
				group by farm_medicinarecetada.IdMedicina";

        /* PORCENTAJE DEL MES ACTUAL */
        $querySelect2 = "select sum(farm_medicinarecetada.Cantidad)
				from farm_medicinarecetada
				inner join farm_recetas
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				where (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdEstado='S'
				and farm_medicinarecetada.IdMedicina='$IdMedicina'
				and left(farm_medicinarecetada.FechaEntrega,7)=left(curdate(),7)
				group by farm_medicinarecetada.IdMedicina";

        /* EXISTENCIAS */
        $querySelect3 = "select sum(farm_entregamedicamento.Existencia)
				from farm_entregamedicamento
				where farm_entregamedicamento.IdMedicina='$IdMedicina'";

        $resp1 = pg_fetch_array(pg_query($querySelect));
        $resp2 = pg_fetch_array(pg_query($querySelect2));
        $resp3 = pg_fetch_array(pg_query($querySelect3));
        $datos[0] = $resp1[0]; //mes anterior
        $datos[1] = $resp2[0]; //mes actual
        $datos[2] = $resp3[0]; //mes actual
        return($datos);
    }

//ObtenerPorcentaje

    function ValorDivisor($IdMedicina, $IdEstablecimiento,$IdModalidad) {
        $SQL = "select DivisorMedicina 
                from farm_divisores 
                where IdMedicina= $IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        $resp = pg_query($SQL);
        return($resp);
    }

}

//Clase Obtencion

class Combos {

    function Farmacias($IdEstablecimiento,$IdModalidad) {

        $SQL = "select * 
                  from mnt_farmacia mf
                  inner join mnt_farmaciaxestablecimiento mfe
                  on mfe.IdFarmacia = mf.IdFarmacia
                  where mfe.IdEstablecimiento=$IdEstablecimiento
                 and mfe.IdModalidad=$IdModalidad
                 and mfe.HabilitadoFarmacia='S'";
        $resp = pg_query($SQL);
        return($resp);
    }

    function Areas($IdFarmacia,$IdEstablecimiento, $IdModalidad) {
        $SQL = "select * 
                    from mnt_areafarmacia  maf
                    inner join mnt_areafarmaciaxestablecimiento mafe
                    on mafe.IdArea = maf.IdArea
                    where mafe.IdArea<> 7 
                    and mafe.Habilitado='S' 
                    and mafe.IdEstablecimiento=$IdEstablecimiento
                    and mafe.IdModalidad=$IdModalidad
                    and maf.IdFarmacia=" . $IdFarmacia;
        $resp = pg_query($SQL);
        return($resp);
    }

}

?>