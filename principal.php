<!DOCTYPE html>
<?php
require_once("php/procedimientos.php");
session_start();
error_reporting(E_ALL ^ E_NOTICE);
?>
<html lang="es">
<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<head>
	<meta charset="UTF-8" >
	<title>REIMSER SOFT -- Recaudación de Impuestos y Servicios. Alc. MJunin Edo. Tachira</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<!-- <link rel="stylesheet" type="text/css" href="css/menu_p.css"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<style type="text/css">
	body{
		background-color: Cornsilk;
		padding: 4em auto;

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
	<div class="row">
	<?php
	session_start();
		if($_SESSION['cod_usu']==''){
			header('Location:index.php');
		}
	?>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<center>
	<table width="95%" bgcolor="white">
		<!-- Linea de tabla para encabezado -->
		<tr>
			<td colspan="2">
				<img src="img/logos/banner.jpg" width="100%" height="85">
			</td>
		</tr>
        <!-- FIN Linea de tabla para encabezado -->
        <!-- linea de tabla para crear cuerpo de pagina; tendra dos columnas -->
        <!-- la primera para el menú y la siguiente para el contenedor principal de contenidos -->
        <!--LINEA EN LA TABLA PARA INSERSIÓN DE MENU-->
        <tr>
			<td colspan="2" align="center"><?php require_once "template/sub_menu.php"; ?> </td>
		</tr>
		<!--FIN LINEA EN LA TABLA PARA INSERSIÓN DE MENU-->
        <tr>
        	<td width="5%" valign="top">

        	</td>

        	<td width="95%">
        		<div name="principal" id="principal" class='row' style=" height='500'; ">
        			<h1>
						  Sistema de Control de Recaudación de Impuestos y Servicios
        			    <br>
        			    (REIMSER SOFT PLUS)
        			</h1>
<div class="row">
            <div class="col-md-6" id="area_graf">
               <div class="row">
        			<?php
        			/*if($conn=conectarpg()){
                    
                        $sql="select date_part('year',fecha) as ejec,
                            sum(neto) as monto
                            from recibos
                            where est='A' and cod_ente ='".$_SESSION['cod_ente']."' 
                            group by date_part('year',fecha)
                            order by ejec";

                    $result=pg_query($sql) or die("Error de Consulta de Datos...");
                    if($result){
                        while($row=pg_fetch_assoc($result)){
                            $datax[]=$row['ejec'];
                            $datay[]=$row['monto'];
                        }
                        
                       
                    }
                    
                 }
                    include("php/graf1.php");*/
                     
                        
    if($conn2=conectarpg()) {
    
    $sql="select extract('year' from fecha) as agno, sum(neto) as monto
          from recibos
          where est='A' AND FECHA<=NOW()
           group by extract('year' from fecha)
           order by extract('year' from fecha)
           ";
     ?>

    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/Chart.bundle.js"></script>
    <style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
    </style>



    <div id="container" style="width: 95%;">
        <canvas id="canvas"></canvas>
    </div>

    <script>
        //var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        var randomScalingFactor = function() {
            return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
        };
        var randomColorFactor = function() {
            return Math.round(Math.random() * 255);
        };
        var randomColor = function() {
            return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
        };
        
        //"rgba(220,220,220,0.5)",
        var barChartData = {
            labels: [
                <?php
                    if($result=pg_query($sql)){
                     while($row=pg_fetch_array($result)){
                         ?>
                 '<?php echo $row["agno"]?>',
                    <?php
                     }   
                    }
                ?>
            ],
            datasets: [{
                label: 'Volumen Liquidado:',
                backgroundColor: "rgba(0,220,220,0.5)",
                data: [
                   <?php
                    if($result=pg_query($sql)){
                     while($row=pg_fetch_array($result)){
                         ?>
                 '<?php echo $row["monto"]?>',
                    <?php
                     }   
                    }
                ?> 
                ]
            }/*, {
                //hidden: true,
                label: 'Dataset 2',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: [randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()]
            }, {
                label: 'Dataset 3',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: [randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()]
            }*/
                      ]

        };
    /*
        window.onload = function() {
            
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    // Elements options apply to all of the options unless overridden in a dataset
                    // In this case, we are setting the border of each bar to be 2px wide and green
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Recaudación por Años'
                    }
                }
            });

        };
        */
 
     </script>   
    </div>
        			
        			
       			</div>
	
       	    <div class="col-md-6">
       	         
       	<?php
        /*
        $sql="select distinct extract('year' from fecha) as agno
            from recibos
            where est='A' and fecha<=now()
            order by agno desc limit 4";*/

        $sql="select distinct extract('year' from fecha) as agno
            from recibos
            where est='A' and fecha<=now()
union 
select '2017'::integer as agno
union 
select '2016'::integer as agno
order by agno desc ";


            $result=pg_query($sql);
            while($row=pg_fetch_array($result)){
                $datay[]=$row['agno'];
            }
                     ?>
        <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
    <div style="width:95%;">
        <canvas id="canvas2"></canvas>
    </div>

    <script>
        //var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var randomScalingFactor = function() {
            return Math.round(Math.random() * 100 * (Math.random() > 0.5 ? -1 : 1));
        };
        var randomColorFactor = function() {
            return Math.round(Math.random() * 255);
        };
        var randomColor = function(opacity) {
            return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
        };

        var config2 = {
            type: 'line',
            data: {
                labels: ["Ene","Feb","Mar","Abl","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
                datasets: [{
                    label: '<?php echo $datay[3];?>',
                    data: [
                        <?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[3]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>
                    ],
                    fill: false,
                    borderDash: [5, 5],
                }, {
                    label: '<?php echo $datay[2];?>',
                    data: [
                        <?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[2]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>
                    ],
                    fill: false,
                    borderDash: [5, 5],
                }, {
                    label: '<?php echo $datay[1];?>',
                    data: [<?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[1]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>],
                    lineTension: 0,
                    fill: false,
                }, {
                    label: '<?php echo $datay[0];?>',
                    data: [<?php
                            $sql=" select extract('month' from fecha) as mes , sum(neto) as monto
                                    from recibos 
                                    where est='A' and fecha<=now() and extract('year' from fecha)=".$datay[0]."
                                    group by extract('month' from fecha)
                                    order by extract('month' from fecha)";
                           $result=pg_query($sql);
                            while($row=pg_fetch_array($result)){
                                ?>
                                '<?php echo $row["monto"]?>',
                            <?php
                            }
                        ?>],
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'bottom',
                },
                hover: {
                    mode: 'label'
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Meses'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Cant. (Bs.)'
                        }
                    }]
                },
                title: {
                    display: true,
                    text: 'Tendencia Anual detallada por meses de Liquidación'
                }
            }
        };

        $.each(config2.data.datasets, function(i, dataset) {
            var background = randomColor(0.5);
            dataset.borderColor = background;
            dataset.backgroundColor = background;
            dataset.pointBorderColor = background;
            dataset.pointBackgroundColor = background;
            dataset.pointBorderWidth = 1;
        });

        window.onload = function() {
            
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    // Elements options apply to all of the options unless overridden in a dataset
                    // In this case, we are setting the border of each bar to be 2px wide and green
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Recaudación por Años'
                    }
                }
            });
            //separa
            var ctx2 = document.getElementById("canvas2").getContext("2d");
            window.myLine = new Chart(ctx2, config2);
        };
        

    </script>
    </div>
