<?php
session_start();
if (!isset($_SESSION["IdPersonal"])) {
    echo "ERROR_SESSION";
} else {
    include("../../Clases/class.php");
    conexion::conectar();

    $IdModalidad = $_SESSION["IdModalidad"];

    class ComboLotes {

        function VerificaExitenciaLotes($IdMedicina, $IdEstablecimiento,$IdModalidad) {
            $SQL = "select farm_entregamedicamento.IdMedicina,Existencia, farm_lotes.Id, Lote, Descripcion

	from farm_entregamedicamento
	inner join farm_lotes
	on farm_entregamedicamento.IdLote=farm_lotes.Id
	inner join farm_catalogoproductos
	on farm_catalogoproductos.Id = farm_entregamedicamento.IdMedicina
	inner join farm_unidadmedidas
	on farm_unidadmedidas.Id=farm_catalogoproductos.IdUnidadMedida
	
	where farm_entregamedicamento.IdMedicina=" . $IdMedicina . "
	and Existencia <> 0
	and left(to_char(FechaVencimiento,'YYYY-MM.DD'),7) >= left(to_char(current_date,'YYYY-MM-DD'),7)
        and farm_entregamedicamento.IdEstablecimiento=$IdEstablecimiento
        and farm_entregamedicamento.IdModalidad=$IdModalidad
	order by FechaVencimiento asc";

            $resp = pg_query($SQL);
            return($resp);
        }

        function ValorDivisor($IdMedicina, $IdEstablecimiento, $IdModalidad) {
            $SQL = "select DivisorMedicina from farm_divisores 
                 where IdMedicina=" . $IdMedicina . " and IdEstablecimiento=" . $IdEstablecimiento . " 
                 and IdModalidad=$IdModalidad";
            $resp = pg_query($SQL);
            return($resp);
        }

    }
    ?>
    <table width="100%" border="1" style="border:solid;border-collapse:collapse;">
        <?php
        $IdTerapeutico = $_GET["IdTerapeutico"];
        $Nombre = $_GET["Nombre"];

        $selectGrupo = "select * from mnt_grupoterapeutico where Id=" . $IdTerapeutico;
        $Grupo = pg_query($selectGrupo);

        $count = 0;

        if (trim($Nombre) != "" or $IdTerapeutico != '0') {

            //$NombreGrupo1=  pg_fetch_array(pg_query(""));
            //$NombreGrupo=$NombreGrupo1["GrupoTerapeutico"];
            //$IdTerapeutico=$DataGrupo["IdTerapeutico"];

            if ($Nombre != '') {
                $comp = " and (Nombre like '%$Nombre%' or Codigo='$Nombre')";
            } else {
                $comp = "";
            }
            if ($IdTerapeutico != '0') {
                $comp1 = "and farm_catalogoproductos.IdTerapeutico='" . $IdTerapeutico . "'";
            } else {
                $comp1 = "";
            }

            $querySelect = "select farm_catalogoproductos.Id as idmedicina,Codigo ,farm_catalogoproductos.Nombre,farm_catalogoproductos.FormaFarmaceutica,
	farm_catalogoproductos.Concentracion,Presentacion,IdTerapeutico
	from farm_catalogoproductos
	inner join farm_catalogoproductosxestablecimiento cpe
	on cpe.IdMedicina=farm_catalogoproductos.Id
	
	where cpe.Condicion='H'
	" . $comp1 . "
	" . $comp . "
	and cpe.IdEstablecimiento=" . $_SESSION["IdEstablecimiento"] . "
        and cpe.IdModalidad=$IdModalidad
	order by Codigo";
            $resp = pg_query($querySelect);

            if ($Datos = pg_fetch_array($resp,null,PGSQL_ASSOC)) {
                if($IdTerapeutico!='0') {
                    $NombreGrupo1 = pg_fetch_row(pg_query("select GrupoTerapeutico from mnt_grupoterapeutico
                                                                    where Id=" . $Datos["idmedicina"]));
                    $NombreGrupo = $NombreGrupo1[0];
                } else {
                    $NombreGrupo = "";
                }
                ?>

                <tr class="MYTABLE"><td align="center" colspan="7">&nbsp;<strong><?php echo $NombreGrupo; ?></strong></td></tr>
                <tr class="MYTABLE">
                    <td width="50" align="center">&nbsp;<strong>Codigo</strong></td>
                    <td width="141" align="center">&nbsp;<strong>Medicamento</strong></td>
                    <td width="94" align="center">&nbsp;<strong>Concentraci&oacute;n</strong></td>
                    <td width="97" align="center">&nbsp;<strong>Presentaci&oacute;n</strong></td>	
                    <td width="101" align="center">&nbsp;<strong>Unidad de Medida</strong></td>	
                    <td width="169" align="center"><strong>Existencias</strong><strong></strong><strong> </strong></td>
                    <td width="296" align="center">&nbsp;<strong>Ingresos</strong></td>
                </tr>
                <?php
                do {
                    $Codigo = $Datos["codigo"];
                    $Nombre = htmlentities($Datos["nombre"]);
                    $Concentracion = $Datos["concentracion"];
                    $Forma = $Datos["formafarmaceutica"] . ' - ' . $Datos["presentacion"];
                    $IdMedicina = $Datos["idmedicina"];

                    /* Unidad de Medida */
                    $data2 = pg_fetch_array(pg_query("select farm_unidadmedidas.Descripcion,
			farm_unidadmedidas.UnidadesContenidas as Divisor
			from farm_unidadmedidas
			inner join farm_catalogoproductos
			on farm_catalogoproductos.IdUnidadMedida=farm_unidadmedidas.Id
			where farm_catalogoproductos.Id='$IdMedicina'"));
                    $UnidadMedida = $data2["descripcion"];
                    $Divisor = $data2["divisor"];
                    /*                     * *********************** */

                    $RespEx = pg_query("select farm_entregamedicamento.*,farm_lotes.*
			from farm_entregamedicamento
			inner join farm_catalogoproductos
			on farm_catalogoproductos.Id=farm_entregamedicamento.IdMedicina
			inner join farm_lotes
			on farm_lotes.Id=farm_entregamedicamento.IdLote
			where farm_entregamedicamento.IdMedicina='$IdMedicina'
                        and farm_entregamedicamento.IdEstablecimiento=" . $_SESSION["IdEstablecimiento"] . "
                        and farm_entregamedicamento.IdModalidad=$IdModalidad    
			and farm_entregamedicamento.Existencia <> '0' 
			and substr(to_char(FechaVencimiento,'YYYY-MM-DD'),1,7) >= substr(to_char(current_date,'YYYY-MM-DD'),1,7)
			order by farm_lotes.FechaVencimiento");
                    $i = 0;
                    $Lote = "";
                    $existencia_ = "";
                    $FechaVencimiento = "";
                    while ($data = pg_fetch_array($RespEx)) {

                        $existencia = $data["existencia"];

                        if ($existencia == '') {
                            $existencia_[$i] = 0;
                        } else {

                            if ($respDivisor = pg_fetch_array(ComboLotes::ValorDivisor($Datos["IdMedicina"], $_SESSION["IdEstablecimiento"], $IdModalidad))) {
                                $Divisor = $respDivisor[0];

                                if ($data["Existencia"] < 1) {
                                    //Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
                                    $TransformaEntero = number_format($data["Existencia"] * $Divisor, 0, '.', ',');
                                    $CantidadTransformada = $TransformaEntero . '/' . $Divisor;
                                } else {
                                    //Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
                                    $CantidadReal = number_format($data["Existencia"], 2, '.', ',');
                                    $CantidadBase = explode('.', $CantidadReal);

                                    $Entero = $CantidadBase[0]; //Faccion ENTERA

                                    $Decimal = $CantidadBase[1];
                                    if ($Decimal == 0) {
                                        $Decimal = "";
                                        $Quebrado = "";
                                    } else {

                                        $Quebrado = number_format(($Decimal / 100) * $Divisor, 0, '.', ',');
                                        $Quebrado = '[' . $Quebrado . '/' . $Divisor . ']';
                                    }


                                    $CantidadTransformada = $Entero . ' ' . $Quebrado;
                                }
                                $CantidadIntro = $CantidadTransformada;
                            } else {
                                $CantidadIntro = $data["Existencia"] / $Divisor;
                            }

                            $existencia_[$i] = $CantidadIntro;
                        }
                        if ($existencia > 0) {
                            $Lote[$i] = $data["Lote"];
                            $FechaVencimiento[$i] = $data["FechaVencimiento"];
                        }
                        $i++;
                    }//While para despliegue de Lotes
                    if ($Lote != NULL) {
                        $Campos = count($Lote);   //Conteo de los vectores
                    } else {
                        $Campos = 0;
                    }


                    $div = "saving" . $IdMedicina;
                    $boton = "guardar" . $IdMedicina;
                    $divExis = "existenciaActual" . $IdMedicina;
                    ?>
                    <tr class="FONDO">
                        <td align="center">&nbsp;<?php echo $Codigo; ?></td>
                        <td align="center">&nbsp;<?php echo $Nombre; ?></td>
                        <td align="center">&nbsp;<?php echo $Concentracion; ?></td>
                        <td align="center">&nbsp;<?php echo htmlentities($Forma); ?></td>
                        <td align="center">&nbsp;<?php echo $UnidadMedida; ?></td>
                        <td align="center"><div id="<?php echo $divExis; ?>">
                <?php
                for ($i = 0; $i <= $Campos - 1; $i++) {
                    if ($FechaVencimiento[$i] != NULL) {
                        $Date = explode('-', $FechaVencimiento[$i]);
                        $Fecha = $Date[2] . "-" . $Date[1] . "-" . $Date[0];
                    } else {
                        $Fecha = "";
                    }
                        //<a onclick='javascript:popUp(\"ActualizaLotes.php?Lote=$Lote[$i]&IdMedicina=$IdMedicina\")'> 
                    echo "Existencia: " . $existencia_[$i] . "<br>Lote: " . $Lote[$i] . "<br>Vencimiento: " . $Fecha . "<br><br>";
                }
                ?>
                            </div></td>
                        <td align="left">
                            <table width="297">
                                <tr class="FONDO"><td width="131">
                                        Cantidad:</td>
                                    <td width="227"><input type="text" id="<?php echo $IdMedicina; ?>" name="<?php echo $IdMedicina; ?>" maxlength="12" size="4" value="0" onFocus="if(this.value=='0'){this.value=''}" onBlur="NoCero(this.id);if(this.value==''){this.value='0'}" onKeyPress="return acceptNum(event)">
                                    </td></tr>
                                <tr class="FONDO"><td>
                                        Lote:</td><td>
                                        <div id="<?php echo "ComboLotesMedicina" . $IdMedicina; ?>">
                                            <?php
                                            $respLotesExiste = ComboLotes::VerificaExitenciaLotes($IdMedicina, $_SESSION["IdEstablecimiento"],$IdModalidad);
                                            if ($rowLotesExiste = pg_fetch_array($respLotesExiste)) {
                                                $disabled = 'disabled="true"';
                                                echo "<select id='Lote" . $IdMedicina . "' name='Lote" . $IdMedicina . "' onchange='MostrarOpcionLote(this.value,this.id);'>";

                                                do {

                                                    if ($respDivisor = pg_fetch_array(ComboLotes::ValorDivisor($IdMedicina, $_SESSION["IdEstablecimiento"],$IdModalidad))) {
                                                        $Divisor = $respDivisor[0];

                                                        if ($rowLotesExiste["Existencia"] < 1) {
                                                            //Si la cantidad a mostrar es menor que 1 es decir menor a un frasco
                                                            $TransformaEntero = number_format($rowLotesExiste["Existencia"] * $Divisor, 0, '.', ',');
                                                            $CantidadTransformada = $TransformaEntero . '/' . $Divisor;
                                                        } else {
                                                            //Si la cantidad es mayor a un frasco se realiza este cambio a quebrados
                                                            $CantidadReal = number_format($rowLotesExiste["Existencia"], 2, '.', ',');
                                                            $CantidadBase = explode('.', $CantidadReal);

                                                            $Entero = $CantidadBase[0]; //Faccion ENTERA

                                                            $Decimal = $CantidadBase[1];
                                                            if ($Decimal == 0) {
                                                                $Decimal = "";
                                                                $Quebrado = "";
                                                            } else {

                                                                $Quebrado = number_format(($Decimal / 100) * $Divisor, 0, '.', ',');
                                                                $Quebrado = '[' . $Quebrado . '/' . $Divisor . ']';
                                                            }


                                                            $CantidadTransformada = $Entero . ' ' . $Quebrado;
                                                        }
                                                        $CantidadIntro = $CantidadTransformada;
                                                    } else {
                                                        $CantidadIntro = $rowLotesExiste["Existencia"] / $Divisor;
                                                    }



                                                    echo "<option value='" . $rowLotesExiste["Lote"] . "'>Existencia: " . $CantidadIntro . " " . $rowLotesExiste["Descripcion"] . " - Lote: " . $rowLotesExiste["Lote"] . "</option>";
                                                } while ($rowLotesExiste = pg_fetch_array($respLotesExiste));
                                                echo "<option value='N'>NUEVO LOTE</option>
				</select>";
                                            } else {
                                                //Si no existen lotes se da la opcion de ingresar el lote respectivo
                                                $disabled = '';
                                                ?>	

                                                <input id="<?php echo "Lote" . $IdMedicina; ?>" name="<?php echo "Lote" . $IdMedicina; ?>" size="8" value="Lote." onFocus="if(this.value=='Lote.'){this.value='';}" onBlur="if(this.value==''){this.value='Lote.';}">
                <?php }//si no existen lotes  ?>
                                        </div>
                                    </td></tr>
                                <tr class="FONDO"><td>
                                        Fecha de Ventto.:</td><td>
                                        <div id="<?php echo "Combos" . $IdMedicina; ?>">
                                            <select id="<?php echo "mes" . $IdMedicina; ?>" name="<?php echo "mes" . $IdMedicina; ?>" <?php echo $disabled; ?> >
                                                <option value="0">[Seleccione Mes]</option>
                                                <option value="01">ENERO</option>
                                                <option value="02">FEBRERO</option>
                                                <option value="03">MARZO</option>
                                                <option value="04">ABRIL</option>
                                                <option value="05">MAYO</option>
                                                <option value="06">JUNIO</option>
                                                <option value="07">JULIO</option>
                                                <option value="08">AGOSTO</option>
                                                <option value="09">SEPTIEMBRE</option>
                                                <option value="10">OCTUBRE</option>
                                                <option value="11">NOVIEMBRE</option>
                                                <option value="12">DICIEMBRE</option>
                                            </select>
                                            <select id="<?php echo "ano" . $IdMedicina; ?>" name="<?php echo "ano" . $IdMedicina; ?>" <?php echo $disabled; ?> >
                                                <option value="0">[Seleccione A&ntilde;o]</option>
                    <?php
                    $date = date('Y');

                    for ($i = 0; $i <= 12; $i++) {
                        $ano = $date + $i;
                        ?>
                                                    <option value="<?php echo $ano; ?>"><?php echo $ano; ?></option>
                    <?php }//fin de for
                    ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="FONDO"><td>
                                        Precio Unitario($):</td><td><input id="<?php echo "Precio" . $IdMedicina; ?>" name="<?php echo "Precio" . $IdMedicina; ?>" type="text" size="8" value="0" onFocus="if(this.value=='0'){this.value=''}" onBlur="if(this.value==''){this.value='0'}" onKeyPress="return acceptNum2(event)" <?php echo $disabled; ?> >
                                        <input id="<?php echo $boton; ?>" name="guardar" type="button" value="Guardar" onClick="javascript:Alerta(<?php echo $IdMedicina; ?>)">
                                    </td></tr></table>
                        </td>
                    </tr>
                <?php
            } while ($Datos = pg_fetch_array($resp)); //while
            echo "<tr class='MYTABLE'><td colspan=\"7\" align=\"right\"><input type=\"submit\" name=\"guardar" . $IdTerapeutico . "\" value=\"Guardar\" style=\"border-bottom-color:#000099; border-left-color:#000099; border-top-color:#000099; border-right-color:#000099\"></tr></td>";
        }//If pg_fetch_array
    } else {//while Teraputico
        echo "";
    }
    conexion::desconectar();
    ?>
    </table>

<?php }//Validcion de Session ?>
