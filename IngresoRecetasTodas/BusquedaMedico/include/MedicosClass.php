<?php
class Classquery{

	function ObtenerQuery($Bandera,$IdArea,$q,$IdEstablecimiento){
	switch($Bandera){
	/*FILTRACIONES*/
	case 1: 
  /*Se cambio id_tipo_empleado='MED'  y id_tipo_empleado='ENF'  por sus id equivalentes, 
    PROVISIONALMENTE SE ASIGNARA MED=4, Y ENF=5 */
	
	$sqlStr = "SELECT mnt_empleado.Codigo_Farmacia,mnt_empleado.Id,mnt_empleado.NombreEmpleado
                   FROM mnt_empleado
                   WHERE (mnt_empleado.NombreEmpleado like '%$q%')
                   AND mnt_empleado.Habilitado_Farmacia='H'
                   AND (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
                   AND mnt_empleado.Id_Establecimiento=$IdEstablecimiento
                   ORDER BY mnt_empleado.NombreEmpleado";

 break;
 
 /*TOTALES*/
 case 0: 
 $sqlStr = "SELECT mnt_empleado.Codigo_Farmacia,mnt_empleado.Id,mnt_empleado.NombreEmpleado
            FROM mnt_empleado
            WHERE mnt_empleado.Habilitado_Farmacia='H'
            AND (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
            AND mnt_empleado.Id_Establecimiento=$IdEstablecimiento
            ORDER BY mnt_empleado.NombreEmpleado";
	
 break;
 
      }//switch
 return ($sqlStr);
	}//ObtenerQueryLike
	
	
function ObtenerQueryTotal($Bandera,$IdArea,$q,$IdEstablecimiento){
switch($Bandera){
case 1:
 $sqlStrAux = "SELECT count(mnt_empleado.Id) as total
               FROM mnt_empleado
               WHERE (mnt_empleado.NombreEmpleado like '%$q%')
               AND mnt_empleado.Habilitado_Farmacia='H'
               AND (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
               AND mnt_empleado.Id_Establecimiento=$IdEstablecimiento";

 break;
 
 case 0:
 $sqlStrAux = "SELECT count(mnt_empleado.Id) as total
               FROM mnt_empleado
               WHERE mnt_empleado.Habilitado_Farmacia='H'
               AND (mnt_empleado.Id_Tipo_Empleado=4 or mnt_empleado.Id_Tipo_Empleado=5)
               AND mnt_empleado.Id_Establecimiento=$IdEstablecimiento";
 break;
}//switch
return($sqlStrAux);
}//ObtenerQueryTotal


}//clase query