</div>
       	    
         <?php
     }
     pg_close($conn2);                
    ?>
        		<br>
        		<br>
        	</td>
        </tr>
        <!-- Fin de linea de tabla para cuerpo de la pagina -->
        <!-- Linea de tabla para píe de pagina -->
        <tr>
        	<td colspan="2" width="100%"></td>
        </tr>
        <!-- Fin de Linea de tabla para píe de pagina -->
	</table>
	<p>&copy; 2016 <a href="http://www.palaciossystems.com">www.palaciossystems.com</a>
	<br> Webmaster: TSU Jarry Palacios | <a href="mailto:jpalacios@palaciossystems.com">jpalacios@palaciossystems.com</a>
	</p>
	</center>
	<br>
	<br>
	<div class="row">
		<div class="col-sm-6 col-md-4">
			<div class="thumbnail">
				<img src="img/logos/logo_ps1.png" alt="...">
				<div class="caption">
				<h3>Palacio's Systems, F. P.</h3>
				<p align="justify">Somos una organización con alto grado de sensibilidad 
				con la Administración Pública Municipal; creamos Sistemas de Gestión de Información 
				considerando las caracteristicas y necesidades especificadas de cada institución.</p>
				<p>
				<a href="http://www.palaciossystems.com" class="btn btn-primary" role="button">Web</a>
				</p>
			</div>
	    </div>
	</div>

	<div class="col-sm-6 col-md-4">
			<div class="thumbnail">
				<img src="img/logos/logo_p001.jpg" alt="...">
				<div class="caption">
				<h3>SOFTPRE</h3>
				<p align="justify">
					Software de Control Presupuestario orientado a las instituciones, Públicas 
					que permite controlar los procesos de registro, procesamiento, validación 
					y presentación de ordenes de compra, servicio y pago, carga inicial de presupuesto de 
					Gastos, modificaciones presupuestarias; Elaboración de nominas, retenciones IVA, ISLR, Timbre Fiscal
					 entre otros procesos referentes al gasto público
				</p>
				<p>
				<a href="http://www.palaciossystems.com/03-sp.pdf" class="btn btn-primary" role="button">Resumen</a>
				<a href="https://www.youtube.com/watch?v=5dWo6jkQ6iM" class="btn btn-default" role="button">Video</a>
				</p>
			</div>
	    </div>
	</div>

	<div class="col-sm-6 col-md-4">
			<div class="thumbnail">
				<img src="img/logos/logo_p002.jpg" alt="...">
				<div class="caption">
				<h3>REIMSER SOFT</h3>
				<p align="justify">
					Sistema de Gestión de Información en versión Desktop y Web que permite a Alcaldías 
					e Institutos Autonomos llevar el control de la Recaudación de Impuestos y Servicios 
					cumpliendo con el formal proceso de la liquidación a la vez que automaticamente procesa
					 los resumenes respectivos.
				</p>
				<p>
				<a href="http://www.palaciossystems.com/04-RS.pdf" class="btn btn-primary" role="button">Resumen</a>
				<a href="https://www.youtube.com/watch?v=C6H7e3jigB8" class="btn btn-default" role="button">Video</a>
				</p>
			</div>
	    </div>
	</div>

 </div>
	</div>

