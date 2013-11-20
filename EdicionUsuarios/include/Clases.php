<?php include("../../Clases/class.php");
class admon{
   function InformacionGral($IdPersonal){
	$SQL="select fos_user_user.nivel, fos_user_user.estadocuenta, fos_user_user. firstname ,fos_user_user. username,
				fos_user_user. datos,	fos_user_user. reportes,fos_user_user. administracion, mnt_areafarmacia.area, mnt_farmacia.farmacia,
	case  
	when fos_user_user.nivel=2 then 'Co-Administrador'
	when fos_user_user.nivel=3 then 'Tecnico de Farmacia' 
	when fos_user_user.nivel=4 then 'Digitador de Farmacia' 
	end AS nivel
from fos_user_user

left join mnt_farmacia
		on mnt_farmacia.id=fos_user_user.idfarmacia
		left join mnt_areafarmacia
		on mnt_areafarmacia.id=fos_user_user.idarea
	where fos_user_user.id=".$IdPersonal;
	$resp=pg_query($SQL);
	return($resp);
   }

   function NivelUsuario($IdPersonal){
	$SQL="select nivel from fos_user_user where id=".$IdPersonal;
	$resp=pg_fetch_array(pg_query($SQL));
	return($resp[0]);
   }
   function CambiarNivel($IdPersonal,$Nivel){
	$SQL="update farm_usuarios set Nivel=".$Nivel." where IdPersonal=".$IdPersonal;
	$resp=pg_query($SQL);
   }

   function CambioPermisos($IdPersonal,$acceso,$campo){
	$SQL="update fos_user_user set ".$campo."=".$acceso." where id_empleado=".$IdPersonal;
	pg_query($SQL);
   }

   function Farmacias($IdModalidad){
	$SQL="select *
		from mnt_farmacia
                inner join mnt_farmaciaxestablecimiento
                on mnt_farmacia.id = mnt_farmaciaxestablecimiento.idfarmacia
                where mnt_farmaciaxestablecimiento.idmodalidad=$IdModalidad";
	$resp=pg_query($SQL);
	return($resp);
   }


   function AreasFarmacia($IdFarmacia,$IdPersonal,$IdModalidad,$IdEstablecimiento){
	$SQL="select * 
		from mnt_areafarmacia 
                inner join mnt_areafarmaciaxestablecimiento
                on mnt_areafarmaciaxestablecimiento.idarea=mnt_areafarmacia .id
		where mnt_areafarmaciaxestablecimiento.idarea not in (select idarea from fos_user_user where id_empleado=".$IdPersonal." )
		and mnt_areafarmaciaxestablecimiento.habilitado='S' and mnt_areafarmacia.idfarmacia=".$IdFarmacia ." 
                and mnt_areafarmaciaxestablecimiento.idmodalidad=$IdModalidad
                and mnt_areafarmaciaxestablecimiento.idestablecimiento=$IdEstablecimiento";
	$resp=pg_query($SQL);
	return ($resp);
   }

   function CambiarArea($IdFarmacia,$IdArea,$IdPersonal){
	$SQL="update fos_user_user set idfarmacia=".$IdFarmacia." , idarea=".$IdArea." 
                where id_empleado=".$IdPersonal;
	$resp=pg_query($SQL);
   }

   function DeshabilitarCuenta($IdPersonal,$NuevoEstado){
	$SQL="update fos_user_user set estadocuenta='".$NuevoEstado."' where id_empleado=".$IdPersonal;
	pg_query($SQL);
   }
}
?>