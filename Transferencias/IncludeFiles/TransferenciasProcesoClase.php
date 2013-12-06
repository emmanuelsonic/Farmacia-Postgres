<?php
require('../../Clases/class.php');

class TransferenciaProceso{
/*INTRODUCCION DE NUEVA TRANSFERENCIA*/

	function ObtenerExistencia($Lote,$Bandera,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad){
	/*Se usa para obtener su existencia y para obtener el codigo de lote[despliegue del detalle]*/
		$querySelect="select farm_medicinaexistenciaxarea.id as IdExistencia,farm_medicinaexistenciaxarea.Existencia,farm_lotes.Lote
					from farm_medicinaexistenciaxarea
					inner join farm_lotes
					on farm_lotes.Id=farm_medicinaexistenciaxarea.IdLote
					where farm_lotes.Id='$Lote'
					and IdArea=".$IdAreaOrigen." 
                                        and Existencia <> 0
                                        and farm_medicinaexistenciaxarea.IdEstablecimiento=".$IdEstablecimiento." 
                                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad";
		$resp=pg_fetch_array(pg_query($querySelect));
		if($Bandera==1){return($resp);}else{return($resp);}
	}//ObtenerExistencia
        
        
        function ObtenerExistencia2($Lote,$Bandera,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad){
	/*Se usa para obtener su existencia y para obtener el codigo de lote[despliegue del detalle]*/
		$querySelect="select IdExistencia,farm_medicinaexistenciaxarea.Existencia,farm_lotes.Lote
					from farm_medicinaexistenciaxarea
					inner join farm_lotes
					on farm_lotes.Id=farm_medicinaexistenciaxarea.IdLote
					where farm_lotes.Id='$Lote'
					and IdArea=".$IdAreaOrigen." 
                                        and farm_medicinaexistenciaxarea.IdEstablecimiento=".$IdEstablecimiento." 
                                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp);
	}//ObtenerExistencia
        

	function ObtenerSiguienteLote($IdMedicina,$Lote,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad){
		$querySelect="select farm_lotes.Id, Existencia,IdExistencia
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.Id
					where farm_lotes.Id <> '$Lote'
					and farm_medicinaexistenciaxarea.IdMedicina='$IdMedicina'
					and IdArea=".$IdAreaOrigen."
                                            and Existencia <> 0
                                        and farm_medicinaexistenciaxarea.IdEstablecimiento=".$IdEstablecimiento."
					and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
                                        order by FechaVencimiento asc";

		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp);	
	}
	
	function IntroducirTransferencia($Cantidad,$IdMedicina,$IdAreaOrigen,$IdAreaDestino,$Justificacion,$FechaTransferencia,$IdPersonal,$Lote,$Divisor,$UnidadesContenidas,$IdEstablecimiento,$IdModalidad){
	/*CONTROL DE EXISTENCIA EN DADO CASO EL LOTE SELECCIONADO NO SUPLA LA CANTIDAD ENTERA SE DESCUENTA DEL SIGUIENTE LOTE*/
$Bandera=true;
while($Bandera){
		$Existencia=TransferenciaProceso::ObtenerExistencia($Lote,1,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad);
                    $Existencia2=TransferenciaProceso::ObtenerExistencia($Lote,1,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad);
		$Existencia=$Existencia["existencia"]*$Divisor;
		$Cantidad=$Cantidad*$UnidadesContenidas;
		
		$falta=0;
		
	if($Existencia <= $Cantidad){
		//Si se necesita mas de un lote para suplir la transferencia
		$Cantidad2=$Cantidad-$Existencia;//restante a suplir
		$Cantidad1=($Existencia/$Divisor);
                        
		//Primera transferencia del lote agotado...
		$queryInsert="insert into farm_transferencias(Cantidad,IdMedicina,IdLote,IdAreaOrigen,IdAreaDestino,Justificacion,FechaTransferencia,IdPersonal,IdEstado,IdEstablecimiento,IdModalidad) 
                                                       values('$Cantidad1','$IdMedicina','$Lote','$IdAreaOrigen','$IdAreaDestino','$Justificacion','$FechaTransferencia','$IdPersonal','X',$IdEstablecimiento,$IdModalidad) RETURNING id";
			//pg_query($queryInsert);
                        $result=pg_query($queryInsert);
                        $insert_row = pg_fetch_row($result);
                        $IdTransferenciasN= $insert_row[0];
			//$IdTransferenciasN=pg_insert_id();
                        var_dump($IdTransferenciasN);
                        die("bananero");
		//SIL A AREA ES DIFERENTE DE CERO ES DECIR SI ES UNA TRANFERENCIA ENTRE FARMACIAS
		if($IdAreaDestino!=0 and $Cantidad1!=0){
		   $ver=TransferenciaProceso::ObtenerExistencia2($Lote,1,$IdAreaDestino,$IdEstablecimiento,$IdModalidad);
                        $IdExistenciaDestino=$ver["idexistencia"];
		   if($IdExistenciaDestino==NULL or $IdExistenciaDestino==""){
		   //NO EXISTE INFORMACION DE ESTE LOTE NI DEL MEDICAMENTO EN CUESTION	
                        
			$SQL="insert into farm_medicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,IdEstablecimiento,IdModalidad) 
                                                                 values('$IdMedicina','$IdAreaDestino','$Cantidad1','$Lote',$IdEstablecimiento,$IdModalidad)";
			pg_query($SQL);
			$SQL2="insert into farm_bitacoramedicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,FechaHoraIngreso,IdPersonal,IdTransferencia,IdEstablecimiento,IdModalidad)
                                                                          values('$IdMedicina','$IdAreaDestino','$Cantidad1','$Lote',now(),'$IdPersonal','$IdTransferenciasN',$IdEstablecimiento,$IdModalidad)";
			pg_query($SQL2);
		   }
                   
                   
                   $ExistenciaDestino=number_format($ver["Existencia"],0,'.','');
		   if(($ExistenciaDestino==0 or $ExistenciaDestino!=0) and $ExistenciaDestino!=NULL and $ExistenciaDestino!=""){
		   //SI EXISTE INFORMACION DEL LOTE PERO EL MEDICAMENTO ESTA COMPLETAMENTE AGOTADO O APUNTO DE...
                                              
			$Cantidad_nueva=$Cantidad1+$ExistenciaDestino;
                        
			$SQL="update farm_medicinaexistenciaxarea set Existencia='$Cantidad_nueva' 
                              where IdMedicina='$IdMedicina' and IdArea='$IdAreaDestino' 
                              and IdLote='$Lote' and IdExistencia=".$ver["IdExistencia"]."
                              ";
                        pg_query($SQL);

			$SQL2="insert into farm_bitacoramedicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,FechaHoraIngreso,IdPersonal,IdTransferencia,IdEstablecimiento,IdModalidad) 
                                                                          values('$IdMedicina','$IdAreaDestino','$Cantidad1','$Lote',now(),'$IdPersonal','$IdTransferenciasN',$IdEstablecimiento,$IdModalidad)";
			pg_query($SQL2);
		   }

		}
		//*******************************************************************************


		$SQL="update farm_medicinaexistenciaxarea set Existencia = '0' 
                      where IdMedicina='$IdMedicina' and IdLote='$Lote' and IdArea='$IdAreaOrigen' and IdExistencia=".$Existencia2["IdExistencia"];
			pg_query($SQL);

		$respLote2=TransferenciaProceso::ObtenerSiguienteLote($IdMedicina,$Lote,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad);
		$Lote=$respLote2[0];
		$Cantidad=($Cantidad2/$UnidadesContenidas);
			if($Lote==NULL or $Lote==''){$Bandera=1;$falta=$Cantidad;}

                        
		if($Cantidad==0){$Bandera=1;}
                
                

	}else{
            
                
            
		$Cantidad1=($Cantidad/$Divisor);
		$queryInsert="insert into farm_transferencias(Cantidad,IdMedicina,IdLote,IdAreaOrigen,IdAreaDestino,Justificacion,FechaTransferencia,IdPersonal,IdEstado,IdEstablecimiento,IdModalidad) 
                                                       values('$Cantidad1','$IdMedicina','$Lote','$IdAreaOrigen','$IdAreaDestino','$Justificacion','$FechaTransferencia','$IdPersonal','X',$IdEstablecimiento,$IdModalidad)";
		pg_query($queryInsert);
			

			$IdTransferenciasN=pg_insert_id();

		//SIL A AREA ES DIFERENTE DE CERO ES DECIR SI ES UNA TRANFERENCIA ENTRE FARMACIAS
		if($IdAreaDestino!=0){
		   $ver=TransferenciaProceso::ObtenerExistencia2($Lote,1,$IdAreaDestino,$IdEstablecimiento,$IdModalidad);
		   $IdExistenciaDestino=$ver["IdExistencia"];
		   if($IdExistenciaDestino==NULL or $IdExistenciaDestino==""){
		   //NO EXISTE INFORMACION DE ESTE LOTE NI DEL MEDICAMENTO EN CUESTION	
			$SQL="insert into farm_medicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,IdEstablecimiento,IdModalidad) 
                                                                 values('$IdMedicina','$IdAreaDestino','$Cantidad1','$Lote',$IdEstablecimiento,$IdModalidad)";
			pg_query($SQL);
			$SQL2="insert into farm_bitacoramedicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,FechaHoraIngreso,IdPersonal,IdTransferencia,IdEstablecimiento,IdModalidad) 
                                                                          values('$IdMedicina','$IdAreaDestino','$Cantidad1','$Lote',now(),'$IdPersonal','$IdTransferenciasN',$IdEstablecimiento,$IdModalidad)";
			pg_query($SQL2);
		   }
                   
		   $ExistenciaDestino=number_format($ver["Existencia"],0,'.','');
		   if(($ExistenciaDestino==0 or $ExistenciaDestino!=0) and $ExistenciaDestino!=NULL and $ExistenciaDestino!=""){
		   //SI EXISTE INFORMACION DEL LOTE PERO EL MEDICAMENTO ESTA COMPLETAMENTE AGOTADO O APUNTO DE...
			$Cantidad_nueva=$Cantidad1+$ExistenciaDestino;
			$SQL="update farm_medicinaexistenciaxarea set Existencia='$Cantidad_nueva' 
                              where IdMedicina='$IdMedicina' and IdArea='$IdAreaDestino' and IdLote='$Lote' and IdExistencia=".$ver["IdExistencia"];
			pg_query($SQL);
			$SQL2="insert into farm_bitacoramedicinaexistenciaxarea (IdMedicina,IdArea,Existencia,IdLote,FechaHoraIngreso,IdPersonal,IdTransferencia,IdEstablecimiento,IdModalidad) 
                                                                          values('$IdMedicina','$IdAreaDestino','$Cantidad1','$Lote',now(),'$IdPersonal','$IdTransferenciasN',$IdEstablecimiento,$IdModalidad)";
			pg_query($SQL2);
		   }

		}
		//*******************************************************************************

		$Existencia_new=($Existencia2["Existencia"]*$Divisor)-($Cantidad1*$Divisor);//Existencia remanente despues de transferencia
                        $Existencia_new=$Existencia_new/$Divisor;
		$SQL="update farm_medicinaexistenciaxarea set Existencia = '$Existencia_new' 
                      where IdMedicina='$IdMedicina' and IdLote='$Lote' and IdArea='$IdAreaOrigen' and IdExistencia=".$Existencia2["IdExistencia"];
			pg_query($SQL);

	      	$Bandera=false;
		$falta=0;
                
                
	}

}
return($falta);
	/**********************************************/

		
	}//Introducir Transferencia



	function ObtenerTransferencias($IdPersonal,$Fecha){
	/*OBTENCION DE INFORMES INTRODUCIDOS POR EL USUARIO SIN SER FINALIZADOS*/
		$querySelect="select farm_transferencias.Cantidad,farm_catalogoproductos.Nombre,farm_catalogoproductos.Concentracion, Presentacion,Descripcion, 
					mnt_areafarmacia.Area,farm_transferencias.Justificacion,farm_transferencias.IdAreaDestino,
					farm_transferencias.id as IdTransferencia,farm_lotes.Lote,farm_catalogoproductos.Id as idmedicina
					from farm_transferencias
					inner join farm_catalogoproductos
					on farm_catalogoproductos.Id=farm_transferencias.IdMedicina
					inner join mnt_areafarmacia
					on mnt_areafarmacia.id=farm_transferencias.IdAreaOrigen
					inner join farm_lotes
					on farm_lotes.Id=farm_transferencias.IdLote
					inner join farm_unidadmedidas fum
					on fum.Id=farm_catalogoproductos.IdUnidadMedida
					where farm_transferencias.IdPersonal='$IdPersonal'
					and farm_transferencias.FechaTransferencia = '$Fecha'
					and farm_transferencias.IdEstado='X'";
		$resp=pg_query($querySelect);
		return($resp);	
	}//Obtener transferencias



	function NombreArea($IdArea){
		$querySelect="select mnt_areafarmacia.Area
					from mnt_areafarmacia
					where mnt_areafarmacia.id='$IdArea'";
		if($resp=pg_fetch_array(pg_query($querySelect))){
			return($resp[0]);
		}else{
			return("Otras Areas");
		}
	}

	
