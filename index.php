<!DOCTYPE html>
<html lang="es">
<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<head>
	<meta charset="UTF-8" >
	<title>REIMSER SOFT -- Recaudación de Impuestos y Servicios ALC JUNIN EDO. TACHIRA</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<style type="text/css">
	body{
		background-color: BurlyWood;
		padding: 10em auto;
	}
		.img-circle:hover{
			-webkit-transform: scale(1.3);
			   -moz-transform: scale(1.3);
			    -ms-transform: scale(1.3);
			     -o-transform: scale(1.3);
			        transform: scale(1.3);
		}
	</style>
</head>
<meta name="author" content="jarryp" >
<meta name="description" content="Sistema de Gestión de Información para el Control de Recaudación de Impuestos y Servicios de Alcaldía e Institutos Autonomos; Ajustado al marco juridico aplicable en materia administrativa en la Republica Bolivariana de Venezuela">
<meta name="keywords" content="presupuesto de ingresos, recaudación, liquidación, palacios systems">
<body>
<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
if($_SESSION['cod_usu']<>''){
	header('Location:principal.php');
	
}
?>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript">
		function limpiar(){
			document.getElementById("text1").value="";
			document.getElementById("text2").value="";
            document.getElementById("cmb_ente").value="Seleccione...";
            document.getElementById("cmb_prd").value="Seleccione...";
			document.getElementById("text1").focus();
		}

		function validar_acceso(){
			var xlog = document.getElementById("text1").value;
			var xpwd = document.getElementById("text2").value;
            var xent = document.getElementById("cmb_ente").value;
            var xprd = document.getElementById("cmb_prd").value;
        if( xpwd.length>0 && xlog.length>0 && xent!="Seleccione..." && xent.length>0 && xprd!="Seleccione..." && xprd.length>0  ){
			$.ajax({
				type:"POST",
				url:"php/validar_acceso.php?xlog="+xlog+"&xcod_ente="+xent+"&xcod_prd="+xprd,
				data: {"xpwd":document.getElementById("text2").value},
				success:function(data){
					var valor=data.split("#")
					if(valor[0]=="pasa"){
						 xmensaje="Bienvenido a REIMSER SOFT: ";
						 xmensaje+=valor[1].trim();
							alert(xmensaje);
							document.location.href="principal.php";
    					}
					else{
						$("#resultado").html(data);
					}
				}
			});
		}else{
			alert("Ingrese Datos de Acceso");
		}
		}

        function lt_periodos(){
            if(document.getElementById("cmb_ente").value!="Seleccione..."){
                $.ajax({
                    type:"GET",
                    url:"php/controler.php?xcod_ente="+document.getElementById("cmb_ente").value,
                    data:{xcontrol_op:"llenar_cmb_prd"},
                    success:function(data){
                        $("#cmb_prd").html(data);
                    }
                });
            }
        }

	</script>
    <center>
    <img src="img/logos/banner.jpg" width="85%" height="85">
    <br>
    <br>
    <div class="row well" style="width:60%;">
    	<table width="90%">
    		<tr>
    			<td colspan="2" align="center"> <legend> Validación de Acceso a REIMSER SOFT (WEB)
    			 					<img src="img/logos/logo_p002.jpg" width="300" height="50">
    			 				</legend>
    			 </td>
    		</tr>
    		<tr>
    			<td align="right"><strong>Usuario:*.-</strong></td>
    			<td>
    				<div class="input-group">
    				<span class="input-group-addon">
    					<span class="glyphicon glyphicon-user"></span>
    				</span>
    					<input type="text" id="text1" name="text1" class="form-control" maxlength="20" /> 
    				</div>
    			</td>
    		</tr>
    		<tr>
    			<td align="right"><strong>Contraseña:*.-</strong></td>
    			<td>
    			 <div class="input-group">
    				<span class="input-group-addon">
    					<span class="glyphicon glyphicon-asterisk"></span>
    				</span>  
    				<input type="password" id="text2" name="text1" class="form-control" maxlength="13" /> 
    			 </div>
    			</td>
    		</tr>
            <tr>
                <td align="right"><strong>Entidad:*.-</strong></td>
                <td>
                    <select id="cmb_ente" name="cmb_ente" class="form-control" onchange="lt_periodos()">
                        <option value="Seleccione...">Seleccione...</option>
                        <?php
                        require_once "php/procedimientos.php";
                        if($conn=conectarpg()){
                        $query="select trim(cod_ente) as cod_ente, nom_ente from entidad order by cod_ente";
                            if($result=pg_query($query)){
                                while ($row=pg_fetch_assoc($result)) {
                                    echo "<option value='".$row['cod_ente']."'>".$row['nom_ente']."</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right"><strong>Periodo:*.-</strong></td>
                <td>
                    <select id="cmb_prd" name="cmb_prd" class="form-control">
                        <option value="Seleccione...">Seleccione...</option>
                    </select>
                </td>
            </tr>
    		<tr>
    			<td></td>
    			<td align="center">
    				<button type="button" class="btn btn-info" onclick="validar_acceso()">
    				<span class="glyphicon glyphicon-lock"></span>
    				Aceptar</button>
    				<button type="button" class="btn btn-success" onclick="limpiar()">
    				<span class="glyphicon glyphicon-arrow-left"></span>
    				Cancelar</button>
    			</td>
    		</tr>
    		<tr>
    			<td colspan="2">
    				<div name='resultado' id="resultado"></div>
    			</td>
    		</tr>
    		<tr>
    			<td colspan="2">
    				<img src="img/logos/banner2.jpg" width="100%"  height="125">
    			</td>
    		</tr>
    	</table>
    </div>
    <p>&copy; 2016 <a href="http://www.palaciossystems.com">www.palaciossystems.com</a>
	<br> Webmaster: Ing. Jarry Palacios | <a href="mailto:jpalacios@palaciossystems.com">jpalacios@palaciossystems.com</a>
	</p>
    </center>



</body>
</html>