<?php
session_start();
include('../Clases/class.php');
conexion::conectar();
$Bandera=$_GET['Bandera'];
$Busqueda = $_GET['q'];

switch($Bandera){
    case 1:// busqueda de medicamentos
                $IdArea = $_GET["IdArea"];

                $querySelect = "SELECT DISTINCT Codigo, Nombre, Concentracion, fcp.Id, FormaFarmaceutica,Presentacion
                                FROM farm_catalogoproductos fcp
                                INNER JOIN farm_catalogoproductosxestablecimiento fcpe
                                ON fcpe.IdMedicina=fcp.Id
                                INNER JOIN farm_medicinaexistenciaxarea fmexa
                                ON fmexa.IdMedicina=fcpe.IdMedicina
                                WHERE (Nombre LIKE '%$Busqueda%' or Codigo ='$Busqueda')
                                AND fcpe.Condicion='H'
                                AND fmexa.IdArea='$IdArea'
                                AND fmexa.IdEstablecimiento=" . $_SESSION["IdEstablecimiento"] . "
                                AND fmexa.IdModalidad=".$_SESSION["IdModalidad"]."
                                AND IdTerapeutico is not null
                                ORDER BY fcp.Id";

                $resp = pg_query($querySelect);
                while ($row = pg_fetch_array($resp)) {
<<<<<<< HEAD
                    $Nombre = $row["nombre"] . " - " . $row["concentracion"] . " - " . $row["formafarmaceutica"] . " - " . $row["presentacion"];
                    $IdMedicina = $row["id"]; //Estaba IdMedicina
                    $Codigo = $row["codigo"];
=======
                    $Nombre = $row["Nombre"] . " - " . $row["Concentracion"] . " - " . $row["FormaFarmaceutica"] . " - " . $row["Presentacion"];
                    $IdMedicina = $row["IdMedicina"];
                    $Codigo = $row["Codigo"];
>>>>>>> b828137fda9ad3e0fabfa24c69e6cc9738584735
                    ?>
                    <li onselect="this.text.value = '<?php echo htmlentities($Nombre); ?>';$('IdMedicina').value='<?php echo $IdMedicina; ?>';ObtenerExistenciaTotal();"> 
                        <span><?php echo $Codigo; ?></span>
                        <strong><?php echo htmlentities($Nombre); ?></strong>
                    </li>
                    <?php
                }// fin while
            break; // fin case 1
    case 2:// busqueda de pacientes
            $querySelect="  SELECT mnt_paciente.id, primer_nombre||' '||segundo_nombre||' '||(case when tercer_nombre!='' then tercer_nombre else '' end )||' '||primer_apellido||' '||segundo_apellido as NombrePaciente, numero
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
}//switch Bandera        
            
conexion::desconectar();
?>