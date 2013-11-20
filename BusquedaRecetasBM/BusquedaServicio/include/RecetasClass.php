<?php
class Classquery{

	function ObtenerQuery($Bandera,$IdArea,$q,$IdEstablecimiento,$IdModalidad){
	switch($Bandera){
	/*FILTRACIONES*/
	case 1: 
	$sqlStr = "select msse.IdSubServicioxEstablecimiento, CodigoFarmacia,NombreSubServicio, ms.IdServicio  as Ubicacion
                    from mnt_subservicio
                    inner join mnt_subservicioxestablecimiento msse on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                    inner join mnt_servicioxestablecimiento mse on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                    inner join mnt_servicio ms on ms.IdServicio=mse.IdServicio
                    
                    where NombreSubServicio like '%$q%'
                    and msse.CodigoFarmacia is not null
                    and msse.CodigoFarmacia <> 0
                    and msse.IdEstablecimiento=".$IdEstablecimiento."
                    and msse.IdModalidad=$IdModalidad";
 break; //  quite este and para mostrar los de BM  -- and mse.IdServicio != 'CONBMG'  
        //  y agrege and msse.CodigoFarmacia <> 0 para que no saque los que tienen cero.
 
 /*TOTALES*/
 case 0: 
 $sqlStr = "select msse.IdSubServicioxEstablecimiento, CodigoFarmacia,NombreSubServicio, ms.IdServicio as Ubicacion
            from mnt_subservicio
            inner join mnt_subservicioxestablecimiento msse on msse.IdSubServicio=mnt_subservicio.IdSubServicio
            inner join mnt_servicioxestablecimiento mse on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
            inner join mnt_servicio ms on ms.IdServicio=mse.IdServicio
            
            where msse.CodigoFarmacia is not null
            and msse.CodigoFarmacia <> 0
            and msse.IdEstablecimiento=".$IdEstablecimiento."
            and msse.IdModalidad=$IdModalidad";
 break; //  quite este and para mostrar los de BM  -- and mse.IdServicio != 'CONBMG'  
        //  y agrege and msse.CodigoFarmacia <> 0 para que no saque los que tienen cero.
 
 
      }//switch
 return ($sqlStr);
	}//ObtenerQueryLike
	
	
function ObtenerQueryTotal($Bandera,$IdArea,$q,$IdEstablecimiento,$IdModalidad){
switch($Bandera){
case 1:
 $sqlStrAux = "select count(msse.IdSubServicioxEstablecimiento) as total
                from mnt_subservicio
                inner join mnt_subservicioxestablecimiento msse on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                inner join mnt_servicioxestablecimiento mse on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                inner join mnt_servicio ms on ms.IdServicio=mse.IdServicio
                
                where NombreSubServicio like '%$q%'
                and msse.CodigoFarmacia is not null
                and msse.CodigoFarmacia <> 0
                and msse.IdEstablecimiento=".$IdEstablecimiento."
                and msse.IdModalidad=$IdModalidad";
 break; //  quite este and para mostrar los de BM  -- and mse.IdServicio != 'CONBMG'  
        //  y agrege and msse.CodigoFarmacia <> 0 para que no saque los que tienen cero.
 
 case 0:
 $sqlStrAux = "select count(msse.IdSubServicioxEstablecimiento) as total
                from mnt_subservicio
                inner join mnt_subservicioxestablecimiento msse on msse.IdSubServicio=mnt_subservicio.IdSubServicio
                inner join mnt_servicioxestablecimiento mse on mse.IdServicioxEstablecimiento=msse.IdServicioxEstablecimiento
                inner join mnt_servicio ms on ms.IdServicio=mse.IdServicio
                
                where msse.CodigoFarmacia is not null
                and msse.CodigoFarmacia <> 0
                and msse.IdEstablecimiento=".$IdEstablecimiento."
                and msse.IdModalidad=$IdModalidad";
 break; //  quite este and para mostrar los de BM  -- and mse.IdServicio != 'CONBMG'  
        //  y agrege and msse.CodigoFarmacia <> 0 para que no saque los que tienen cero.
}//switch
return($sqlStrAux);
}//ObtenerQueryTotal


}//clase query


/*   ESTO ES EL ARCHIVO ORIGINAL 
class Classquery{

	function ObtenerQuery($Bandera,$IdArea,$q){
	switch($Bandera){
	//FILTRACIONES
	case 1: 
	$sqlStr = "select mnt_subservicio.IdSubServicio, CodigoFarmacia,NombreSubServicio, IdServicio  as Ubicacion
						from mnt_subservicio
						inner join mnt_subservicioxestablecimiento msse 
                                                on msse.IdSubServicio=mnt_subservicio.IdSubServicio
						where NombreSubServicio like '%$q%'
						and CodigoFarmacia is not null";
 break;
 
 //TOTALES
 case 0: 
 $sqlStr = "select mnt_subservicio.IdSubServicio, CodigoFarmacia,NombreSubServicio, IdServicio as Ubicacion
						from mnt_subservicio
						inner join mnt_subservicioxestablecimiento msse 
                                                on msse.IdSubServicio=mnt_subservicio.IdSubServicio
						where CodigoFarmacia is not null";
 break;
 
 
      }//switch
 return ($sqlStr);
	}//ObtenerQueryLike
	
	
function ObtenerQueryTotal($Bandera,$IdArea,$q){
switch($Bandera){
case 1:
 $sqlStrAux = "select count(mnt_subservicio.IdSubServicio) as total
				from mnt_subservicio
				inner join mnt_subservicioxestablecimiento msse 
				on msse.IdSubServicio=mnt_subservicio.IdSubServicio
				where NombreSubServicio like '%$q%'";
 break;
 
 case 0:
 $sqlStrAux = "select count(mnt_subservicio.IdSubServicio) as total
				from mnt_subservicio
				inner join mnt_subservicioxestablecimiento msse 
				on msse.IdSubServicio=mnt_subservicio.IdSubServicio";
 break;
}//switch
return($sqlStrAux);
}//ObtenerQueryTotal


}//clase query