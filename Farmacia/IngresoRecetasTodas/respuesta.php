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
            $querySelect="  select IdNumeroExp,mnt_datospaciente.IdPaciente,
                            concat_ws(' ',PrimerNombre,SegundoNombre,TercerNombre,PrimerApellido,SegundoApellido) as NombrePaciente
                            
                            from mnt_datospaciente
                            inner join mnt_expediente on mnt_expediente.IdPaciente=mnt_datospaciente.IdPaciente				
                            where (concat_ws(' ',concat_ws(' ',PrimerNombre,SegundoNombre,TercerNombre),CONCAT_WS(' ',PrimerApellido,SegundoApellido)) like '%$Busqueda%' or IdNumeroExp = '$Busqueda')
                            limit 100";

            $resp=mysql_query($querySelect);
            while($row=mysql_fetch_array($resp)){
                $NombrePaciente=$row["NombrePaciente"];
                //$IdPaciente=$row["IdPaciente"];
                $IdNumeroExp=$row["IdNumeroExp"];
?>
                <li onselect="this.text.value = '<?php echo htmlentities($NombrePaciente);?>';$('Expediente').value='<?php echo $IdNumeroExp;?>';$('Expediente').innerHTML='<?php echo $IdNumeroExp;?>';$('Nombre').innerHTML='<?php echo htmlentities($NombrePaciente);?>';">        
                    <strong><?php echo htmlentities($NombrePaciente)."   [ ".htmlentities($IdNumeroExp)." ]";?></strong>
                </li>
<?php       }// fin del while
    break; // fin case 2 busqueda de pacientes
}//switch Bandera        
            
conexion::desconectar();
?>