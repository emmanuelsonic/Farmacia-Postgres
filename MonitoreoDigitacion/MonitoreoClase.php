<?php

include('../Clases/class.php');

class Monitoreo {

    function ObtenerPersonal() {
        $query = "select IdPersonal,Nombre
				from farm_usuarios
				where IdArea=7
				order by IdPersonal";
        $resp = pg_query($query);
        return($resp);
    }

    function ObtenerFarmacia() {
        $query = "select IdFarmacia,Farmacia
				from mnt_farmacia";
        $resp = pg_query($query);
        return($resp);
    }

//Farmacias

    function ObtenerIdArea() {
        $query = "select IdArea
				from mnt_areafarmacia
				order by IdFarmacia asc,IdArea";
        $resp = pg_query($query);
        return($resp);
    }

    function ObtenerInformacion($IdEstablecimiento,$IdModalidad) {
        $query = "select farm_usuarios.Nombre,count(farm_usuarios.Id)
				from farm_usuarios
				inner join farm_recetas
				on farm_recetas.IdPersonal=farm_usuarios.Id
					
				inner join farm_medicinarecetada
				on farm_recetas.Id=farm_medicinarecetada.Id
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				
				where FechaHoraReg is not null
				and date(FechaHoraReg)=current_date
				and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
								and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                               
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_usuarios.IdEstablecimiento=$IdEstablecimiento
                                and farm_usuarios.IdModalidad=$IdModalidad
                                group by farm_usuarios.Nombre,farm_recetas.IdPersonal
                                order by farm_recetas.IdPersonal";
        $resp = pg_query($query);
        return($resp);
    }

//Informacion

    function ObtenerInformacionEnLinea($IdPersonal,$IdEstablecimiento,$IdModalidad) {
        $query = "select fos_user_user.id,farm_usuarios.nombre,
				case farm_usuarios.conectado 
				when 'S' then 'En Linea' 
				when 'N' then '-' 
				end as Estado
				from farm_usuarios, fos_user_user
				where farm_usuarios.conectado='S'
				and fos_user_user.id<> '$IdPersonal'
				
				and IdEstablecimiento=$IdEstablecimiento       /*Aqui hace  falata ver de donde se va a sacar  el establacimiento*/ 
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