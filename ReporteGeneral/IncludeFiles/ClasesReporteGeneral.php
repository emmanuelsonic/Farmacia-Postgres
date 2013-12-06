<?php

include($path . '../Clases/class.php');

class ReporteGeneral {

    function ObtenerNombreSubEspecialidad($IdSubEspecialidad) {
        $query = "select mnt_subservicio.IdSubServicio,NombreServicio,NombreSubServicio
			from mnt_subservicio
			inner join mnt_servicio
			on mnt_servicio.IdServicio=mnt_subservicio.IdServicio
			inner join mnt_subservicioxestablecimiento
			on mnt_subservicio.IdSubServicio=mnt_subservicioxestablecimiento.IdSubServicio
			where IdEstablecimiento=" . $_SESSION["IdEstablecimiento"] . " 
			and IdSubServicio=" . $IdSubEspecialidad;
        $resp = pg_fetch_array(pg_query($query));
        return($resp[0] . '' . $resp[1]);
    }

    function SubEspecialidad($IdSubEspecialidad, $FechaInicial, $FechaFinal, $IdEstablecimiento, $IdModalidad) {
        if ($IdSubEspecialidad != 0) {
            $comp = "and mssxe.IdSubServicioxEstablecimiento='$IdSubEspecialidad'";
        } else {
            $comp = "";
        }
        $query = "select distinct mssxe.IdSubServicioxEstablecimiento,NombreSubServicio,msxe.IdServicio,CodigoFarmacia
		from mnt_subservicio
                inner join mnt_subservicioxestablecimiento mssxe
                on mssxe.IdSubServicio = mnt_subservicio.IdSubServicio
                inner join mnt_servicioxestablecimiento msxe 
                on msxe.IdServicioxEstablecimiento = mssxe.IdServicioxEstablecimiento
		inner join sec_historial_clinico shc
		on shc.IdSubServicioxEstablecimiento=mssxe.IdSubServicioxEstablecimiento
		inner join farm_recetas fr
		on fr.IdHistorialClinico=shc.IdHistorialClinico
		
		where Fecha between '$FechaInicial' and  '$FechaFinal'
                and shc.IdEstablecimiento=$IdEstablecimiento
                and shc.IdModalidad=$IdModalidad
                and fr.IdEstablecimiento=$IdEstablecimiento
                and fr.IdModalidad=$IdModalidad
                and mssxe.IdEstablecimiento =$IdEstablecimiento
                and mssxe.IdModalidad =$IdModalidad
		" . $comp;
        $resp = pg_query($query);
        return($resp);
    }

    function ObtenerRecetas($IdSubEspecialidad, $FechaInicial, $FechaFinal) {


        $query = "select sec_historial_clinico.IdSubServicio,CodigoFarmacia,sum(if(IdFarmacia=1,1,0)) as Central,
				sum(if(IdFarmacia=2,1,0)) as Externa,sum(if(IdFarmacia=3,1,0)) as Emergencia,sum(if(IdFarmacia=4,1,0)) as Bodega,
				sum(if(IdFarmacia=1,(CantidadDespachada/UnidadesContenidas)*PrecioLote,0))as CostoCentral,
				sum(if(IdFarmacia=2,(CantidadDespachada/UnidadesContenidas)*PrecioLote,0))as CostoExterna,
				sum(if(IdFarmacia=3,(CantidadDespachada/UnidadesContenidas)*PrecioLote,0))as CostoEmergencia,
				sum(if(IdFarmacia=4,(CantidadDespachada/UnidadesContenidas)*PrecioLote,0))as CostoBodega
				
				from farm_medicinadespachada
				inner join farm_medicinarecetada
				on farm_medicinarecetada.IdMedicinaRecetada=farm_medicinadespachada.IdMedicinaRecetada
				inner join farm_recetas
				on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				inner join mnt_subservicio
				on mnt_subservicio.IdSubServicio=sec_historial_clinico.IdSubServicio
				inner join farm_catalogoproductos
				on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
				inner join farm_unidadmedidas
				on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
				inner join farm_lotes
				on farm_lotes.IdLote = farm_medicinadespachada.IdLote
				inner join mnt_grupoterapeutico
				on farm_catalogoproductos.IdTerapeutico=mnt_grupoterapeutico.IdTerapeutico
				
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina



				where Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
					and farm_medicinarecetada.IdEstado<>'I'
					and sec_historial_clinico.IdSubServicio='$IdSubEspecialidad'
                        
				group by sec_historial_clinico.IdSubServicio
				order by CodigoFarmacia";

        $resp = pg_query($query);
        return($resp);
    }

