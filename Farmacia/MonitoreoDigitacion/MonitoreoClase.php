<?php

include('../Clases/class.php');

class Monitoreo {

    function ObtenerPersonal() {
        $query = "select IdPersonal,Nombre
				from farm_usuarios
				where IdArea=7
				order by IdPersonal";
        $resp = mysql_query($query);
        return($resp);
    }

    function ObtenerFarmacia() {
        $query = "select IdFarmacia,Farmacia
				from mnt_farmacia";
        $resp = mysql_query($query);
        return($resp);
    }

//Farmacias

    function ObtenerIdArea() {
        $query = "select IdArea
				from mnt_areafarmacia
				order by IdFarmacia asc,IdArea";
        $resp = mysql_query($query);
        return($resp);
    }

    function ObtenerInformacion($IdEstablecimiento,$IdModalidad) {
        $query = "select farm_usuarios.Nombre,count(IdMedicinaRecetada)
				from farm_usuarios
				inner join farm_recetas
				on farm_recetas.IdPersonalIntro=farm_usuarios.IdPersonal
							
				inner join farm_medicinarecetada
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				
				where FechaHoraReg is not null
				and date(FechaHoraReg)=curdate()
				and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
                                and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                                and sec_historial_clinico.IdModalidad=$IdModalidad
                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                and farm_medicinarecetada.IdModalidad=$IdModalidad
                                and farm_usuarios.IdEstablecimiento=$IdEstablecimiento
                                and farm_usuarios.IdModalidad=$IdModalidad
                                group by farm_recetas.IdPersonalIntro
                                order by farm_recetas.IdPersonalIntro";
        $resp = mysql_query($query);
        return($resp);
    }

//Informacion

    function ObtenerInformacionEnLinea($IdPersonal,$IdEstablecimiento,$IdModalidad) {
        $query = "select IdPersonal,farm_usuarios.Nombre,case Conectado when 'S' then 'En Linea' when 'N' then '-' end as Estado
				from farm_usuarios
				where Conectado='S'
				and IdPersonal <> '$IdPersonal'
                                and IdEstablecimiento=$IdEstablecimiento
                                and IdModalidad=$IdModalidad";
        $resp = mysql_query($query);
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
        $resp = mysql_query($SQL);
        return($resp);
    }

}

?>