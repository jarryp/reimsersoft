<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<?php


echo "<div class='form-group'>";
echo "<form method='post' action='php/ingresos_diarios.php' target='contenido1'";
echo "<center>";
echo "	<table>";
echo "<tr><th colspan='2'>Ingrese Fecha a Consultar</th></tr>";
echo "<tr><td><input type='date' name='fecha' id='fecha 'size='12' placeholder='yyyy-mm-dd' required='required' value='".date('Y-m-d')."' /></td>";
echo '<td> <button type="submit" class="btn btn-success">Consultar</button> </td></tr>';
echo "	</table>";
echo "</center>";
echo "</form>";
echo "</div>";

echo "<iframe name='contenido1' id='contenido1' width='100%' height='1450'></iframa>";
?>