<div class="modal fade" id='acercade' tabindex='-1' role='dialog' aria-labelledby='myModelLabel' aria-hidden='true'>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<table>
			<tr>
			<td>
				<img src="http://localhost/reimsersoft/img/iconos/ico_ps2.ico" width="170" height="50">
			</td>
			<td>
				<h3><strong>REIMSER SOFT</strong></h3> 
				<h5>(Software de Control de Recaudaión de Impuestos y Servicios)</h5>
			</td>
			</tr>
			</table>
				<button type="button" class="close" data-dismiss='modal' aria-hidden='true'>
					&times;
				</button>
			</div>
			<div class="modal-body">
			<center>
				<table border="1" width="90%">
				    <tr>
				    	<td colspan="2" bgcolor="lightsteelblue" align="center"><strong>Acerca de</strong></td>
				    </tr>
					<tr>
						<th width="35%">Desarrollado por:</th>
						<td width="65%">PALACIO'S SYSTEMS  RIF: V-16233325-5</td>
					</tr>
					<tr>
						<th>Responsable:</th>
						<td>T. S. U. JARRY JESUS PALACIOS RIVAS</td>
					</tr>
					<tr>
						<th>Teléfono:</th>
						<td>(0146) 173-80-65<br>
						    (0276) 7628833 
						</td>
					</tr>
					<tr>
						<th>Correo Electronico:</th>
						<td>jpalacios@palaciossystems.com<br>
						    jarry.palacios@hotmail.com</td>
					</tr>
					<tr>
						<th>Logo:</th>
						<td>
							<img src="img/logos/logo_p002.jpg" width="280" height="100">
						</td>
					</tr>
					<tr>
						<th>Pagina Web:</th>
						<td><a href="http://www.palaciossystems.com" target="_self">www.palaciossystems.com</a></td>
					</tr>
					<tr>
						<td colspan="2" align="center" valign="center">
							<img src="img/software/009.jpg" width="510" height="250">
						</td>
					</tr>
				</table>
			</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss='modal'>
					Cerrar
				</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
