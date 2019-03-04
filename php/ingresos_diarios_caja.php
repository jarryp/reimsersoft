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
  <meta charset="UTF-8" >
</head>
<body>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>

<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once "procedimientos.php";
if($conn=conectarpg()){
$query="select r.n_recibo, r.fecha,r.cod_contri, trim(c.nom_contri) as nom_contri, est, cod_caja, "
." case est"
." when 'A' then neto"
." when 'N' then 0"
." end as total,"
." (select sum(d.neto) from recb_deta_1 d left join recibos rec on rec.n_recibo=d.n_recibo  "
." where rec.est='A' and d.n_recibo=r.n_recibo and d.cod_prd='".$_GET['xcod_prd']."') as conceptos, "
." (select sum(monto) from recb_deta_2 d where d.n_recibo=r.n_recibo) as efectos_cobro "
." from recibos r"
." left join contribuyente c on c.cod_contri=r.cod_contri"
." where r.fecha='". $_GET['xfecha'] ."' and cod_caja='". $_GET['xcod_caja'] ."'"
." order by n_recibo";
$result=pg_query($query);
  if($result){
    echo "<table id='tabla1' name='tabla1' class='table table-striped table-hover table-bordered'>";
    echo "<tr class='success'><td colspan='9'><strong>Reporte Liquidación CAJA #: ". $_GET['xcod_caja'] ."  Fecha:  ". $_GET['xfecha'] ."</strong></td></tr>";
    echo "<tr bgcolor='lightsteelblue'>";
    echo "<th>N. Recibo</th>";
    echo "<th>Fecha</th>";
    echo "<th>Cédula/RIF</th>";
    echo "<th>Contribuyente</th>";
    echo "<th>Estado</th>";
    echo "<th>Monto</th><th>Conceptos</th><th>Efectos</th>";
    echo "<th>Diferencias</th>";
    echo "</tr>";
    $cont=0;
    $acu1=0;
    $acu2=0;
    while ($row=pg_fetch_assoc($result)) {
      $cont++;
      $acu1=$acu1+$row['total'];
      $query="select sum(monto) as monto from vista_04 where n_recibo='". $row['n_recibo'] ."'";
       $result1=pg_query($query);
       if($result1){
          $row1=pg_fetch_assoc($result1);
          $diferencia = number_format($row['total']-$row1['monto'],2,',','.');
          $acu2=$acu2+($row['total']-$row1['monto']);
       }else{
        $diferencia=0;
       }
      if( $row['total']==$row['conceptos'] && $row['total']==$row['efectos_cobro'] ){

				if($row['est']=="N"){
					echo "<tr class='warning'>";
				}else{
					echo "<tr>";
				}
      }else{
       echo "<tr class='danger'>";
      }
      echo "<td><font size='2'>". $row['n_recibo'] ."</font></td>";
      echo "<td><font size='2'>". $row['fecha'] ."</font></td>";
      echo "<td><font size='2'>". $row['cod_contri'] ."</font></td>";
      echo "<td><font size='2'>". $row['nom_contri'] ."</font></td>";
      echo "<td>". $row['est'] ."</td>";
      echo "<td align='right'>". number_format($row['total'],2,',','.') ."</td>";
      echo "<td align='right'>". number_format($row['conceptos'],2,',','.') ."</td>";
      echo "<td align='right'>". number_format($row['efectos_cobro'],2,',','.') ."</td>";
      echo "<td align='right'>".$diferencia."</td>";
      echo "</tr>";
    }
    echo "<tr>";
    echo "<td align='center' colspan='5' align='center'><strong> Cantidad de Recibos: ". $cont ." / Activos: ". $_GET['xnr_act'] ." / Nulos: ". $_GET['xnr_nulos'] ."</strong></td>";
    echo "<td align='right'><strong>". number_format($acu1,2,',','.') ."</strong></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td align='right'><strong>". number_format($acu2,2,',','.') ."</strong></td>";
    echo "</tr>";
    $query="select distinct nom_ban, num_cuen, sum(monto) as total, count(*) as cant"
    ." from vista_04"
    ." where fecha='". $_GET['xfecha'] ."' and cod_caja='".$_GET['xcod_caja']."'"
    ." group by nom_ban, num_cuen"
    ." order by nom_ban, num_cuen";
    $result2=pg_query($query);
    if($result2){
      //se crea linea en la tabla principal para agregar resumen de bancos por caja
      echo "<tr>";
      echo "<td colspan='7' align='center'>";
        //se crear tabla que contiene resumen de bancos por caja
          echo "<table class='table table-hover'>";
          echo "<tr><th colspan='6'>Resumen de Bancos</th></tr>";
          echo "<tr bgcolor='lightsteelblue'>";
          echo "<th> Banco</th>";
          echo "<th> Cuenta</th>";
          echo "<th> Monto</th>";
          echo "<th> Nro. Comprobantes</th>";
          echo "</tr>";
          while ($row2=pg_fetch_assoc($result2)) {
            echo "<tr>";
            echo "<td>". $row2['nom_ban'] ."</td>";
            echo "<td><a href='depositos_diarios_caja.php?xnum_cuen=".$row2['num_cuen']."&xfecha=".$_GET['xfecha']."&xcod_caja=".$_GET['xcod_caja']."'>". $row2['num_cuen'] ."</a></td>";
            echo "<td align='right'>". number_format($row2['total'],2,',','.') ."</td>";
            echo "<td align='center'>". $row2['cant'] ."</td>";
            echo "</tr>";
          }
            echo "<tr>";
            echo "<td colspan='6'>  <a href='depositos_diarios_caja2.php?xfecha=".$_GET['xfecha']."&xcod_caja=".$_GET['xcod_caja']."'>Ver todas las cuentas bancarias</a>  </td>";
            echo "</tr>";
          echo "</table>";
        //fin se crear tabla que contiene resumen de bancos por caja
      echo "</td>";
      echo "</tr>";
      //fin se crea linea en la tabla principal para agregar resumen de bancos por caja
    }
    echo "<tr><td colspan='5' align='center'> ";
    ?>
    <strong>
    <input type="button" onclick="tableToExcel('tabla1', 'Resumen de Ingresos Diario')" value="Exportar a Excel">
    </strong>
    <?php
    echo " </td></tr>";
    echo "<table>";
  }
}else{
  echo "Error de Conexión a la Base de Datos...";
}
?>
</body>
</html>
