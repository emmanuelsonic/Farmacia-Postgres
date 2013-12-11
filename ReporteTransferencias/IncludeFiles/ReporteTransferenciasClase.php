<?php

require('../../Clases/class.php');

class ReporteTransferencias {

    function ObtenerTransferencias($IdPersonal, $FechaInicio, $FechaFin, $IdEstablecimiento, $IdModalidad) {
        $querySelect = "select fos_user_user.firstname,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,
					farm_transferencias.Cantidad, mnt_areafarmacia.Area, farm_transferencias.IdAreaDestino,
					farm_transferencias.Justificacion,farm_transferencias.FechaTransferencia,farm_catalogoproductos.id as IdMedicina, Descripcion
					from farm_transferencias
					inner join farm_catalogoproductos
					on farm_catalogoproductos.Id=farm_transferencias.IdMedicina
					inner join mnt_areafarmacia
					on mnt_areafarmacia.Id=farm_transferencias.IdAreaOrigen
					inner join fos_user_user
					on fos_user_user.Id=farm_transferencias.IdPersonal
					inner join farm_unidadmedidas
					on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
					where farm_transferencias.IdPersonal='$IdPersonal'
					and farm_transferencias.FechaTransferencia between '$FechaInicio' and '$FechaFin' 
                                        and farm_transferencias.IdEstablecimiento=$IdEstablecimiento
                                        and farm_transferencias.IdModalidad=$IdModalidad
                                        and fos_user_user.Id_Establecimiento=$IdEstablecimiento
                                        and fos_user_user.id_aten_area_mod_estab=$IdModalidad
					order by FechaTransferencia";
        $resp = pg_query($querySelect);
        return($resp);
    }

//ObtenerTransferencias

    function ObtenerUsuarios($IdPersonal, $IdEstablecimiento, $IdModalidad) {
        switch ($IdPersonal) {
            case 0:
                $querySelect = "select distinct fos_user_user.Id,fos_user_user.firstname
					from fos_user_user
					inner join farm_transferencias
					on farm_transferencias.IdPersonal=fos_user_user.Id
                                        where farm_transferencias.IdEstablecimiento=$IdEstablecimiento
                                        and farm_transferencias.IdModalidad=$IdModalidad";
                $resp = pg_query($querySelect);
                return($resp);

                break;
            default:
                $querySelect = "select fos_user_user.Id,fos_user_user.firstname
					from fos_user_user
					where fos_user_user.Id=$IdPersonal
                                        and Id_Establecimiento=$IdEstablecimiento
                                        and IdModalidad=$IdModalidad";
                $resp = pg_fetch_array(pg_query($querySelect));
                
                return($resp[1]);
                break;
        }//switch
    }

//ObtenerUsuarios

    function ObtenerNombreArea($IdArea) {
        $querySelect = "select Area from mnt_areafarmacia where Id='$IdArea'";
        $resp = pg_fetch_array(pg_query($querySelect));
        if ($resp != NULL) {
            return($resp[0]);
        } else {
            $resp = "Fuera de las Areas de Farmacia";
            return($resp);
        }
    }

//NombreArea

    function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad) {
        $SQL = "select DivisorMedicina 
                from farm_divisores 
                where IdMedicina= $IdMedicina
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        $resp = pg_query($SQL);
        return($resp);
    }

    function UnidadesContenidas($IdMedicina,$IdEstablecimiento,$IdModalidad) {
        $SQL = "select UnidadesContenidas,Descripcion
		from farm_unidadmedidas fu
		inner join farm_catalogoproductos fcp
		on fcp.IdUnidadMedida = fu.IdUnidadMedida
                inner join farm_catalogoproductosxestablecimiento fcpe
                on fcpe.IdMedicina=fcp.IdMedicina
		where fcpe.IdMedicina= $IdMedicina
                and fcpe.IdEstablecimiento=$IdEstablecimiento
                and fcpe.IdModalidad=$IdModalidad";
        $resp = pg_fetch_array(pg_query($SQL));
        return($resp[0]);
    }

}

//ReporteTransferencias
?>