/* ELIMINAR */	
	
	function EliminarTransferencia($IdTransferencia,$IdModalidad){

		$SQL="select * from farm_transferencias where Id=".$IdTransferencia;
		$row=pg_fetch_array(pg_query($SQL));

		$IdMedicina=$row["idmedicina"];
		$Cantidad=$row["cantidad"];
		$IdLote=$row["idlote"];
		$IdAreaOrigen=$row["idareaorigen"];
		$IdAreaDestino=$row["idareadestino"];
		$IdPersonal=$row["idpersonal"];
                
                $IdEstablecimiento=$row["idestablecimiento"];

		if($IdAreaDestino!=0){
			$SQL1="select * from farm_medicinaexistenciaxarea 
                               where IdArea=$IdAreaDestino 
                               and IdMedicina=$IdMedicina and IdLote=$IdLote and Existencia <> 0
                               and IdEstablecimiento=".$IdEstablecimiento." 
                               and IdModalidad=$IdModalidad";
                        
			$respDestino=pg_fetch_array(pg_query($SQL1));
			
			$Existencia_Actual_Destino=$respDestino["existencia"];
				$IdExistenciaDestino=$respDestino["id"];
			if($Existencia_Actual_Destino!=0){
			   $Existencia_Nueva_Destino=$Existencia_Actual_Destino-$Cantidad;
				$SQL4="update farm_medicinaexistenciaxarea set Existencia='$Existencia_Nueva_Destino' where Id=".$IdExistenciaDestino;
				pg_query($SQL4);
			}
			
			
		}


		$SQL2="select * 
			from farm_medicinaexistenciaxarea fmexa
			inner join farm_lotes fl
			on  fl.id = fmexa.IdLote
			
			where IdMedicina='$IdMedicina'
			and  fl.id='$IdLote'
			and IdArea=".$IdAreaOrigen."
                        and fmexa.IdEstablecimiento=".$IdEstablecimiento." 
                        and fmexa.IdModalidad=$IdModalidad";

		$resp=pg_fetch_array(pg_query($SQL2));
		
                $Divisor=$this->ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad);
                    if($valorDivisor=pg_fetch_array($Divisor)){
                        $TDivisor=$valorDivisor[0];
                    }else{
                        $TDivisor=1;
                    }
                
                //Transformacion de medicamentos
		$ExistenciaActual=$resp["existencia"]*$TDivisor;
               
                $Cantidad=$Cantidad*$TDivisor;
                // ******************************************
                
		  $Existencia_new=$ExistenciaActual+$Cantidad;
                  
                        $Existencia_new=$Existencia_new/$TDivisor; //Se regresa a unidad original
		
		$SQL3="update farm_medicinaexistenciaxarea set Existencia='$Existencia_new' 
                       where Id=".$resp["id"];
		   pg_query($SQL3);


		$querySelect="delete from farm_transferencias 
                              where Id='$IdTransferencia'";
		pg_query($querySelect);

		$SQL33="delete from farm_bitacoramedicinaexistenciaxarea 
                        where IdTransferencia='$IdTransferencia'";
		pg_query($SQL33);

                return($Cantidad."~".$Existencia_new);
	}//ObtenerIdRecetaRepetitivaEliminar


