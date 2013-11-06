<?php
class Classquery{

	function ObtenerQuery($Bandera,$IdArea,$q,$IdEstablecimiento){
	switch($Bandera){
	/*FILTRACIONES*/
	case 1: 
  /*Se cambio id_tipo_empleado='MED'  y id_tipo_empleado='ENF'  por sus id equivalentes, 
    PROVISIONALMENTE SE ASIGNARA MED=4, Y ENF=5 */
	
	$sqlStr = "select mnt_empleado.Codigo_Farmacia,mnt_empleado.Id,mnt_empleado.NombreEmpleado
	from mnt_empleado
	where (mnt_empleado.NombreEmpleado like '%$q%')
	and mnt_empleado.Habilitado_Farmacia='H'
	and (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
	and mnt_empleado.Id_Establecimiento=$IdEstablecimiento
	order by mnt_empleado.NombreEmpleado";

 break;
 
 /*TOTALES*/
 case 0: 
 $sqlStr = "select mnt_empleado.Codigo_Farmacia,mnt_empleado.Id,mnt_empleado.NombreEmpleado
	from mnt_empleado
	where mnt_empleado.Habilitado_Farmacia='H'
	and (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
	and mnt_empleado.Id_Establecimiento=$IdEstablecimiento
	order by mnt_empleado.NombreEmpleado";
	
 break;
 
      }//switch
 return ($sqlStr);
	}//ObtenerQueryLike
	
	
function ObtenerQueryTotal($Bandera,$IdArea,$q,$IdEstablecimiento){
switch($Bandera){
case 1:
 $sqlStrAux = "select  count(mnt_empleado.Id) as total
	from mnt_empleado
	where (mnt_empleado.NombreEmpleado like '%$q%')
	and mnt_empleado.Habilitado_Farmacia='H'
	and (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
	and mnt_empleado.Id_Establecimiento=$IdEstablecimiento";

 break;
 
 case 0:
 $sqlStrAux = "select count(mnt_empleado.Id) as total
	from mnt_empleado
	where  mnt_empleado.Habilitado_Farmacia='H'
	and (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
	and mnt_empleado.Id_Establecimiento=$IdEstablecimiento";
 break;
 
}//switch
return($sqlStrAux);
}//ObtenerQueryTotal


}//clase query