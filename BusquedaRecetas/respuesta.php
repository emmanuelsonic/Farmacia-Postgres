<?php session_start();
include('../Clases/class.php');
conexion::conectar();
$Bandera=$_GET['Bandera'];
$Busqueda=$_GET['q'];
echo $Busqueda;

switch($Bandera){
    case 1:// busqueda de medicamentos
        
            $IdArea=$_GET["IdAreaActual"];


            $querySelect="SELECT DISTINCT Codigo, Nombre, Concentracion, fcp.Id, FormaFarmaceutica, Presentacion
                            FROM farm_catalogoproductos AS fcp
                            INNER JOIN farm_catalogoproductosxestablecimiento fcpe ON fcpe.IdMedicina=fcp.Id
                            INNER JOIN farm_medicinaexistenciaxarea fmexa ON fmexa.IdMedicina=fcpe.IdMedicina

                            WHERE (Nombre like '%$Busqueda%' or Codigo ='$Busqueda')
                            AND IdArea='$IdArea'
                            AND fcpe.IdEstablecimiento=".$_SESSION["IdEstablecimiento"]."
                            AND fcpe.IdModalidad=".$_SESSION["IdModalidad"]."
                            AND Condicion = 'H'
                            AND IdTerapeutico IS NOT NULL
                            ORDER BY fcp.Id";
                       
           $resp=pg_query($querySelect);
            while($row=pg_fetch_array($resp)){
                    $Nombre=$row["nombre"]." - ".$row["concentracion"]." - ".$row["formafarmaceutica"]." - ".$row["presentacion"];
                    $IdMedicina=$row["id"];
                    $Codigo=$row["codigo"];
            ?>
            <li onselect="this.text.value = '<?php echo htmlentities($Nombre);?>';$('IdMedicina').value='<?php echo $IdMedicina;?>';ObtenerExistenciaTotal();"> 
                    <span><?php echo $Codigo;?></span>
                    <strong><?php echo htmlentities($Nombre);?></strong>
            </li>
            <?php
            } // fin del while
    break;
    
    case 2:// busqueda de pacientes
            $querySelect="SELECT mnt_paciente.id, primer_nombre||' '||segundo_nombre||' '||(case when tercer_nombre!='' then tercer_nombre else '' end )||' '||primer_apellido||' '||segundo_apellido as NombrePaciente, numero
                            FROM mnt_paciente
                            INNER JOIN mnt_expediente ON mnt_expediente.id_paciente=mnt_paciente.id
                            WHERE (primer_nombre||' '||segundo_nombre||' '||(case when tercer_nombre!='' then tercer_nombre else '' end )||' '||primer_apellido||' 	'||segundo_apellido)  like '%$Busqueda%' or numero = '$Busqueda'
                            limit 100";

            $resp=pg_query($querySelect);
            while($row=pg_fetch_array($resp)){
                $NombrePaciente=$row["nombrepaciente"];
                //$IdPaciente=$row["IdPaciente"];
                $IdNumeroExp=$row["numero"];
?>
                <li onselect="this.text.value = '<?php echo htmlentities($NombrePaciente);?>';$('Expediente').value='<?php echo $IdNumeroExp;?>';$('Expediente').innerHTML='<?php echo $IdNumeroExp;?>';$('Nombre').innerHTML='<?php echo htmlentities($NombrePaciente);?>';">        
                    <strong><?php echo htmlentities($NombrePaciente)."   [ ".htmlentities($IdNumeroExp)." ]";?></strong>
                </li>
<?php       }// fin del while
    break; // fin case 2 busqueda de pacientes  
}// fin switch    
conexion::desconectar();
?>