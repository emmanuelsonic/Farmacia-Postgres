<?php

include('../Clases/class.php');

class Monitoreo {

    function ObtenerPersonal() {
        $query = "select id as IdPersonal,Nombre
				from fos_user_user
				where IdArea=7
				order by Id";
        $resp = pg_query($query);
        return($resp);
    }

    function ObtenerFarmacia() {
        $query = "select id as IdFarmacia,Farmacia
				from mnt_farmacia";
        $resp = pg_query($query);
        return($resp);
    }

//Farmacias

    function ObtenerIdArea() {
        $query = "select id as IdArea
				from mnt_areafarmacia
				order by IdFarmacia asc,Id";
        $resp = pg_query($query);
        return($resp);
    }

    function ObtenerInformacion($IdEstablecimiento,$IdModalidad) {
        $query = "select fos_user_user.firstname,count(fos_user_user.Id)
				from fos_user_user
				inner join farm_recetas
				on farm_recetas.IdPersonal=fos_user_user.Id
					
				inner join farm_medicinarecetada
				on farm_recetas.Id=farm_medicinarecetada.Id
				inner join sec_historial_clinico
				on sec_historial_clinico.Idhistorialclinico=farm_recetas.IdHistorialClinico
				
				where FechaHoraReg is not null
				and date(FechaHoraReg)=current_date
				and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
								and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                               
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and fos_user_user.Id_Establecimiento=$IdEstablecimiento
                                and fos_user_user.IdModalidad=$IdModalidad
                                group by fos_user_user.firstname,farm_recetas.IdPersonal
                                order by farm_recetas.IdPersonal";
        $resp = pg_query($query);
        return($resp);
    }

//Informacion

    function ObtenerInformacionEnLinea($IdPersonal,$IdEstablecimiento,$IdModalidad) {
        $query = "select fos_user_user.id,fos_user_user.firstname,
				case fos_user_user.conectado 
				when 'S' then 'En Linea' 
				when 'N' then '-' 
				end as Estado
				from fos_user_user
				where fos_user_user.conectado='S'
				and fos_user_user.id<> '$IdPersonal'
				
				and Id_Establecimiento=$IdEstablecimiento       /*Aqui hace  falata ver de donde se va a sacar  el establacimiento*/ 
                and IdModalidad=$IdModalidad";
        $resp = pg_query($query);
        return($resp);
    }

//Informacion

    function Chat($IdPersonalD, $IdPersonal, $IdEstablecimiento,$IdModalidad) {
        $SQL = "select distinct count(whosays) as Numero, whosays 
                from chat where IdPersonalD='$IdPersonalD' 
                    and whosays='$IdPersonal' 
                    and IdEstado='D' 
                    and IdEstablecimiento=$IdEstablecimiento
                    and IdModalidad=$IdModalidad
                    group by whosays";
        $resp = pg_query($SQL);
        return($resp);
    }

}

?>