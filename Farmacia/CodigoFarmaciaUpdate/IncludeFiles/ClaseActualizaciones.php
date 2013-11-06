<?php 
include('../../Clases/class.php');
class Actualizaciones{
	function DatosGenerales($pagina,$IdEstablecimiento){
		$querySelect="select distinct  mnt_empleado.IdEmpleado,Codigo_Farmacia,NombreEmpleado,Habilitado_Farmacia
					from mnt_empleado
					
					where (mnt_empleado.id_tipo_empleado=4 or mnt_empleado.id_tipo_empleado=5)
					and mnt_empleado.Id_Establecimiento=".$IdEstablecimiento."
					order by NombreEmpleado
					LIMIT 20 offset $pagina";
		$resp=pg_query($querySelect);
		return($resp);		
	}//Datos Generales
	
	function BusquedaMedico($CodigoFarmacia,$NombreEmpleado,$IdEstablecimiento){
	if($CodigoFarmacia !=''){
		$filtro="Codigo_Farmacia='$CodigoFarmacia'";
	}
	if($NombreEmpleado!=''){
		$filtro="NombreEmpleado like '%$NombreEmpleado%'";
	}
		$querySelect="select distinct mnt_empleado.IdEmpleado,Codigo_Farmacia,NombreEmpleado,Habilitado_Farmacia
					from mnt_empleado
					
					where (mnt_empleado.id_tipo_empleado=4 or mnt_empleado.id_tipo_empleado=5)
					and mnt_empleado.id_establecimiento =".$IdEstablecimiento."
					and $filtro
					order by NombreEmpleado";
		$resp=pg_query($querySelect);
		return($resp);	}//BusquedaMedico
	
	
	function Tope($IdEstablecimiento){
		$querySelect="select count(IdEmpleado)
					from mnt_empleado
					where (id_tipo_empleado=4 or id_tipo_empleado=5)
                                        and Id_Establecimiento=$IdEstablecimiento";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	
	function CodigoActualFarmacia($IdEmpleado,$IdEstablecimiento){
		$querySelect="select Codigo_Farmacia
					from mnt_empleado
					where IdEmpleado='$IdEmpleado'
                                        and id_establecimiento =".$IdEstablecimiento;
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	
	function SubEspecialidad($IdSubEspecialidad){
		$querySelect="select IdSubServicio,NombreSubServicio
					from mnt_subservicio
					where IdSubServicio=".$IdSubEspecialidad;
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[1]);		
	}//SubEspecialidad
	
	function MedicoSubEspecialidad($IdEmpleado){
		$querySelect="select distinct NombreSubServicio
					from mnt_subservicio
					inner join mnt_usuarios
					on mnt_usuarios.IdSubServicio=mnt_subservicio.IdSubServicio
					inner join mnt_empleado
					on mnt_empleado.IdEmpleado=mnt_usuarios.IdEmpleado
					where mnt_empleado.IdEmpleado='$IdEmpleado'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);		
	}//MedicoSubEspecialidad
	
	function ActualizarCodigoFarmacia($IdEmpleado,$CodigoNuevo,$IdEstablecimiento){
		$queryUpdate="update mnt_empleado set Codigo_Farmacia='$CodigoNuevo' 
                                where IdEmpleado='$IdEmpleado' and id_establecimiento =".$IdEstablecimiento;
		pg_query($queryUpdate);
	}//Actualiza Codigo
	
	function VerificaCodigo($IdEmpleado,$CodigoNuevo){
		$querySelect="select IdEmpleado from mnt_empleado where Codigo_Farmacia='$CodigoNuevo' and IdEmpleado <> '$IdEmpleado'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//varificacion de Codigo
	
	function ComboSubEspecialidades($Combo,$IdEmpleado){
		$querySelect="select IdSubServicio, NombreSubServicio
					from mnt_subservicio
					where IdServicio='CONEXT'
					order by NombreSubServicio";
		$resp=pg_query($querySelect);
		$combo="<select id='".$Combo."' name='".$Combo."' onblur='EspecialidadMedico(\"".$Combo."\",6);'>
				<option value='0'>[Seleccion ...]</option>";

		while($row=pg_fetch_array($resp)){
		$combo.="<option value='".$row[0]."'>".htmlentities($row[1])."</option>";
		}//while
		
		$combo.="</select>";		
		return($combo);
	}//COmboSubEspecialidades
	
	function VerificaUbicacionMedico($IdEmpleado,$IdSubEspecialidad){
		$querySelect="select IdEspecialidad
					from mnt_subespecialidad
					inner join mnt_empleado
					on mnt_empleado.IdSubEspecialidad=mnt_subespecialidad.IdSubEspecialidad
					where IdEmpleado='$IdEmpleado'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);		
	}//UbicacionMedico
	
	function ActualizarSubEspecialidad($IdEmpleado,$IdSubEspecialidad){
		$queryUpdate="update mnt_empleado set IdSubEspecialidad='$IdSubEspecialidad' where IdEmpleado='$IdEmpleado'";
		pg_query($queryUpdate);
	}//ActualizarSubEspecialidad
	
	function VerificaEstadoMedico($IdEmpleado,$IdEstablecimiento){
		$querySelect="select Habilitado_Farmacia
					from mnt_empleado
					where IdEmpleado='$IdEmpleado'
                                        and id_establecimiento =".$IdEstablecimiento;
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	
	function ActualizaEstadoCuenta($IdEmpleado,$NuevoEstado,$IdEstablecimiento){
		$queryUpdate="update mnt_empleado set Habilitado_Farmacia='$NuevoEstado' where IdEmpleado='$IdEmpleado' and id_establecimiento =".$IdEstablecimiento;
		pg_query($queryUpdate);
		
	}
	
}//Clase Actualizaciones

?>
