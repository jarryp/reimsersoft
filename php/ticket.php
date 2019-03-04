<!DOCTYPE html>
<html>
<head>
	<meta charset='UTF-8' />
	<title></title>
</head>
<body>
<?php
header("Refresh: 1; URL='ticket.php'");
require_once "procedimientos.php";
echo "<img src='../img/logos/banner.jpg'>";
$conn=conectarpg();
if($conn){
	$query="select * from cajas order by des_caja";
	$result=pg_query($query);
	echo "<table>";
	echo "<tr>";
	echo "<font size='34'><strong>";
	echo "<td><h1>Fecha</h1></td>";
	echo "<td><h1>". date ("h:i:s") ."</h1></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>Hora</td>";
	echo "<td>". date ("d-m-Y") ."</td>";
	echo "</strong></font>";
	echo "</tr>";
	echo "<tr><td colspan='2'>Cubiculo de Atención</td></tr>";
	while ($row=pg_fetch_assoc($result)) {
		echo "<tr>";
		echo "<td>".$row['des_caja']."</td>";
		echo "</tr>";
	}
	echo "</table>";
}


?>
</body>
</html>
