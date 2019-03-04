<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<?php


echo "<div class='form-group'>";
echo "<form method='post' action='php/verifica_solvencia.php' target='contenido1'";
echo "<center>";
echo "	<table>";
echo "<tr><th colspan='2'>Ingrese Cédula del Contribuyente</th></tr>";
echo "<tr><td><input type='text' class='form-control' name='cedula' id='cedula 'size='12' placeholder='V-16233325' required='required' /></td>";
echo '<td> <button type="submit" class="btn btn-success">Consultar</button> </td></tr>';
echo "	</table>";
echo "</center>";
echo "</form>";
echo "</div>";

echo "<iframe name='contenido1' id='contenido1' width='100%' height='550'></iframa>";
?>