/*FINALIZA TODAS LAS TRANSFERENCIAS*/	
	function FinalizaTransferencia($IdPersonal){
		$queryUpdate="update farm_transferencias set IdEstado='D' where IdPersonal='$IdPersonal' and IdEstado='X'";
		pg_query($queryUpdate);
	}//Receta Lista


	function ObtenerCantidadMedicina($IdPersonal){
		$querySelect="select farm_transferencias.Cantidad1,farm_transferencias.Cantidad2,farm_transferencias.IdMedicina,
				farm_transferencias.IdAreaOrigen as IdArea,farm_transferencias.IdLote,farm_transferencias.IdLote2
				from farm_transferencias
				where farm_transferencias.FechaTransferencia=curdate()
				and farm_transferencias.IdEstado='X'
				and farm_transferencias.IdPersonal='$IdPersonal'";
		$resp=pg_query($querySelect);
		return($resp);
	}//ObtenerCantidadMedicina


	function ObtenerLotesMedicamento($IdMedicina,$Cantidad,$IdAreaOrigen,$IdEstablecimiento,$IdModalidad){
		$querySelect="select sum(Existencia),farm_lotes.id as IdLote,
								case 
								when (substr(to_char(farm_lotes.FechaVencimiento + '1 month'::interval, 'YYYY-MM-DD'), 1,7) < 
								substr(to_char(current_date + '1 month'::interval, 'YYYY-MM-DD'), 1,7)) 
								then farm_lotes.lote||' [Lote Vencido]'
								else
								farm_lotes.Lote end as Lote, 
                                farm_lotes.FechaVencimiento
					from farm_lotes
					inner join farm_medicinaexistenciaxarea
					on farm_medicinaexistenciaxarea.IdLote=farm_lotes.Id
					where farm_medicinaexistenciaxarea.IdMedicina='$IdMedicina'
					and farm_medicinaexistenciaxarea.Existencia <> 0	
					and IdArea=".$IdAreaOrigen."
					and farm_medicinaexistenciaxarea.IdEstablecimiento=".$IdEstablecimiento."
                                        and farm_medicinaexistenciaxarea.IdModalidad=$IdModalidad
					group by farm_lotes.Id
					order by farm_lotes.FechaVencimiento";
		$resp=pg_query($querySelect);
		return($resp);
	}//ObtenerLotesMedicamento


	function ObtenerDetalleLote($IdTransferencia){
		$querySelect="select Cantidad, Lote,  fl.id as idlote
				from farm_transferencias ft
				inner join farm_lotes fl
				on  fl.id = ft.IdLote
					where ft.Id='$IdTransferencia'";
		$resp=pg_fetch_array(pg_query($querySelect));
		return($resp);
	}//ObtenerDetalleLote

	function ValorDivisor($IdMedicina,$IdEstablecimiento,$IdModalidad){
	   $SQL="select DivisorMedicina from farm_divisores 
                 where IdMedicina=".$IdMedicina." 
                 and IdEstablecimiento=$IdEstablecimiento
                 and IdModalidad=$IdModalidad";
	   $resp=pg_query($SQL);
	   return($resp);
    	}
	
	function UnidadesContenidas($IdMedicina){
	  $SQL="select UnidadesContenidas,Descripcion
		from farm_unidadmedidas fu
		inner join farm_catalogoproductos fcp
		on fcp.IdUnidadMedida = fu.Id
		where fcp.Id=".$IdMedicina;
	  $resp=pg_fetch_array(pg_query($SQL));
	  return($resp[0]);
	}


}//Clase RecetasProceso


?>