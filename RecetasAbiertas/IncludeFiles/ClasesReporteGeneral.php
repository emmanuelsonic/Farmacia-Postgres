<?php
include($path.'../Clases/class.php');
class RecetasAbiertas{
    function CierreMes($Periodo,$IdEstablecimiento,$IdModalidad){
<<<<<<< HEAD
        $query="SELECT MesCierre 
                FROM farm_cierre 
                WHERE MesCierre='".$Periodo."'
                AND IdEstablecimiento=$IdEstablecimiento
                AND IdModalidad=$IdModalidad";
             
=======
        $query="select MesCierre from farm_cierre 
                where MesCierre='".$Periodo."'
                and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
        $resp=pg_fetch_array(pg_query($query));
        return($resp[0]);
    }
    
    function ListadoRecetasAbiertas($Periodo,$IdEstablecimiento,$IdModalidad){
        $query="select CorrelativoAnual,Nombre,Area,Fecha, IdReceta
                from farm_recetas fr
                inner join farm_usuarios fu
                on fu.IdPersonal=fr.IdPersonalIntro
                inner join mnt_areafarmacia maf
                on maf.IdArea=fr.IdArea
                where left(Fecha,7)='".$Periodo."' and IdEstado <> 'E'
                and fr.IdEstablecimiento=$IdEstablecimiento
                and fr.IdModalidad=$IdModalidad";
        $resp=pg_query($query);
        return($resp);
    }
    
    function FinalizarReceta($IdReceta,$IdEstablecimiento,$IdModalidad){
        $query="update farm_recetas set IdEstado='E' 
                where IdReceta=".$IdReceta."
                and IdEstablecimiento=$IdEstablecimiento
                and IdModalidad=$IdModalidad";
        pg_query($query);
    }
}//Clase Reporte Farmacias
?>