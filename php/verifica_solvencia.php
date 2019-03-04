<!DOCTYPE HTML>
<html lang="es">
<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<head>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript">
		var tableToExcel = (function() {
  		var uri = 'data:application/vnd.ms-excel;base64,'
    		, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    		, base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    		, format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  		return function(table, name) {
    		if (!table.nodeType) table = document.getElementById('tabla1')
    			var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    			window.location.href = uri + base64(format(template, ctx))
  			}
		})()

        function imp_solvencia(cedula){
          $.ajax({
            type:"GET",
            url:"controler.php",
            data:{xcontrol_op:"imp_solvencia",xcod_contri:cedula},
            success:function(data){
                alert("pasa");
                $("#contenedor1").html(data);
            }
          });
        }
	</script>
</head>
<body>

<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){

	$query="select * from contribuyente where cod_contri='".$_POST['cedula']."'";
	$result=pg_query($query);
	if ($result) {
	 $row=pg_fetch_assoc($result);
     $cedula=$row['cod_contri'];
     $nombre=$row['nom_contri'];
     $tel1=$row['tel_contri'];
     $tel2=$row['tcl_contri'];
     $email=$row['email_contri'];
     $nit=$row['nit_contri'];
     $direc=$row['dir_contri'];
	}
	 echo "<center><font size='2'>";
     echo "<table class='none' border='1' name='tabla1' id='tabla1'>";
     echo "<tr>";
     echo "<th>Cédula: </th><td>". $cedula ."</td>";
     echo "</tr>";
     echo "<tr>";
     echo "<th>Nombre: </th><td>". $nombre . "</td>";
     echo "</tr>";
     echo "<tr>";
     echo "<th>NIT: </th><td>". $nit . "</td>";
     echo "</tr>";
     echo "<tr>";
     echo "<th>Email: </th><td>". $email . "</td>";
     echo "</tr>";
     echo "<tr>";
     echo "<th>Telefono Celular: </th><td>". $tel1 . "</td>";
     echo "</tr>";
     echo "<tr>";
     echo "<th>Telefono Fijo: </th><td>". $tel2. "</td>";
     echo "</tr>";
     echo "<tr>";
     echo "<th>Dirrección: </th><td>". $direc ." </td>";
     echo "</tr>";
     
     $query="select r.fecha, r.n_recibo, c.des, dt.ser, dt.num, dt.neto, r.est
     from recibos r 
     left join recb_deta_1 dt on dt.n_recibo=r.n_recibo
     left join concepto c on c.cod=dt.cod_c
     where r.est='A' and r.cod_contri='".$cedula."'";

     //$query="select fecha, n_recibo, des, ser, num, neto, est from vista_03 where cod_contri='".$cedula."'";
     $result1=pg_query($query) or die("muere");
     if($result1){
     	echo "<tr><th colspan='2' align='center'><center> Recibos de Pago </center></th></tr>";
     	echo "<tr><td colspan='2'>";
     	echo "<table class='table table-striped table-hover'>";
     	echo "<tr><th>Fecha</th><th>Recibo</th><th>Concepto</th><th>Serie</th><th>Control</th><th>Neto</th><th>Estatus</th></tr>";
     	while ($row1=pg_fetch_assoc($result1)) {
     		echo "<tr>";
     		echo "<td>". $row1['fecha'] ."</td>";
     		echo "<td>". $row1['n_recibo'] ."</td>";
     		echo "<td align='center'>". $row1['des'] ."</td>";
     		echo "<td align='center'>". $row1['ser'] ."</td>";
     		echo "<td align='right'>". $row1['num'] ."</td>";
     		echo "<td align='right'>". number_format($row1['neto'],2,',','.') ."</td>";
     		echo "<td align='center'>". $row1['est'] ."</td>";
     		echo "</tr>";
     	}
     	echo "</table>";
     	echo "</td></tr>";
     }

     echo "<tr><th colspan='2' align='center'><center> Formas de Pago </center></th></tr>";
     $query="select nom_fp, nom_ban, num_cuen, n_recibo, deta, fecha_d, monto, est from vista_04 "
           ." where cod_contri='".$cedula."'"
           ." order by fecha_d desc ";
     $result2=pg_query($query);
     if($result2){
        echo "<tr><td colspan='2'>";
        echo "<table class='table table-striped table-hover'>";
        echo "<tr>";
            echo "<th>F. Pago</th>";
            echo "<th>Banco</th>";
            echo "<th>N. Cuenta</th>";
            echo "<th>N. Recibo</th>";
            echo "<th>N. Deposito</th>";
            echo "<th>Fecha Deposito</th>";
            echo "<th>Monto</th>";
            echo "<th>Status</th>";
        echo "</tr>";
        while ($row2=pg_fetch_assoc($result2)) {
            echo "<tr>";
            echo "<td>". $row2['nom_fp'] ."</td>";
            echo "<td>". $row2['nom_ban'] ."</td>";
            echo "<td>". $row2['num_cuen'] ."</td>";
            echo "<td>". $row2['n_recibo'] ."</td>";
            echo "<td>". $row2['deta'] ."</td>";
            echo "<td>". $row2['fecha_d'] ."</td>";
            echo "<td>". number_format($row2['monto'],2,',','.') ."</td>";
            echo "<td>". $row2['est'] ."</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</td></tr>";
     }
     echo "<tr><td colspan='2' align='center'>";
     ?>
     <input type="button" onclick="tableToExcel('tabla1', 'Resumen Contribuyente')" value="Exportar a Excel">
     <button type="button" class="btn btn-default">Exportar a PDF</button>
     <a href="imprimir_solvencia.php?xcod_contri=<?php echo $cedula; ?>&xnom_contri=<?php echo $nombre; ?>">Imprimir Solvencia </a>
     <?php
     echo "<td></tr>";
     echo "</table>";
     echo "</font></center>";
}else{
	echo '<div class="alert alert-info">'
		.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
		.'<strong>Error de conexión a la Base de Datos</strong> Debe consultar la '
		.'conectividad del hardware o ponerse en contacto con  el administrados de REIMSER SOFT...'
	.'</div>';
}


?>
</body>
</html>
