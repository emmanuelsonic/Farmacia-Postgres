<?php
class Classquery{

	function ObtenerQuery($Bandera,$IdArea,$q){
	switch($Bandera){
	/*FILTRACIONES*/
	case 1: 
	$sqlStr = "select msse.IdSubServicioxEstablecimiento, CodigoFarmacia,NombreSubServicio, IdServicio  as Ubicacion
						from mnt_subservicio
						inner join mnt_subservicioxestablecimiento msse 
                                                on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                                                inner join mnt_servicioxestablecimiento mse
                                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
						where NombreSubServicio like '%$q%'
                                                and mse.IdServicio='CONBMG'
						and CodigoFarmacia is not null";
 break;
 
 /*TOTALES*/
 case 0: 
 $sqlStr = "select msse.IdSubServicioxEstablecimiento, CodigoFarmacia,NombreSubServicio, IdServicio as Ubicacion
						from mnt_subservicio
						inner join mnt_subservicioxestablecimiento msse 
                                                on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                                                inner join mnt_servicioxestablecimiento mse
                                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
						where CodigoFarmacia is not null
                                                and mse.IdServicio='CONBMG'";
 break;
 
 
      }//switch
 return ($sqlStr);
	}//ObtenerQueryLike
	
	
function ObtenerQueryTotal($Bandera,$IdArea,$q){
switch($Bandera){
case 1:
 $sqlStrAux = "select count(msse.IdSubServicioxEstablecimiento) as total
				from mnt_subservicio
				inner join mnt_subservicioxestablecimiento msse 
				on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                                inner join mnt_servicioxestablecimiento mse
                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
				where NombreSubServicio like '%$q%'
				and CodigoFarmacia is not null
                                and mse.IdServicio='CONBMG'";
 break;
 
 case 0:
 $sqlStrAux = "select count(msse.IdSubServicioxEstablecimiento) as total
				from mnt_subservicio
				inner join mnt_subservicioxestablecimiento msse 
				on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                                inner join mnt_servicioxestablecimiento mse
                                on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
				where CodigoFarmacia is not null
                                and mse.IdServicio='CONBMG'";
 break;
}//switch
return($sqlStrAux);
}//ObtenerQueryTotal


}//clase query