    function Farmacias($TipoFarmacia, $IdEstablecimiento, $IdModalidad) {
        if ($TipoFarmacia == 1) {
            $comp = " and mfxe.IdFarmacia <> 4";
        } else {
            $comp = "";
        }
        $SQL = "select mf.IdFarmacia,Farmacia 
                from mnt_farmacia  mf
                inner join mnt_farmaciaxestablecimiento mfxe
                on mfxe.IdFarmacia = mf.IdFarmacia
                where mfxe.HabilitadoFarmacia='S'
                and mfxe.IdEstablecimiento=$IdEstablecimiento
                and mfxe.IdModalidad=$IdModalidad                
                " . $comp;
        $resp = pg_query($SQL);
        return($resp);
    }

    function ObtenerRecetasFarmacia($IdSubEspecialidad,$IdFarmacia, $FechaInicial, $FechaFinal, $IdEstablecimiento, $IdModalidad) {


        $query = "select 
				
                            sum((CantidadDespachada/UnidadesContenidas)*PrecioLote)as Costo,farm_recetas.IdFarmacia
				
				from farm_medicinadespachada
				inner join farm_medicinarecetada
				on farm_medicinarecetada.IdMedicinaRecetada=farm_medicinadespachada.IdMedicinaRecetada
				inner join farm_recetas
				on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				
                                inner join mnt_subservicioxestablecimiento mssxe
                                on mssxe.IdSubServicioxEstablecimiento=sec_historial_clinico.IdSubServicioxEstablecimiento
                                
				inner join farm_catalogoproductos
				on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
				inner join farm_unidadmedidas
				on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
				inner join farm_lotes
				on farm_lotes.IdLote = farm_medicinadespachada.IdLote
				inner join mnt_grupoterapeutico
				on farm_catalogoproductos.IdTerapeutico=mnt_grupoterapeutico.IdTerapeutico
				
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina



				where Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
				and farm_medicinarecetada.IdEstado<>'I'
				and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'
                                and farm_medicinadespachada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinadespachada.IdModalidad=$IdModalidad
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
                                    
                                and farm_recetas.IdFarmacia=$IdFarmacia
                                
				group by sec_historial_clinico.IdSubServicioxEstablecimiento, farm_recetas.IdFarmacia
				order by mssxe.CodigoFarmacia";

        $resp = pg_query($query);
        return($resp);
    }

