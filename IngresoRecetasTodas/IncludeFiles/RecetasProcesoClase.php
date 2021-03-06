<?php
require('../../Clases/class.php');

class RecetasProceso{
/*ACTUALIZACION DE HISTORIAL CLINICO*/ //($Expediente,$IdMedico,$IdSubServicio,$FechaConsulta,$IdPersonal,$IdEstablecimiento,$IdModalidad){
	function IntroducirHistorialClinico($Expediente,$IdMedico,$FechaConsulta,$IdPersonal,$IdEstablecimiento){
		
		$Ip=$_SERVER['REMOTE_ADDR'];
		
                /*** A ESTA CONSULTA SE LE QUITARON 2 CAMPOS DE INSERCION 
                   insert into sec_historial_clinico (IdNumeroExp,FechaConsulta,IdEmpleado,IdSubServicioxEstablecimiento,IdUsuarioReg,FechaHoraReg,Piloto,IpAddress,IdEstablecimiento,IdModalidad) 
                              values('$Expediente','$FechaConsulta','$IdMedico','$IdSubServicio','$IdPersonal',now(),'V','$Ip','$IdEstablecimiento',$IdModalidad)";
                
                 ***/            
                
		$queryInsert="insert into sec_historial_clinico (IdNumeroExp,FechaConsulta,IdEmpleado,IdUsuarioReg,FechaHoraReg,Piloto,IpAddress,IdEstablecimiento) 
                                                          values('$Expediente','$FechaConsulta','$IdMedico','$IdPersonal',now(),'V','$Ip','$IdEstablecimiento')";
		     //$queryInsert="insert into sec_historial_clinico (IdNumeroExp,FechaConsulta,IdEmpleado,IdSubServicio,IdSubEspecialidad,IdUsuarioReg,FechaHoraReg,Piloto) values('$Expediente','$FechaConsulta','$IdMedico','$IdSubServicio','$IdSubEspecialidad','$IdPersonal',now(),'V')";
		pg_query($queryInsert);
<<<<<<< HEAD
=======

>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735

/******* EN LA SIGUIENTE QUERYS SE QUITARON 2 CAMPOS QUE GENERABAN ERROR
    and IdSubServicioxEstablecimiento='$IdSubServicio'
    and IdModalidad=$IdModalidad   *********/
                        
		$querySelect="select IdHistorialClinico 
						from sec_historial_clinico
						where IdNumeroExp='$Expediente'
						and IdUsuarioReg='$IdPersonal'
						and FechaConsulta='$FechaConsulta'
						and IdEmpleado='$IdMedico'
				
						and Piloto='V'
                                                and IdEstablecimiento=$IdEstablecimiento
                                                
						order by IdHistorialClinico desc limit 1";
		
		
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);		
	}//Introducir Historial Clinico


