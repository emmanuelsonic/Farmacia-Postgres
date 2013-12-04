<?php
class Classquery{

	function ObtenerQuery($Bandera,$IdArea,$q,$IdEstablecimiento,$IdModalidad){
	switch($Bandera){
	/*FILTRACIONES*/
	case 1: 
	$sqlStr = "select mnt_subservicioxestablecimiento.IdSubServicioxEstablecimiento, mnt_subservicioxestablecimiento.CodigoFarmacia,NombreSubServicio, IdServicio  as Ubicacion
                    from mnt_subservicio
                    inner join mnt_subservicioxestablecimiento on mnt_subservicioxestablecimiento.IdSubServicio=mnt_subservicio.IdSubServicio
                    inner join mnt_servicioxestablecimiento mse on mnt_subservicioxestablecimiento.IdServicioxEstablecimiento=mse.IdServicioxEstablecimiento
                    where NombreSubServicio like '%$q%'
                    
                    and mnt_subservicioxestablecimiento.CodigoFarmacia is not null
                    and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
                    and mnt_subservicioxestablecimiento.IdEstablecimiento=".$IdEstablecimiento."
                    and mnt_subservicioxestablecimiento.IdModalidad=$IdModalidad    ";
            // le elimine un and para que muestre todos los servicios uncluyendo lo de BM
            //  -- and mse.IdServicio <> 'CONBMG' y agrege and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
 break;
 
 /*TOTALES*/
 case 0: 
 $sqlStr = "select mnt_subservicioxestablecimiento.IdSubServicioxEstablecimiento, mnt_subservicioxestablecimiento.CodigoFarmacia,NombreSubServicio, IdServicio as Ubicacion
                from mnt_subservicio
                inner join mnt_subservicioxestablecimiento on mnt_subservicioxestablecimiento.IdSubServicio=mnt_subservicio.IdSubServicio
                inner join mnt_servicioxestablecimiento mse on mnt_subservicioxestablecimiento.IdServicioxEstablecimiento=mse.IdServicioxEstablecimiento
                where mnt_subservicioxestablecimiento.CodigoFarmacia is not null
                and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
                and mnt_subservicioxestablecimiento.IdEstablecimiento=".$IdEstablecimiento."
                and mnt_subservicioxestablecimiento.IdModalidad=$IdModalidad    ";
 break;     // le elimine un and para que muestre todos los servicios uncluyendo lo de BM
            //  -- and mse.IdServicio <> 'CONBMG' y agrege and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
 
 
      }//switch
 return ($sqlStr);
	}//ObtenerQueryLike
	
	
function ObtenerQueryTotal($Bandera,$IdArea,$q,$IdEstablecimiento,$IdModalidad){
switch($Bandera){
case 1:
 $sqlStrAux = "select count(mnt_subservicioxestablecimiento.IdSubServicioxEstablecimiento) as total
                from mnt_subservicio
                inner join mnt_subservicioxestablecimiento on mnt_subservicioxestablecimiento.IdSubServicio=mnt_subservicio.IdSubServicio
                inner join mnt_servicioxestablecimiento mse on mnt_subservicioxestablecimiento.IdServicioxEstablecimiento=mse.IdServicioxEstablecimiento
                
                where NombreSubServicio like '%$q%'
                and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
                and mnt_subservicioxestablecimiento.CodigoFarmacia is not null
                and mnt_subservicioxestablecimiento.IdEstablecimiento=".$IdEstablecimiento."
                and mnt_subservicioxestablecimiento.IdModalidad=$IdModalidad    ";
 break;     // le elimine un and para que muestre todos los servicios uncluyendo lo de BM
            //  -- and mse.IdServicio <> 'CONBMG' y agrege and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
 
 case 0:
 $sqlStrAux = "select count(mnt_subservicioxestablecimiento.IdSubServicioxEstablecimiento) as total
                from mnt_subservicio
                inner join mnt_subservicioxestablecimiento on mnt_subservicioxestablecimiento.IdSubServicio=mnt_subservicio.IdSubServicio
                inner join mnt_servicioxestablecimiento mse on mnt_subservicioxestablecimiento.IdServicioxEstablecimiento=mse.IdServicioxEstablecimiento
                
                where mnt_subservicioxestablecimiento.CodigoFarmacia is not null
                and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
                and mnt_subservicioxestablecimiento.IdEstablecimiento=".$IdEstablecimiento."
                and mnt_subservicioxestablecimiento.IdModalidad=$IdModalidad    ";
 break;     // le elimine un and para que muestre todos los servicios uncluyendo lo de BM
            //  -- and mse.IdServicio <> 'CONBMG' y agrege and mnt_subservicioxestablecimiento.CodigoFarmacia <> 0
}//switch
return($sqlStrAux);
}//ObtenerQueryTotal


}//clase query