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
	</script>

</head>
<body>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<?php
session_start();
$fecha=$_POST['fecha'];
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){

$query="select c.des, sum(r.neto) as total, count(*) as nro "
." from recb_deta_1 r"
." left join concepto c on c.cod=r.cod_c"
." left join recibos re on re.n_recibo=r.n_recibo"
." where r.cod_prd='".$_SESSION['cod_prd']."' and re.fecha='".$_POST['fecha']."' and re.est='A'"
." group by c.des "
." order by c.des ";


$result=pg_query($query);
	if($result){
		echo "<center><table bgcolor='white' border='3' name='tabla1' id='tabla1' class='table table-striped table-hover'>";
		echo "<tr><td colspan='3'><strong><div class='alert alert-info'>Alcaldía del Municipio Junín Edo. Táchira<br> Reporte de Cierre Diario al ". $_POST['fecha'] ."</div></strong></td></tr>";
		echo "<tr>";
		echo "<th> Concepto </th>";
		echo "<th> Nro Recibos</th>";
		echo "<th> Monto Recaudado (Bs.) </th></tr>";
		$acu1=0;
		$acu2=0;
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>". $row['des'] ."</td>";
			echo "<td>". $row['nro'] ."</td>";
			echo "<td align='right'>". number_format($row['total'],2,',', '.') ."</td>";
			echo "</tr>";
			$acu1=$acu1+$row['nro'];
			$acu2=$acu2+$row['total'];
		}
		echo "<tr>";
		echo "<th>Totales.....</th>";
		echo "<th>". $acu1 ."</th>";
		echo "<th>". number_format($acu2,2,',', '.') ."</th>";
		echo "</tr>";

		$query="select distinct nom_ban, num_cuen, sum(monto) as total, count(*) as cant"
		." from vista_04"
		." where fecha='". $_POST['fecha'] ."'"
		." group by nom_ban, num_cuen"
		." order by nom_ban, num_cuen";
		$result1=pg_query($query);
		if($result1){
			echo "<tr><th colspan='3' align='center'><center>Resumen Bancos</center></th></tr>";
			echo "<tr><td colspan='3'>";
			echo "<table class='table table-striped table-hover'>";
			echo "<tr>";
			echo "<th> Banco </th>";
			echo "<th> N. Cuenta </th>";
			echo "<th> Cant </th>";
			echo "<th> Monto </th>";
			echo "</tr>";
			$acu3=0;
			$acu4=0;
			while ($row1=pg_fetch_assoc($result1)) {
				$acu3=$acu3+$row1['cant'];
				$acu4=$acu4+$row1['total'];
				echo "<tr>";
				echo "<td>". $row1['nom_ban'] ."</td>";
				echo "<td>". $row1['num_cuen'] ."</td>";
				echo "<td>". $row1['cant'] ."</td>";
				echo "<td align='right'>". number_format($row1['total'],2,',', '.') ."</td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<th colspan='2'> Totales...</th>";
			echo "<td><b>". $acu3 ."</b></td>";
			echo "<td align='right'><b>". number_format($acu4,2,',', '.') ."</b></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th colspan='4'><center>Resumen Por Caja</center></th>";
			echo "</tr>";
			$query="select a.*,"
       				." (select count(*) from recibos "
       				."  where est='A' and fecha='". $_POST['fecha'] ."' and cod_caja=a.cod_caja) as activos,"
       				."  (select count(*) from recibos "
       				."    where est='N' and fecha='". $_POST['fecha'] ."' and cod_caja=a.cod_caja) as nulos,"
				." (select sum(d.neto) from recb_deta_1 d "
                                ."  left join recibos r on r.n_recibo=d.n_recibo " 
                                ."   where r.fecha='".$_POST['fecha']."' and d.cod_prd='".$_SESSION['cod_prd']."' " 
                                ." and r.cod_caja = a.cod_caja) as conceptos ,"
				." (select sum(d.monto) from recb_deta_2 d "
				."    left join recibos r on r.n_recibo=d.n_recibo "
				."    where r.fecha='".$_POST['fecha']."' and r.cod_caja=a.cod_caja) as efectos_cobro "
					." from "
					." 	(select distinct cod_caja,"
                	." 	 sum(total) as total"
  					." 	from "
					." 		(select cod_caja, "
					." 			case est"
					." 				when 'A' then neto"
					." 				when 'N' then 0"
					." 			end as total"
					." 		from recibos r	"
					." where r.fecha='". $_POST['fecha'] ."') as a"
					." group by cod_caja"
					." order by cod_caja) as a";
			$result2=pg_query($query);
			if($result2){
				echo "<tr>";
				echo "<th>Nro. Caja</th>";
				echo "<th>Total Procesado (Bs.)</th>";
                                echo "<th>Conceptos</th>";
				echo "<th>Efectos de Cobro</th>";
				echo "<th>Recibos Activos</th>";
				echo "<th>Recibos Nulos</th>";
				echo "</tr>";
				$acu5=0;
				$acu6=0;
				$acu7=0;
				while ($row2=pg_fetch_assoc($result2)) {
						echo "<tr>";
						echo "<td align='center'><a href='ingresos_diarios_caja.php?xcod_prd=".$_SESSION['cod_prd']."&xcod_caja=".$row2['cod_caja']."&xfecha=".$_POST['fecha']."' target='_blank'>". $row2['cod_caja'] ."</a></td>";
						echo "<td align='right'>". number_format($row2['total'],2,',','.') ."</td>";
					        echo "<td align='right'>". number_format($row2['conceptos'],2,',','.') ."</td>";
						echo "<td align='right'>". number_format($row2['efectos_cobro'],2,',','.') ."</td>";
						echo "<td align='center'>". $row2['activos'] ."</td>";
						echo "<td align='center'><a href='lista_rnulos.php?xcod_caja=".$row2['cod_caja']."&xfecha=".$_POST['fecha']."'>". $row2['nulos'] ."</a></td>";
						echo "</tr>";
						$acu5=$acu5+$row2['total'];
						$acu6=$acu6+$row2['activos'];
						$acu7=$acu7+$row2['nulos'];
				}
				echo "<tr>";
				echo "<strong>";
				echo "<td align='right'>Totales...</td>";
				echo "<td align='right'>". number_format($acu5,2,',','.') ."</td>";
				echo "<td align='right'>". number_format($acu6,0,',','.') ."</td>";
				echo "<td align='right'>". number_format($acu7,0,',','.') ."</td>";
				echo "</strong>";
				echo "</tr>";
			}
			echo "<tr><td colspan='4'>";
			if(($acu2-$acu4)==0){
				echo "<div class='alert alert-success'>";
				echo " Operaciones del Día guardan positiva relación entre la sumatoria procesada y soportes (Depositos y Flujo de Caja)";
				echo "</div>";
			}else{
				echo "<div class='alert alert-danger closed'> Diferencia de procesamiento: ". number_format($acu2-$acu4,2,',', '.') ."</div>";
			}
			echo "</td></tr>";
			echo "</table>";
			echo "</td></tr>";
		}
		echo "<tr><td colspan='3' align='center'> ";
		?>
		<strong>
		<input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
		<?php
		echo "<a href='imprimir_cierre_diario2.php?xfecha=".$_POST['fecha']."'>Ver en PDF</a>";
		echo "</strong>";
		echo " </td></tr>";
		echo "</table></center>";
	}
}else{
	echo "Error de Conexión con la Base de Datos";
}
?>
</body>
</html>