/* FIN HISTORIAL CLINICO */

	function Correlativo($Fecha,$IdEstablecimiento,$IdModalidad){
		$querySelect="select farm_recetas.NumeroReceta
					from farm_recetas
					where farm_recetas.Fecha='$Fecha'
					and (farm_recetas.IdEstado<>'RE' and farm_recetas.IdEstado<>'ER')
                                        and IdEstablecimiento=$IdEstablecimiento
                                        and IdModalidad=$IdModalidad
					order by farm_recetas.NumeroReceta desc
					limit 1";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}

	function ObtenerIdReceta($IdHistorialClinico,$IdPersonal){
<<<<<<< HEAD
		$querySelect="select Id
                                from farm_recetas
                                where IdHistorialClinico='$IdHistorialClinico'
                                and IdEstado='E'
                                and IdPersonalIntro='$IdPersonal'";
=======
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='E'
					and IdPersonalIntro='$IdPersonal'";
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
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
	
	
	function IntroducirRecetaNueva($IdHistorialClinico,$IdMedico,$IdPersonal,$Fecha,$IdArea,$IdFarmacia,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad){
		$Correlativo=RecetasProceso::Correlativo($Fecha,$IdEstablecimiento,$IdModalidad);
		$Correlativo++;
		$queryInsert=" insert into farm_recetas(IdHistorialClinico,Fecha,IdEstado,IdArea,NumeroReceta,IdPersonalIntro,IdFarmacia,IdAreaOrigen,IdEstablecimiento,IdModalidad) 
                               values('$IdHistorialClinico','$Fecha','E','$IdArea','$Correlativo','$IdPersonal','$IdFarmacia','$IdAreaOrigen',$IdEstablecimiento,$IdModalidad)";
		pg_query($queryInsert);
	}//Intro Receta Nueva
	
	
		function IntroducirRecetaNuevaRepetitiva($IdHistorialClinico,$IdMedico,$IdPersonal,$Fecha,$IdArea){
		$Correlativo=RecetasProceso::Correlativo($Fecha);
		if($Correlativo==NULL){$Correlativo=0;}
		$Correlativo++;
		$queryInsert=" insert into farm_recetas(IdHistorialClinico,Fecha,IdEstado,IdArea,NumeroReceta,IdPersonalIntro) values('$IdHistorialClinico','$Fecha','RE','$IdArea','$Correlativo','$IdPersonal')";
		pg_query($queryInsert);
	}//Intro Receta Nueva

	
/*	ELIMINAR RECETA E HISTORIA CLINICA	*/	
	
	function EliminarReceta($IdHistorialClinico,$IdPersonal,$IdReceta,$IdArea,$IdEstablecimiento,$IdModalidad){
	   		
	//*****************		ELIMINACION DE RECETA Y DETALLE		
		$queryDeleteReceta="delete from farm_recetas where farm_recetas.IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";

		$queryDeleteMedicina="delete from farm_medicinarecetada where farm_medicinarecetada.IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";

		$queryDeleteHistorialClinico="delete from sec_historial_clinico where sec_historial_clinico.IdHistorialClinico='$IdHistorialClinico' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		
		$querySelect="select * from farm_medicinarecetada 
			      where farm_medicinarecetada.IdReceta='$IdReceta'
                              and IdEstablecimiento=$IdEstablecimiento
                              and IdModalidad=$IdModalidad";
		
		$resp=pg_query($querySelect);
			
		if($row=pg_fetch_array($resp)){
		//Si hay medicina recetada en farm_medicinarecetada
		//Se debe actualizar la existencia del medicamento
			do{
			//recorrido de IdMedicinaRecetada
			    $IdMedicinaRecetada=$row["IdMedicinaRecetada"];
			 
			    $this->AumentarInventario($IdMedicinaRecetada,$IdArea,$IdEstablecimiento,$IdModalidad);
				
				
			}while($row=pg_fetch_array($resp));
			
			
			pg_query($queryDeleteMedicina);
		}		
		pg_query($queryDeleteReceta);


		
		pg_query($queryDeleteHistorialClinico);
				
	}//Eliminar Receta



	function ObtenerIdRecetaRepetitivaEliminar($IdHistorialClinico,$IdPersonal){
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='RE'
					and IdPersonalIntro='$IdPersonal'";
		$resp=pg_query($querySelect);
		return($resp);
			
	}//ObtenerIdRecetaRepetitivaEliminar



/* DETALLE DE RECETA */
//*************************************************************
	function IntroducirMedicinaPorReceta($IdReceta,$IdMedicina,$Cantidad,$Dosis,$Satisfecha,$Fecha,$IdEstablecimiento,$IdModalidad){

		$queryInsert="insert into farm_medicinarecetada(IdReceta,IdMedicina,Cantidad,Dosis,FechaEntrega,IdEstado,IdEstablecimiento,IdModalidad) 
                                                         values('$IdReceta','$IdMedicina','$Cantidad','$Dosis','$Fecha','$Satisfecha',$IdEstablecimiento,$IdModalidad)";
<<<<<<< HEAD
		echo $queryInsert;
                pg_query($queryInsert);
=======
		pg_query($queryInsert);
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735

		$IdMedicinaRecetada=pg_insert_id();
		return($IdMedicinaRecetada);
		
	}//Introducir Medicina Receta


	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="SELECT DivisorMedida 
                 FROM farm_divisores 
                 WHERE IdMedicina=".$IdMedicina." 
                 AND IdEstablecimiento=".$IdEstablecimiento."
                 AND IdModalidad=".$IdModalidad."";
           
	   $resp=pg_query($SQL);
	   return($resp);
    	}
//****************************************************************

	function ObtenerFecha($intervalo){
	$querySelect="select adddate(curdate(), interval +".$intervalo." month)";
	$resp=pg_fetch_array(pg_query($querySelect));
	return($resp[0]);
	}
	
	
	
	function ObtenerMedicinaIntroducida($IdReceta,$IdEstablecimiento,$IdModalidad){
		$querySelect="select farm_medicinarecetada.*,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,Presentacion,FormaFarmaceutica,
                                (
                                    select group_concat(Lote SEPARATOR '-') from farm_lotes fl inner join farm_medicinadespachada fmd on fl.IdLote=fmd.IdLote where fmd.IdMedicinaRecetada=farm_medicinarecetada.IdMedicinaRecetada
                                ) as Lotes
						from farm_medicinarecetada
						inner join farm_catalogoproductos
						on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
						where farm_medicinarecetada.IdReceta = '$IdReceta'
                                                and farm_medicinarecetada.IdEstablecimiento=$IdEstablecimiento
                                                and farm_medicinarecetada.IdModalidad=$IdModalidad
						order by farm_medicinarecetada.IdMedicinaRecetada desc";
		$resp=pg_query($querySelect);
		return($resp);
	}//Obtener Medicina Introducida
	
	
	function RecetaLista($IdReceta){
		$queryUpdate="update farm_recetas set IdEstado='E' where IdReceta='$IdReceta'";
		pg_query($queryUpdate);
	}//Receta Lista


	function ObtenerIdRecetaRepetitiva($IdHistorialClinico,$Fecha){
		$querySelect="select farm_recetas.IdReceta
					from farm_recetas
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdEstado='RE'
					and farm_recetas.Fecha='$Fecha'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//Obtener ID Receta

	function ObtenerRecetaRepetitiva($IdHistorialClinico,$IdPersonal){
		$querySelect="select farm_medicinarecetada.Cantidad,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion,
					farm_recetas.Fecha,farm_medicinarecetada.*
					from farm_recetas
					inner join farm_medicinarecetada
					on farm_medicinarecetada.IdReceta=farm_recetas.IdReceta
					inner join farm_catalogoproductos
					on farm_catalogoproductos.IdMedicina=farm_medicinarecetada.IdMedicina
					where farm_recetas.IdHistorialClinico='$IdHistorialClinico'
					and farm_recetas.IdPersonalIntro='$IdPersonal'
					and farm_recetas.IdEstado='RE'
					order by farm_recetas.Fecha";
		$resp=pg_query($querySelect);
		return($resp);
	
	}

	function EliminarMedicinaRecetada($IdMedicinaRecetada,$IdEstablecimiento,$IdModalidad){
		
		
		$queryDelete="delete from farm_medicinarecetada 
                              where IdMedicinaRecetada='$IdMedicinaRecetada' 
                              and IdEstablecimiento=$IdEstablecimiento
                              and IdModalidad=$IdModalidad";
		pg_query($queryDelete);
		
		
	}//EliminarMedicinaRecetada
	
	

	function UpdateCantidad($IdMedicinaRecetada,$Cantidad,$IdEstablecimiento,$IdModalidad){
		
		$queryUpdate="update farm_medicinarecetada set Cantidad='$Cantidad' 
                              where IdMedicinaRecetada='$IdMedicinaRecetada'
                              and IdEstablecimiento=$IdEstablecimiento
                              and IdModalidad=$IdModalidad";
		pg_query($queryUpdate);

	
	}//UpdateCantidad
	

	
	
//**************	MANEJO DE EXISTENCIAS POR LOTES		**********************************
	
	//Actualizacion de inventario e identificacion de lote utilizado por medicamento
	
	function ActualizarInventario($IdMedicina,$IdMedicinaRecetada,$Cantidad,$IdArea,$Fecha,$IdEstablecimiento,$IdModalidad){
		$queryLote="select fme.IdExistencia,fl.IdLote,Existencia,FechaVencimiento
			from farm_lotes fl
			inner join farm_medicinaexistenciaxarea fme
			on fme.IdLote=fl.IdLote
			where fme.IdMedicina=$IdMedicina
			and Existencia <> 0
			and left('$Fecha',7) <= left(FechaVencimiento,7)
			and fme.IdArea=$IdArea
                        and fme.IdEstablecimiento=$IdEstablecimiento
                        and fme.IdModalidad=$IdModalidad
			order by FechaVencimiento asc,IdExistencia asc";
                
		$lotes=pg_query($queryLote);
                //Se recorren los siguiente lotes... Modo iterativo
                while($lotesA=pg_fetch_array($lotes)){
                        if($Cantidad <= $lotesA["Existencia"]){
                            //****** Si la cantidad de medicamento no exede el total del primer lote a descagar...
                            $IdLote=$lotesA["IdLote"];
                            $existencia_old=$lotesA["Existencia"];
                            $IdExistenciaTabla=$lotesA["IdExistencia"];
                            // se obtiene la nueva exitencia
                            $existencia_new=$existencia_old-$Cantidad;

                            //se actualiza la existencia del lote en uso
                            $actualiza="update farm_medicinaexistenciaxarea set Existencia='$existencia_new' 
                                        where IdExistencia='$IdExistenciaTabla' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
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
                            $IdExistenciaTabla=$lotesA["IdExistencia"];

                            //Medicina que aun falta por despachar
                            $restante2=$Cantidad-$existencia_old;
                            //Se cierra el lote con existencia = 0
                            $actualiza="update farm_medicinaexistenciaxarea set Existencia='0' 
                                        where IdExistencia='$IdExistenciaTabla' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
                            pg_query($actualiza);

                            //se ingresa el lote utilizado
                            $query="insert into farm_medicinadespachada (IdMedicinaRecetada,IdLote,CantidadDespachada,IdEstablecimiento,IdModalidad) 
                                                                  values('$IdMedicinaRecetada','$IdLote','$existencia_old',$IdEstablecimiento,$IdModalidad)";
                            pg_query($query);

                            $Cantidad=$restante2;
                        }//else de la comparacion de restante vs existencia

                }// Recorrido de los demas lotes con existencia
                        
 		return $queryLote;
	}//actualizar inventario
	
	
	
	//MANEJO DE LOTES CUANDO SE ELIMINA UNA RECETA POR CORRECCION
	
	function AumentarInventario($IdMedicinaRecetada,$IdArea,$IdEstablecimiento,$IdModalidad){
		$query="select CantidadDespachada,IdLote,IdMedicinaDespachada 
			from farm_medicinadespachada
			where IdMedicinaRecetada=".$IdMedicinaRecetada." 
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		$resp=pg_query($query);
		
		while($row=pg_fetch_array($resp)){
			$CantidadDespacha=$row["CantidadDespachada"];
			$IdLoteDespachado=$row["IdLote"];
			$IdMedicinaDespachada=$row["IdMedicinaDespachada"];
		
		//Obtencion de existencias actuales del lote utilizado
			$queryExistencia="select Existencia,IdExistencia 
				from farm_medicinaexistenciaxarea fmexa
				where IdArea='$IdArea' and IdLote='$IdLoteDespachado'
                                and fmexa.IdEstablecimiento=$IdEstablecimiento
                                and fmexa.IdModalidad=$IdModalidad
				order by IdExistencia asc";
			$datos=pg_fetch_array(pg_query($queryExistencia));
			
		//Aumento de existencia
			$IdExistenciaTabla=$datos["IdExistencia"];
			$Nueva_Existencia=$CantidadDespacha+$datos["Existencia"];
			
		//Ingreso de Nueva Existencia
			
			$query2="update farm_medicinaexistenciaxarea set Existencia='$Nueva_Existencia'
				where IdExistencia='$IdExistenciaTabla' 
                                and IdEstablecimiento=$IdEstablecimiento
                                and IdModalidad=$IdModalidad";
			pg_query($query2);
			
		// Eliminacion de movimiento de despacho
			$AnulacionDespacho="delete from farm_medicinadespachada 
					where IdMedicinaDespachada=".$IdMedicinaDespachada."
                                        and IdEstablecimiento=$IdEstablecimiento
                                        and IdModalidad=$IdModalidad";
			pg_query($AnulacionDespacho);
			
			
		}//Recorrido de farm_medicinadespachada	
		
		
		
	}//aumento de existencias por eliminacion de recetas...
	
	
	
	//***********	Actualizacion de inventario cuando se cambia la Cantidad de medicamento introducido *************
	
	function ActualizacionInventarioCantidad($IdMedicinaRecetada,$NuevaCantidad,$IdArea,$IdEstablecimiento,$IdModalidad){
	//Obtencion de Cantidad Antigua
	$query="select IdMedicina,Cantidad from farm_medicinarecetada 
                where IdMedicinaRecetada=".$IdMedicinaRecetada."
                and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
	$datos=pg_fetch_array(pg_query($query));
	//Primera parte cuando se aumenta la Cantidad
		if($datos["Cantidad"] < $NuevaCantidad){
			
			$IdMedicina=$datos["IdMedicina"];
			$CantidadAnterior=$datos["Cantidad"];
		    //Calculo de la entrega extra
			$extra=$NuevaCantidad-$CantidadAnterior;
			
		    $this->ActualizarInventario($IdMedicina,$IdMedicinaRecetada,$extra,$IdArea);
                    
		
		}
	//**************************************************
	//Segunda Parte cuando se disminuye la Cantidad
		if($NuevaCantidad < $datos["Cantidad"]){
			$CantidadAnterior=$datos["Cantidad"];
		    //Calculo del medicamento a disminuir y aumentar en la existencia
			$restante=$CantidadAnterior-$NuevaCantidad;
		    //Obtencion de lotes ordenados por mayor existencia
		    //Son lotes que realmente se utilizaron para esa receta introducida...
			
			$queryLotes="select IdMedicinaDespachada, CantidadDespachada, farm_medicinadespachada.IdLote, Existencia
			from farm_medicinadespachada
			inner join farm_lotes
			on farm_lotes.IdLote=farm_medicinadespachada.IdLote
			inner join farm_medicinaexistenciaxarea
			on farm_medicinaexistenciaxarea.IdLote=farm_lotes.IdLote
			
			where IdMedicinaRecetada='$IdMedicinaRecetada'
			
			order by Existencia desc";	
			
			$resp=pg_query($queryLotes);
			
			while($row=pg_fetch_array($resp)){
				
				$IdMedicinaDespachada=$row["IdMedicinaDespachada"];
				
			    if($restante < $row["CantidadDespachada"]){
			    //Cuando la cantidad a disminuir es menor a la despachada por el primer lote
				$IdLote=$row["IdLote"];
				$CantidadDespachada=$row["CantidadDespachada"];
			    //Calculo de restante [Medicina realmente despachada]
				$restante2=$CantidadDespachada-$restante;
				
			    //Disminucion de la medicina despachada en farm_medicinadespachada
				$actualizaDespacho="update farm_medicinadespachada 
						set CantidadDespachada='$restante2'
						where IdMedicinaDespachada='$IdMedicinaDespachada'";
				pg_query($actualizaDespacho);
				
			    //Aumento de existencias en lote utilizado por el moviemitno anterior
				$ExistenciaAnterior=$row["Existencia"];
				$ExistenciaNueva=$ExistenciaAnterior+$restante;
				
			    //Actualizacion de Existencia
				$actualizaExistencia="update farm_medicinaexistenciaxarea
						set Existencia='$ExistenciaNueva'
						where IdArea='$IdArea' and IdLote='$IdLote'";
				pg_query($actualizaExistencia);
				
				break;
			    }else{
			    //Cuando la cantidad a disminuir es mayor a la cantidad utilizada 
			    //por el primer Lote. Se debe eliminar el movimiento descrito por la receta
			    //en farm_medicinadespachada
				$IdLote=$row["IdLote"];
				$CantidadDespachada=$row["CantidadDespachada"];
				$restante2=$restante-$CantidadDespachada;
				$prueba=1;
					if($restante2==0){
					   //En dado caso sea exacto al movimiento del lote el restante2 sera = restante
						$prueba=0;
						$restante2=$restante;
					}
			    //Aumento de la existencia del lote utilizado
				$ExistenciaAnterior=$row["Existencia"];
				$ExistenciaNueva=$ExistenciaAnterior+$CantidadDespachada;
				
			    //Actualizacion de la existencia del lote en cuestion
				$actualizaExistencia="update farm_medicinaexistenciaxarea
						set Existencia='$ExistenciaNueva'
						where IdArea='$IdArea' and IdLote='$IdLote'";	
				pg_query($actualizaExistencia);
				
			    //Eliminacion del movimiento de farm_medicinadespachada
				$eliminaMovimiento="delete from farm_medicinadespachada where IdMedicinaDespachada=".$IdMedicinaDespachada;
				pg_query($eliminaMovimiento);
				
				if($prueba==0){
					break;
				}else{
					$restante=$restante2;
				}
			    }
			
			}//recorrido de lotes
			
		}
	//***************************************************
	}//actualizacion de inventario por cambio de cantidad
	
	
	
//**************	FIN DE MANEJO DE LOTES Y EXISTENCIAS	**********************************
	
		function ObtenerCodigoFarmacia($IdMedico){
		$querySelect="SELECT codigo_farmacia
					FROM mnt_empleado
					WHERE id='$IdMedico'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp[0]);
	}//CodigoFarmacia
	
	
	function ObtenerDatosMedico($CodigoFarmacia){
		$querySelect="select mnt_empleados.IdEmpleado, mnt_empleados.NombreEmpleado
					from mnt_empleados
					where mnt_empleados.CodigoFarmacia='$CodigoFarmacia'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp);
	}//ObtenerDatosMedico
	
	/*	ACUTALIZACION DE INFORMACION	*/
	function ActualizarArea($IdArea,$IdReceta,$IdFarmacia,$Fecha,$IdEstablecimiento,$IdModalidad){
	 //MANEJO DE EXISTENCIAS EN CASO DE CAMBIO DE AREA
		$SQL="select IdMedicinaRecetada,IdArea,Cantidad,IdMedicina
			from farm_medicinarecetada
			inner join farm_recetas
			on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
			where farm_medicinarecetada.IdReceta=".$IdReceta." 
                        and farm_recetas.IdEstablecimiento=$IdEstablecimientocombo
                        and farm_recetas.IdModalidad=$IdModalidad";
		$resp=pg_query($SQL);
		
		while($row=pg_fetch_array($resp)){
		   
			//se elimina el medicamento despachado de la area anterior y se aumentan existencias
			$this->AumentarInventario($row["IdMedicinaRecetada"],$row["IdArea"],$IdEstablecimiento,$IdModalidad);
			
			//se le atribuye a la futura area el cargo del medicamento a cambiar de area
			$this->ActualizarInventario($row["IdMedicina"],$row["IdMedicinaRecetada"],$row["Cantidad"],$IdArea,$Fecha,$IdEstablecimiento,$IdModalidad);
		   
		}
		

	    //**************************************************

		$queryH="select IdHistorialClinico from farm_recetas where IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		$respH=pg_fetch_array(pg_query($queryH));
			$IdNumeroExp='0'.$IdFarmacia;
		    $SQL="update sec_historial_clinico set IdNumeroExp='$IdNumeroExp' where IdHistorialClinico=".$respH[0]." and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
			pg_query($SQL);
		$query="update farm_recetas set IdArea='$IdArea',IdAreaOrigen='$IdArea',IdFarmacia='$IdFarmacia' where IdReceta='$IdReceta' and IdEstablecimiento=$IdEstablecimiento and IdModalidad=$IdModalidad";
		pg_query($query);
		$query2="select Area combo
                         from mnt_areafarmacia 
                         inner join mnt_areafarmaciaxestablecimiento maxe
                         on maxe.IdArea=mnt_areafarmacia.IdArea
                         where maxe.IdArea='$IdArea' and maxe.IdEstablecimiento=$IdEstablecimiento
                        and maxe.IdModalidad=$IdModalidad";
		$resp=pg_fetch_array(pg_query($query2));
		
		$query3="select Farmacia 
                         from mnt_farmacia 
                         inner join mnt_farmaciaxestablecimiento mfe
                         on mfe.IdFarmacia=mnt_farmacia.IdFarmacia
                         where mfe.IdFarmacia=".$IdFarmacia." 
                         and IdEstablecimiento = $IdEstablecimiento
                         and IdModalidad=$IdModalidad";
		$resp2=pg_fetch_array(pg_query($query3));

	}//actualizaicon de area
	
	function ActualizarMedico($IdHistorialClinico,$IdMedico,$IdEstablecimiento,$IdModalidad){
		$query="update sec_historial_clinico set IdEmpleado='$IdMedico' 
                        where IdHistorialClinico='$IdHistorialClinico' 
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);
	}
	
	function ActualizarSubServicio($IdHistorialClinico,$IdSubServicio,$IdEstablecimiento,$IdModalidad){
				
		$query="update sec_historial_clinico set IdSubServicioxEstablecimiento='$IdSubServicio' 
                        where IdHistorialClinico='$IdHistorialClinico' 
                        and IdEstablecimiento=$IdEstablecimiento
                        and IdModalidad=$IdModalidad";
		pg_query($query);	
		
	}
	
	function VerificaRecetas($IdReceta){
<<<<<<< HEAD
		$query="select Id from farm_medicinarecetada where IdReceta= ".$IdReceta;
=======
		$query="select IdMedicinaRecetada from farm_medicinarecetada where IdReceta=".$IdReceta;
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);		
	}//verifica recetas

	
	function Cierre($Fecha,$IdEstablecimiento,$IdModalidad){
<<<<<<< HEAD
		$sql="SELECT AnoCierre
                      FROM farm_cierre
                      WHERE  AnoCierre=substring('".$Fecha."',1,4)::int
                      AND IdEstablecimiento=".$IdEstablecimiento."
                      AND IdModalidad=$IdModalidad";
=======
		$sql="select AnoCierre
			from farm_cierre
			where AnoCierre=year('".$Fecha."')
                        and IdEstablecimiento=".$IdEstablecimiento."
                        and IdModalidad=$IdModalidad";
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
		$resp=pg_query($sql);
		return($resp);		
	}//Cierre
	

	function CierreMes($Fecha,$IdEstablecimiento,$IdModalidad){
<<<<<<< HEAD
		$sql="SELECT MesCierre
                      FROM farm_cierre
                      WHERE MesCierre=substring('$Fecha',1,7)
                      AND IdEstablecimiento=".$IdEstablecimiento." 
                      AND IdModalidad=$IdModalidad";
=======
		$sql="select MesCierre
			from farm_cierre
			where MesCierre=left('$Fecha',7)
                        and IdEstablecimiento=".$IdEstablecimiento." 
                        and IdModalidad=$IdModalidad";
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
		$resp=pg_query($sql);
		return($resp);		
	}//CierreMes

   function ObtenerExistencia($IdMedicina,$IdArea,$Fecha,$IdEstablecimiento,$IdModalidad){
	$SQL="SELECT sum(Existencia) as Existencia
 	FROM farm_medicinaexistenciaxarea fmea
        INNER JOIN farm_lotes fl ON fmea.IdLote = fl.IdLote
	WHERE IdMedicina=".$IdMedicina."
	AND IdArea=".$IdArea."
        AND fmea.IdEstablecimiento=".$IdEstablecimiento."
        AND fmea.IdModalidad = ".$IdModalidad."
        AND left('$Fecha',7) <= left(FechaVencimiento,7)";
        //se agrego el inner join para validar la fecha y el and de modalidades.
        
	$resp=pg_fetch_array(pg_query($SQL));
	if($resp[0]!='' and $resp[0]!=NULL)
            {
                if($row=pg_fetch_array($this->ValorDivisor($IdMedicina,$IdEstablecimiento)))
                {
		$respuesta=number_format($resp[0]*$row[0],0,'.','');
		}else{
                    $respuesta=$resp[0];
		}
            }else{
                $respuesta=0;
            }
	return($respuesta);
   }	


   function AreaOrigen($IdArea,$IdEstablecimiento){
	$SQL="SELECT DISTINCT mafe.IdArea, Area 
              FROM mnt_areafarmacia 
              INNER JOIN mnt_areafarmaciaxestablecimiento mafe
              ON mafe.IdArea=mnt_areafarmacia.Id
              WHERE mafe.Habilitado='S' and mafe.IdArea not in(12,7,".$IdArea.")
              AND mafe.IdEstablecimiento=".$IdEstablecimiento;
	$resp=pg_query($SQL);
	$combo="<select id='IdAreaOrigen' name='IdAreaOrigen'>
		<option value='0'>[Opcional ...]</option>";
	while($row=pg_fetch_array($resp, null, PGSQL_ASSOC)){
	   $combo.="<option value='".$row["idarea"]."'>".$row["area"]."</option>";
	}
	$combo.="</select>";
	return($combo);
   }

    function ActualizarAreaOrigen($IdArea,$IdReceta){

		$query="UPDATE farm_recetas SET IdAreaOrigen='$IdArea' WHERE Id='$IdReceta'";
		pg_query($query);

   }



	function RecetasIngresadasConteo($IdPersonal,$IdEstablecimiento,$IdModalidad){
		$query="select count(IdMedicinaRecetada)
				from farm_usuarios
				inner join farm_recetas
				on farm_recetas.IdPersonalIntro=farm_usuarios.IdPersonal
							
				inner join farm_medicinarecetada
				on farm_recetas.IdReceta=farm_medicinarecetada.IdReceta
				inner join sec_historial_clinico
				on sec_historial_clinico.IdHistorialClinico=farm_recetas.IdHistorialClinico
				
				where FechaHoraReg is not null
				and date(FechaHoraReg)=curdate()
				and IdPersonalIntro='$IdPersonal'
                                and farm_usuarios.IdEstablecimiento=$IdEstablecimiento
                                and farm_recetas.IdModalidad=$IdModalidad
group by farm_recetas.IdPersonalIntro
order by farm_recetas.IdPersonalIntro";
		$resp=pg_fetch_array(pg_query($query));
		return($resp[0]);
	}//Informacion


	function ObtenerCorrelativoAnual($IdReceta,$Fecha,$IdEstablecimiento,$IdModalidad){
	
	$datos=array('A'=>'01','B'=>'02','C'=>'03','D'=>'04','E'=>'05','F'=>'06','G'=>'07','H'=>'08','I'=>'09','J'=>'10','K'=>'11','L'=>'12',);

	$date=explode('-',$Fecha);
	$mes=$date[1];
	$Letra= array_search($mes,$datos);	
		

	   $SQL="select Correlativo
		from farm_recetas
                inner join sec_historial_clinico shc
                on shc.IdHistorialClinico=farm_recetas.IdHistorialClinico
		where CorrelativoAnual like '%$Letra%'
		and EXTRACT(YEAR FROM Fecha)=EXTRACT(YEAR FROM '$Fecha'::DATE)
                and shc.IdEstablecimiento=$IdEstablecimiento
                and farm_recetas.IdModalidad=$IdModalidad
		order by Correlativo desc";
	   $resp=pg_fetch_array(pg_query($SQL));
		
           $tail="select right('".$date[0]."',2) as tail";
            $tt=pg_fetch_array(pg_query($tail));
           
		$correlativo=$resp[0]+1;
		
		$correlativoAnual=$correlativo."".$Letra."".$tt[0];
		
<<<<<<< HEAD
		$SQL_u="update farm_recetas set Correlativo='$correlativo', CorrelativoAnual='$correlativoAnual' where Id=".$IdReceta;
=======
		$SQL_u="update farm_recetas set Correlativo='$correlativo', CorrelativoAnual='$correlativoAnual' where IdReceta=".$IdReceta;
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
			pg_query($SQL_u);
		return($correlativoAnual);
	}

}//Clase RecetasProceso


?>