    function ObtenerNumeroRecetasFarmacia($IdSubEspecialidad,$IdFarmacia, $FechaInicial, $FechaFinal, $IdEstablecimiento, $IdModalidad) {



        $query = "select 
                                count(farm_medicinarecetada.IdMedicinaRecetada) as Recetas, farm_recetas.IdFarmacia
				
				from farm_medicinarecetada
				
				inner join farm_recetas
				on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				
                                inner join mnt_subservicioxestablecimiento mssxe
                                on mssxe.IdSubServicioxEstablecimiento=sec_historial_clinico.IdSubServicioxEstablecimiento
				inner join farm_catalogoproductos
				on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
				inner join farm_unidadmedidas
				on farm_unidadmedidas.IdUnidadMedida=farm_catalogoproductos.IdUnidadMedida
				
				inner join mnt_grupoterapeutico
				on farm_catalogoproductos.IdTerapeutico=mnt_grupoterapeutico.IdTerapeutico
				
				inner join farm_catalogoproductosxestablecimiento fcpe
				on fcpe.IdMedicina=farm_catalogoproductos.IdMedicina



				where farm_recetas.Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
                                and farm_medicinarecetada.IdEstado<>'I'
				and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'
                                
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
                                and mssxe.IdEstablecimiento=$IdEstablecimiento
                                and mssxe.IdModalidad=$IdModalidad
                                and farm_recetas.IdFarmacia=$IdFarmacia
                                and fcpe.IdEstablecimiento=$IdEstablecimiento
                                and fcpe.IdModalidad=$IdModalidad
                                
				group by sec_historial_clinico.IdSubServicioxEstablecimiento, farm_recetas.IdFarmacia
				order by mssxe.CodigoFarmacia";

        $resp = pg_query($query);
        return($resp);
    }

    function Titulos($IdFarmacia, $IdSubEspecialidad, $FechaInicial, $FechaFinal,$IdEstablecimiento,$IdModalidad) {
        $SQL = "select distinct farm_recetas.IdFarmacia,
                (select Farmacia 
                    from mnt_farmacia 
                    where IdFarmacia=farm_recetas.IdFarmacia
                 ) as Farmacia,
                farm_recetas.IdAreaOrigen,
                (select Area 
                    from mnt_areafarmacia 
                    where IdArea=farm_recetas.IdAreaOrigen
                ) as Area
                from farm_recetas
                inner join sec_historial_clinico shc
                on shc.IdHistorialClinico=farm_recetas.IdHistorialClinico
                inner join farm_medicinarecetada fmr
                on fmr.IdReceta=farm_recetas.IdReceta
                where Fecha between '$FechaInicial' and '$FechaFinal'
                and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
                and fmr.IdEstado<>'I'
				
                and shc.IdSubServicioxEstablecimiento='$IdSubEspecialidad'
                and farm_recetas.IdFarmacia=" . $IdFarmacia . "
                and shc.IdEstablecimiento=$IdEstablecimiento
                and shc.IdModalidad=$IdModalidad
                order by IdFarmacia,IdAreaOrigen";
        $resp = pg_query($SQL);
        return($resp);
    }

    function MonitoreoRecetas2($IdFarmacia, $IdArea, $IdSubEspecialidad, $FechaInicial, $FechaFinal,$IdEstablecimiento,$IdModalidad) {
        $SQL = "select mnt_subservicio.NombreSubServicio, 
			sum(if(IdAreaOrigen=" . $IdArea . " and IdFarmacia=" . $IdFarmacia . ",1,0)) as Total
				

				from farm_recetas
				inner join farm_medicinarecetada
				on farm_recetas.IdReceta = farm_medicinarecetada.IdReceta
				inner join farm_medicinadespachada
				on farm_medicinadespachada.IdMedicinaRecetada=farm_medicinarecetada.IdMedicinaRecetada
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				inner join mnt_subservicioxestablecimiento msse
				on msse.IdSubServicioxEstablecimiento=sec_historial_clinico.IdSubServicioxEstablecimiento
                                inner join mnt_subservicio 
                                on mnt_subservicio.IdSubServicio = msse.IdSubServicio
				inner join farm_catalogoproductos
				on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
				inner join farm_lotes
				on farm_lotes.IdLote=farm_medicinadespachada.IdLote
		where Fecha between '$FechaInicial' and '$FechaFinal'
				and (farm_recetas.IdEstado='E' or farm_recetas.IdEstado='ER')
					and farm_medicinarecetada.IdEstado<>'I'
				and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
                                and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                                and sec_historial_clinico.IdModalidad=$IdModalidad
				and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'
				group by mnt_subservicio.IdSubServicio";
        $resp = pg_query($SQL);
        return($resp);
    }

    /*     * ********************************** */
}

//Clase Reporte Farmacias
?>