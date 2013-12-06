<?php
require($path.'../Clases/class.php');

class RecetasProceso{

	function Combo($Tipo,$TipoFarmacia,$IdEstablecimiento,$IdModalidad){
		switch($Tipo){
			case 1:
				/*COMBO DE FARMACIA*/
				if($TipoFarmacia==1){
				$query="select mfxe.IdFarmacia, Farmacia
						from mnt_farmacia mf
                                                inner join mnt_farmaciaxestablecimiento mfxe
                                                on mfxe.IdFarmacia = mf.IdFarmacia
						where IdFarmacia <> 4 
                                                and mfxe.IdEstablecimiento=$IdEstablecimiento
                                                and mfxe.IdModalidad=$IdModalidad";
				}else{
				$query="select IdFarmacia, Farmacia
						from mnt_farmacia where IdFarmacia=4";
				}

				$resp=pg_query($query);
				$combo='';
				while($row=pg_fetch_array($resp)){
					$combo.='<option value="'.$row[0].'">'.$row[1].'</option>';
				}
				
			break;
			
			case 2:
				/*COMBO DE AREA*/
				/*COMBO DE FARMACIA*/
				if($TipoFarmacia==1){
				$query="SELECT mnt_areafarmacia.IdArea,mnt_areafarmacia.Area
						   FROM mnt_areafarmacia
                                                   inner join mnt_areafarmaciaxestablecimiento mafxe
                                                   on mafxe.IdArea=mnt_areafarmacia.IdArea
						   inner join mnt_farmacia
						   on mnt_farmacia.IdFarmacia=mnt_areafarmacia.IdFarmacia
						   WHERE mnt_areafarmacia.IdArea not in(7,12)
						   and mafxe.Habilitado='S'
                                                   and mafxe.IdEstablecimiento=$IdEstablecimiento
                                                   and mafxe.IdModalidad=$IdModalidad";
				}else{
				$query="select IdArea,Area
						from mnt_areafarmacia
						where IdArea=12 
                                                and Habilitado ='S' ";
				}
				$resp=pg_query($query);
				$combo='';
				while($row=pg_fetch_array($resp)){
					$combo.='<option value="'.$row[0].'">'.$row[1].'</option>';
				}
			break;

			
		}//switch	
	
	return($combo);
		
	}//Combo

	function ObtenerIdSubServicio($IdSubEspecialidad,$IdEstablecimiento,$IdModalidad){
		$querySelect="select mnt_subservicio.IdSubServicio
					from mnt_subservicio
					inner join mnt_subespecialidad
					on mnt_subespecialidad.NombreSubEspecialidad=mnt_subservicio.NombreSubServicio
					where mnt_subespecialidad.IdSubEspecialidad='$IdSubEspecialidad'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);	
	}//Obtener IdSubServicio


	function ObtenerEspecialidad($IdSubServicio){
		$query="select NombreSubServicio
				from mnt_subservicio
				where IdSubServicio=".$IdSubServicio;
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}



