<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<?php
require_once "procedimientos.php";
$var=strtoupper($_POST['codigo']);
if($conn=conectarpg()){
	$query="select * from contribuyente where cod_contri like '%". $var ."%'"
	     ." or nom_contri like '%". $var ."%' order by nom_contri";
	$result=pg_query($query);
	if($result){
		echo "Cantidad de Resultados: ". pg_num_rows($result)."<br>" ;
		echo "<table class='table table-striped table-hover'>";
		echo "<tr>";
		echo "<th>Cédula / RIF </th>";
		echo "<th>Contribuyente </th>";
		/*echo "<th>Telefono</th>";
		echo "<th>Email</th>"; */
		echo "</tr>";
		while ($row=pg_fetch_assoc($result)) {
			echo "<tr>";
			?>
			<td onclick="pasar_cod('<?php echo $row['cod_contri']; ?>')"> <?php echo $row['cod_contri']; ?></td>
			<?php
			echo "<td>". $row['nom_contri'] ."</td>";
			/*echo "<td>". $row['tcl_contri'] ."</td>";
			echo "<td>". $row['email_contri'] ."</td>";*/
			echo "</tr>";
		}
		echo "</table>";
	}else{
		echo "Error de Consulta a la Base de Datos";
	}
}else{
	echo "Error de Conexión a la Base de Datos";
}
?>