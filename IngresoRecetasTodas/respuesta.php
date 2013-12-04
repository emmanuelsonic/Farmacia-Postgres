<?php
session_start();
include('../Clases/class.php');
conexion::conectar();
$Bandera=$_GET['Bandera'];
$Busqueda = $_GET['q'];

switch($Bandera){
    case 1:// busqueda de medicamentos
                $IdArea = $_GET["IdArea"];

                $querySelect = "select distinct Codigo, Nombre, Concentracion, fcp.IdMedicina, FormaFarmaceutica,Presentacion
                                        from farm_catalogoproductos fcp
                                        inner join farm_catalogoproductosxestablecimiento fcpe
                                        on fcpe.IdMedicina=fcp.IdMedicina
                                        inner join farm_medicinaexistenciaxarea fmexa
                                        on fmexa.IdMedicina=fcpe.IdMedicina
                where (Nombre like '%$Busqueda%' or Codigo ='$Busqueda')
                and fcpe.Condicion='H'
                and fmexa.IdArea='$IdArea'
                and fmexa.IdEstablecimiento=" . $_SESSION["IdEstablecimiento"] . "
                and fmexa.IdModalidad=".$_SESSION["IdModalidad"]."
                and IdTerapeutico is not null
                order by fcp.IdMedicina";

                $resp = mysql_query($querySelect);
                while ($row = mysql_fetch_array($resp)) {
                    $Nombre = $row["Nombre"] . " - " . $row["Concentracion"] . " - " . $row["FormaFarmaceutica"] . " - " . $row["Presentacion"];
                    $IdMedicina = $row["IdMedicina"];
                    $Codigo = $row["Codigo"];
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