/* FIN HISTORIAL CLINICO */

	function Correlativo($Fecha){
		$querySelect="select farm_recetas.NumeroReceta
					from farm_recetas
					where farm_recetas.Fecha='$Fecha'
					and (farm_recetas.IdEstado<>'RE' and farm_recetas.IdEstado<>'ER')
					order by farm_recetas.NumeroReceta desc
					limit 1";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}

	function ObtenerIdReceta($IdHistorialClinico){
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='XX'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}
	function ObtenerExpediente($Expediente){
		$querySelect="select mnt_expediente.IdNumeroExp,mnt_datospaciente.PrimerNombre,
					mnt_datospaciente.PrimerApellido,mnt_expediente.IdNumeroExp
					from  mnt_expediente
					inner join mnt_datospaciente
					on mnt_datospaciente.IdPaciente=mnt_expediente.IdPaciente
					where mnt_expediente.IdNumeroExp='$Expediente'";
		//Obtenemos el ultimo IdHistorialClinico del Paciente
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//ObtenerExpediente
	
	
	function IntroducirRecetaNueva($IdHistorialClinico,$IdMedico,$IdPersonal,$Fecha,$IdArea){
		$Correlativo=RecetasProceso::Correlativo($Fecha);
		$Correlativo++;
		$queryInsert=" insert into farm_recetas(IdHistorialClinico,Fecha,IdEstado,IdArea,NumeroReceta,IdPersonalIntro) values('$IdHistorialClinico','$Fecha','XX','$IdArea','$Correlativo','$IdPersonal')";
		pg_query($queryInsert);
	}//Intro Receta Nueva
	
	
		
	function ObtenerComboAreas($IdFarmacia){
		$filtro='';
		if($IdFarmacia!=0){$filtro='and farm_recetas.IdFarmacia='.$IdFarmacia;}
		$query="select distinct mnt_areafarmacia.IdArea,Area
				from mnt_areafarmacia
				inner join farm_recetas
				on farm_recetas.IdAreaOrigen=mnt_areafarmacia.IdArea
				where mnt_areafarmacia.IdArea <> 7
				and Habilitado ='S'
				".$filtro;
		$resp=pg_query($query);
		return($resp);
	}//Combo Areas



	
	
	
	function ObtenerMedicinaIntroducida($IdReceta){
		$querySelect="select IdHistorialClinico, farm_medicinarecetada.*,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,IdArea,FormaFarmaceutica,Presentacion
						from farm_medicinarecetada
						inner join farm_catalogoproductos
						on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
						inner join farm_recetas
						on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
						where farm_medicinarecetada.IdReceta = '$IdReceta'
						order by farm_medicinarecetada.IdMedicinaRecetada desc";
		$resp=pg_query($querySelect);
		return($resp);
	}//Obtener Medicina Introducida
	
	
	function ObtenerDatosGenerales($IdReceta){
		$query="select Area, NombreEmpleado,NombreSubEspecialidad, case IdEspecialidad when 0 then 'CON. EXT.' when 3 then 'CON. EXT.' when 4 then '' end as Origen
				from farm_recetas
				inner join mnt_areafarmacia
				on mnt_areafarmacia.IdArea=farm_recetas.IdArea
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				inner join mnt_empleados
				on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
				inner join mnt_subespecialidad
				on mnt_subespecialidad.IdSubEspecialidad=sec_historial_clinico.IdSubEspecialidad
				where IdReceta=".$IdReceta;
		$resp=pg_fetch_array(pg_query($query));
		return($resp);		
	}
	
	
	
	function RecetaLista($IdReceta){
		$queryUpdate="update farm_recetas set IdEstado='E' where IdReceta='$IdReceta'";
		pg_query($queryUpdate);
	}//Receta Lista


	function ObtenerRecetas($IdMedicina,$IdFarmacia,$IdArea,$IdSubEspecialidad,$IdEmpleado,$FechaInicial,$FechaFinal,$TipoFarmacia,$IdEstablecimiento,$IdModalidad){
		$filtro1='';$filtro2='';$filtro3='';$filtro4='';$filtro5='';$TipoFarmaciaFiltro="";
		
		if($TipoFarmacia==2){
			$TipoFarmaciaFiltro="and farm_recetas.IdFarmacia=4";
		}
		
		if($IdMedicina!=''){
			$filtro5="and farm_medicinarecetada.IdMedicina='$IdMedicina'";
		}

		if($IdFarmacia!=0){
			$filtro1="and farm_recetas.IdFarmacia='$IdFarmacia'";
		}
		if($IdArea!=0){
			$filtro2="and farm_recetas.IdAreaOrigen='$IdArea'";
		}
		if($IdSubEspecialidad!=''){
			$filtro3="and sec_historial_clinico.IdSubServicioxEstablecimiento='$IdSubEspecialidad'";
		}
		if($IdEmpleado!=''){
			$filtro4="and sec_historial_clinico.IdEmpleado='$IdEmpleado'";
		}
		//Filtracion de informacion
		
		//****************************
		
		$query="select IdMedicinaRecetada,farm_medicinarecetada.IdMedicina,farm_recetas.IdReceta,farm_recetas.IdReceta, farm_catalogoproductos.Nombre, 
                        Concentracion, FormaFarmaceutica, Presentacion, Cantidad,NombreEmpleado, Fecha, Area, CorrelativoAnual,
                        if(farm_usuarios.Nombre is not null,farm_usuarios.Nombre,concat_ws(' ','Dr(a).',NombreEmpleado)) as Digitador,
                        case farm_recetas.IdFarmacia 
			when 1 then 'Central' 
			when 2 then 'Con. Externa' 
			when 3 then 'Emergencias' 
			when 4 then 'Bodega' end as NombreFarmacia

				from farm_recetas
				inner join farm_medicinarecetada
				on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
				inner join farm_catalogoproductos
				on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				inner join mnt_empleados
				on mnt_empleados.IdEmpleado=sec_historial_clinico.IdEmpleado
				inner join mnt_areafarmacia
				on mnt_areafarmacia.IdArea=farm_recetas.IdAreaOrigen
				left join farm_usuarios
				on farm_usuarios.IdPersonal=farm_recetas.IdPersonalIntro
				inner join mnt_subservicioxestablecimiento msse
				on msse.IdSubServicioxEstablecimiento=sec_historial_clinico.IdSubServicioxEstablecimiento
                                inner join mnt_subservicio
                                on mnt_subservicio.IdSubServicio = msse.IdSubServicioxEstablecimiento
				
				where Fecha between '$FechaInicial' and '$FechaFinal'
                                and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
                                and sec_historial_clinico.IdEstablecimiento=$IdEstablecimiento
                                and sec_historial_clinico.IdModalidad=$IdModalidad    
				$TipoFarmaciaFiltro
				$filtro5
				$filtro1
				$filtro2
				$filtro3
				$filtro4				
				order by Area,Fecha";
		$resp=pg_query($query);
		return($resp);
	}//Obtener Recetas 

	
	function DetalleReceta($IdReceta,$IdEstablecimiento,$IdModalidad){
		$query="select IdMedicinaRecetada,farm_recetas.IdReceta,farm_medicinarecetada.IdMedicina,Nombre, Concentracion, FormaFarmaceutica,Presentacion,Cantidad,
			case farm_medicinarecetada.IdEstado 
			when 'S' then 'S' 
			when 'I' then 'I' 
			when '' then 'S' end as EstadoMedicina
			
			from farm_recetas
			inner join farm_medicinarecetada
			on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
			where farm_recetas.IdReceta=".$IdReceta."
                        and farm_recetas.IdEstablecimiento=$IdEstablecimiento
                        and farm_recetas.IdModalidad=$IdModalidad
                        and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                        and farm_medicinarecetada.IdModalidad=$IdModalidad";
		$resp=pg_query($query);
		return($resp);
	}//DetalleReceta

	function IdAreaMedicinaRecetada($IdMedicinaRecetada,$IdEstablecimiento,$IdModalidad){
	   $SQL="select IdArea
	   from farm_recetas r
	   inner join farm_medicinarecetada mr
	   on mr.IdReceta=r.IdReceta
	
	   where mr.IdMedicinaRecetada=$IdMedicinaRecetada
           and mr.IdEstablecimiento=$IdEstablecimiento
           and mr.IdModalidad=$IdModalidad";
	   $resp=pg_fetch_array(pg_query($SQL));
	   return($resp[0]);
	}
	
	function EliminarMedicina($IdMedicinaRecetada,$IdEstablecimiento,$IdModalidad){
		$IdArea=$this->IdAreaMedicinaRecetada($IdMedicinaRecetada,$IdEstablecimiento,$IdModalidad);

		$this->AumentarInventario($IdMedicinaRecetada,$IdArea,$IdEstablecimiento,$IdModalidad);

		$query="delete from farm_medicinarecetada 
                        where IdMedicinaRecetada=$IdMedicinaRecetada
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);
		
	}//Eliminar Medicina
	
	function GuardarNuevaMedicina($IdReceta,$IdMedicina,$Cantidad,$IdEstablecimiento,$IdModalidad){

	   $Fecha=pg_fetch_array(
			pg_query("select IdArea,Fecha 
                                     from farm_recetas 
                                     where IdReceta='$IdReceta'
                                     and IdEstablecimiento=$IdEstablecimiento
                                     and IdModalidad=$IdModalidad")
		  );
	   $FechaEntrega=$Fecha["Fecha"];
		
	   $query="insert into farm_medicinarecetada (IdReceta,IdMedicina,Cantidad,Dosis,FechaEntrega,IdEstado,IdEstablecimiento,IdModalidad) 
                                               values('$IdReceta','$IdMedicina','$Cantidad','-','$FechaEntrega','S',$IdEstablecimiento,$IdModalidad)";
	   pg_query($query);

	    //manejo de existencias
		$IdMedicinaRecetada=pg_insert_id();
		$IdArea=$Fecha["IdArea"];

		$this->ActualizarInventario($IdMedicina,$IdMedicinaRecetada,$Cantidad,$IdArea,$FechaEntrega,$IdEstablecimiento,$IdModalidad);
	   //

		
	}
	
	
	function ObtenerCodigoFarmacia($IdMedico){
		$querySelect="select CodigoFarmacia
					from mnt_empleados
					where IdEmpleado='$IdMedico'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//CodigoFarmacia
	
	
	function ObtenerDatosMedico($CodigoFarmacia,$IdEstablecimiento){
		$querySelect="select mnt_empleados.IdEmpleado, mnt_empleados.NombreEmpleado
					from mnt_empleados
					where mnt_empleados.CodigoFarmacia='$CodigoFarmacia'
                                        and IdEstablecimiento=$IdEstablecimiento";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp);
	}//ObtenerDatosMedico
	
	/*	ACUTALIZACION DE INFORMACION	*/
	function ActualizarArea($IdArea,$IdReceta){
		$query="update farm_recetas set IdArea='$IdArea' where IdReceta='$IdReceta'";
		pg_query($query);
		$query2="select Area from mnt_areafarmacia where IdArea='$IdArea'";
		$resp=pg_fetch_array(pg_query($query2));
		$salida="<a href='#' onClick='javascript:Correcciones(\"NombreArea\");'>".$resp[0]."</a>";
		return($salida);		
	}//actualizaicon de area
	
	function ActualizarMedico($IdHistorialClinico,$IdMedico){
		$query="update sec_historial_clinico set IdEmpleado='$IdMedico' where IdHistorialClinico='$IdHistorialClinico'";
		pg_query($query);
		$query2="select NombreEmpleado from mnt_empleados where IdEmpleado='$IdMedico'";
		$resp=pg_fetch_array(pg_query($query2));
		$salida="<a href='#' onClick='javascript:Correcciones(\"NombreMedico\");'>".$resp[0]."</a>";
		return($salida);
	}

	function ActualizaEstadoMedicina($IdMedicinaRecetada,$IdEstado,$IdEstablecimiento,$IdModalidad){
		$query="update farm_medicinarecetada set IdEstado='$IdEstado' 
                        where IdMedicinaRecetada=$IdMedicinaRecetada
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);
	}
	
	function ActualizarEspecialidad($IdHistorialClinico,$IdSubEspecialidad,$IdEstablecimiento,$IdModalidad){
		$IdSubServicio=RecetasProceso::ObtenerIdSubServicio($IdSubEspecialidad,$IdEstablecimiento,$IdModalidad);
		
		$query="update sec_historial_clinico set IdSubServicio='$IdSubServicio',IdSubEspecialidad='$IdSubEspecialidad' 
                        where IdHistorialClinico='$IdHistorialClinico'
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);
		
		$query2="select NombreSubEspecialidad, case IdEspecialidad when 0 then 'CON. EXT.' when 3 then 'CON. EXT.' when 4 then '' end as Origen
				from mnt_subespecialidad
				where IdSubEspecialidad='$IdSubEspecialidad'";
		$resp=pg_fetch_array(pg_query($query2));
		if($resp[1]!='' and $resp[1]!=NULL){$Especialidad=$resp[1].' -> '.$resp[0];}else{$Especialidad=$resp[0];}
		$respuesta="<a href='#' onClick='javascript:Correcciones(\"Especialidad\");'>".$Especialidad."</a>";
		return($respuesta);
	}
	
	function ObtenerArea(){
		$query="select IdArea, Area
		from mnt_areafarmacia
		where IdArea != 7";
		$resp=pg_query($query);
		return($resp);
	}

	
	function Cierre($Fecha,$IdEstablecimiento,$IdModalidad){
		$sql="select AnoCierre
			from farm_cierre
			where AnoCierre=year('".$Fecha."')
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		$resp=pg_query($sql);
		return($resp);		
	}//Cierre
		
	function CierreMes($Fecha,$IdEstablecimiento,$IdModalidad){
		$sql="select MesCierre
			from farm_cierre
			where MesCierre=left('$Fecha',7)
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		$resp=pg_query($sql);
		return($resp);		
	}//CierreMes

//**************	MANEJO DE EXISTENCIAS POR LOTES		**********************************
	
	//Actualizacion de inventario e identificacion de lote utilizado por medicamento
	
	function ActualizarInventario($IdMedicina,$IdMedicinaRecetada,$Cantidad,$IdArea,$Fecha,$IdEstablecimiento,$IdModalidad){
		$queryLote="select fl.IdLote,Existencia,FechaVencimiento
			from farm_lotes fl
			inner join farm_entregamedicamento fme
			on fme.IdLote=fl.IdLote
			where fme.IdMedicina=$IdMedicina
			and Existencia <> 0
			and left('$Fecha',7) <= left(FechaVencimiento,7)
			and fme.IdEstablecimiento=$IdEstablecimiento
                        and fme.IdModalidad=$IdModalidad
			order by FechaVencimiento asc";
		$lotes=pg_query($queryLote);
                 //Se recorren los siguiente lotes... Modo iterativo
		while($lotesA=pg_fetch_array($lotes)){
				if($Cantidad <= $lotesA["Existencia"]){
				//****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
				$IdLote=$lotesA["IdLote"];
				$existencia_old=$lotesA["Existencia"];
				$existencia_new=$existencia_old-$Cantidad;
					
					//se actualiza la existencia del lote en uso
					$actualiza="update farm_entregamedicamento set Existencia='$existencia_new' 
                                                    where IdLote='$IdLote' and IdMedicina='$IdMedicina'
                                                    and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
					pg_query($actualiza);
				
					//se ingresa el lote utilizado
					$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                              values('$IdMedicinaRecetada','$IdLote','$Cantidad',$IdEstablecimiento,$IdModalidad)";
					pg_query($query);
				
					//Se termina el lazo porque el lote en cuestion suple la demanda restante
					break;
				}else{
				
				//Primer lote a agotar...
				$IdLote=$lotesA["IdLote"];
				$existencia_old=$lotesA["Existencia"];
				//Medicina que aun falta por despachar
					$restante2=$Cantidad-$existencia_old;
				//Se cierra el lote con existencia = 0
					$actualiza="update farm_entregamedicamento set Existencia='0' 
                                                    where IdLote='$IdLote' and IdMedicina='$IdMedicina'
                                                    and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
					pg_query($actualiza);
					
					//se ingresa el lote utilizado
					$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                              values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
					pg_query($query);
					
					$Cantidad=$restante2;
					
				
				}//else de la comparacion de restante vs existencia
				
			}// Recorrido de los demas lotes con existencia while
              }//actualizar inventario
                
/* funcion anterior                
		
                $lotesA=pg_fetch_array($lotes);
 

		if($Cantidad <= $lotesA["Existencia"]){
		//****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
			
			$IdLote=$lotesA["IdLote"];
			$existencia_old=$lotesA["Existencia"];
			$existencia_new=$existencia_old-$Cantidad;
			
			$actualiza="update farm_entregamedicamento set Existencia='$existencia_new' 
                                    where IdLote='$IdLote' 
                                    and IdMedicina='$IdMedicina'
                                    and IdEstablecimiento=$IdEstablecimiento
                                    and IdModalidad=$IdModalidad";
			pg_query($actualiza);
			
			//se ingresa el lote utilizado
			$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                              values('$IdMedicinaRecetada','$IdLote','$Cantidad',$IdEstablecimiento,$IdModalidad)";
			pg_query($query);
			
			
		
		}else{
		//****** Si la existencia del lote es menor a lo que se descargara, se debe utilizar el segundo lote...
			
			//Primer lote a agotar...
			$IdLote=$lotesA["IdLote"];
			$existencia_old=$lotesA["Existencia"];
				//Medicina que aun falta por despachar
				$restante=$Cantidad-$existencia_old;
				
				
				
			//Se cierra el lote con existencia = 0
				$actualiza="update farm_entregamedicamento set Existencia='0' 
                                            where IdMedicina='$IdMedicina' 
                                            and IdLote='$IdLote'
                                            and IdEstablecimiento=$IdEstablecimiento
                                            and IdModalidad=$IdModalidad";
				pg_query($actualiza);
				
				//se ingresa el lote utilizado
			$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                              values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
			pg_query($query);
				
			//Se recorren los siguiente lotes... Modo iterativo
			while($lotesA=pg_fetch_array($lotes)){
				if($restante <= $lotesA["Existencia"]){
				//****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
				$IdLote=$lotesA["IdLote"];
				$existencia_old=$lotesA["Existencia"];
				$existencia_new=$existencia_old-$restante;
					
					//se actualiza la existencia del lote en uso
					$actualiza="update farm_entregamedicamento set Existencia='$existencia_new' 
                                                    where IdLote='$IdLote' 
                                                    and IdMedicina='$IdMedicina'
                                                    and IdEstablecimiento=$IdEstablecimiento
                                                    and IdModalidad=$IdModalidad";
					pg_query($actualiza);
				
					//se ingresa el lote utilizado
					$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                              values('$IdMedicinaRecetada','$IdLote','$restante',$IdEstablecimiento,$IdModalidad)";
					pg_query($query);
				
					//Se termina el lazo porque el lote en cuestion suple la demanda restante
					break;
				}else{
				
				//Primer lote a agotar...
				$IdLote=$lotesA["IdLote"];
				$existencia_old=$lotesA["Existencia"];
				//Medicina que aun falta por despachar
					$restante2=$restante-$existencia_old;
				//Se cierra el lote con existencia = 0
					$actualiza="update farm_entregamedicamento set Existencia='0' 
                                                    where IdLote='$IdLote' 
                                                    and IdMedicina='$IdMedicina'
                                                    and IdEstablecimiento=$IdEstablecimiento
                                                    and IdModalidad=$IdModalidad";
					pg_query($actualiza);
					
					//se ingresa el lote utilizado
					$query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                              values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
					pg_query($query);
					
					$restante=$restante2;
					
				
				}//else de la comparacion de restante vs existencia
				
			}// Recorrido de los demas lotes con existencia
			
						
		}//else de cantidad vs existencia si no suple la demanda el primer lote */
		
		

	
	
	
	//MANEJO DE LOTES CUANDO SE ELIMINA UNA RECETA POR CORRECCION
	
	function AumentarInventario($IdMedicinaRecetada,$IdArea,$IdEstablecimiento,$IdModalidad){
		$query="select IdMedicina,CantidadDespachada,IdLote,IdMedicinaDespachada 
			from farm_medicinadespachada
			inner join farm_medicinarecetada fmr
			on fmr.IdMedicinaRecetada=farm_medicinadespachada.IdMedicinaRecetada
			where fmr.IdMedicinaRecetada=$IdMedicinaRecetada
                        and fmr.IdEstablecimiento=$IdEstablecimiento
                        and fmr.IdModalidad=$IdModalidad";
		$resp=pg_query($query);
		
		while($row=pg_fetch_array($resp)){
			$CantidadDespacha=$row["CantidadDespachada"];
			$IdLoteDespachado=$row["IdLote"];
			$IdMedicinaDespachada=$row["IdMedicinaDespachada"];
			$IdMedicina=$row["IdMedicina"];
		
		//Obtencion de existencias actuales del lote utilizado
			$queryExistencia="select Existencia 
				from farm_entregamedicamento
				where IdMedicina='$IdMedicina' 
                                and IdLote='$IdLoteDespachado'
                                and IdEstablecimiento=$IdEstablecimiento
                                and IdModalidad=$IdModalidad";
			$datos=pg_fetch_array(pg_query($queryExistencia));
			
		//Aumento de existencia
			$Nueva_Existencia=$CantidadDespacha+$datos["Existencia"];
			
		//Ingreso de Nueva Existencia
			
			$query2="update farm_entregamedicamento set Existencia='$Nueva_Existencia'
				where IdMedicina='$IdMedicina' 
                                and IdLote='$IdLoteDespachado'
                                and IdEstablecimiento=$IdEstablecimiento
                                and IdModalidad=$IdModalidad";
			pg_query($query2);
			
		// Eliminacion de movimiento de despacho
			$AnulacionDespacho="delete from farm_medicinadespachada 
					where IdMedicinaDespachada=$IdMedicinaDespachada
                                        and IdEstablecimiento=$IdEstablecimiento
                                        and IdModalidad=$IdModalidad";
			pg_query($AnulacionDespacho);
			
			
		}//Recorrido de farm_medicinadespachada	
		
		
		
	}//aumento de existencias por eliminacion de recetas...
	
	
	
	//***********	Actualizacion de inventario cuando se cambia la Cantidad de medicamento introducido *************
	
	
	
//**************	FIN DE MANEJO DE LOTES Y EXISTENCIAS	**********************************

	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="select DivisorMedicina 
                    from farm_divisores 
                    where IdMedicina=".$IdMedicina."
                    and IdEstablecimiento=$IdEstablecimiento
                    and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
	   return($resp);
    	}

function ObtenerExistencia($IdMedicina,$TipoFarmacia,$Fecha, $IdEstablecimiento,$IdModalidad){
	$SQL="SELECT sum(Existencia) as Existencia
            FROM farm_entregamedicamento fem
            INNER JOIN farm_lotes fl ON fem.IdLote = fl.IdLote
            where IdMedicina=".$IdMedicina."
            and fem.IdEstablecimiento=$IdEstablecimiento
            and fem.IdModalidad=$IdModalidad
            AND left('$Fecha',7) <= left(FechaVencimiento,7)";
	$resp=pg_fetch_array(pg_query($SQL));

	if($row=pg_fetch_array($this->ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad)) and $TipoFarmacia==1){
	   $respuesta=number_format($resp[0]*$row[0],0,'.','');
		
	}else{
	   $respuesta=$resp[0];
	}
	
	return($respuesta);
}

}//Clase RecetasProceso


?>