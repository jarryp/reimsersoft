<!DOCTYPE HTML>
<html lang="es">
<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<head>
  <meta charset="UTF-8" >
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
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){
  $query="select * from contribuyente where cod_contri='". $_POST['text1'] ."'";
  $result=pg_query($query);
  if($result){
    if(pg_num_rows($result)>0){
      $x_op="2";
    }else{
      $x_op="1";
    }
    ?>
    <div class="row">
    <center>
    <table width="65%">
    <tr>
    <td>
    <div class="panel panel-success">
      <div class="panel-heading">
      <img src="../img/logos/banner.png" width="97%" height="85">
        <h3>
          <?php
           if($x_op=="1"){
              echo "Registro de Datos Básicos del Contribuyente";
           }
           if($x_op=="2"){
              echo "Actualización de Datos Básicos del Contribuyente";
           }
          ?>
        </h3>
        <div class="panel-body">
        <table bgcolor="#EFFBFB" border="3">
          <tr>
            <td> Tipo: </td>
            <td> <?php echo $_POST['cmb_tipo']; ?> </td>
          </tr>
          <tr>
            <td>
              Cédula /RIF: 
            </td>
            <td>
              <?php echo $_POST['text1']; ?>
            </td>
          </tr>
          <tr>
            <td> Contribuyente: </td>
            <td> <?php echo $_POST['text2']; ?> </td>
          </tr>
          <tr>
            <td> NIT: </td>
            <td> <?php echo $_POST['text3']; ?> </td>
          </tr>
          <tr>
            <td> Correo Electronico:  </td>
            <td> <?php echo $_POST['text4']; ?> </td>
          </tr>
          <tr>
            <td> Telefono Residencial:  </td>
            <td> <?php echo $_POST['text5']; ?> </td>
          </tr>
          <tr>
            <td> Telefono Celular: </td>
            <td> <?php echo $_POST['text6']; ?> </td>
          </tr>
          <tr>
            <td> Dirección: </td>
            <td> <?php echo $_POST['text7']; ?> </td>
          </tr>
          <tr>
            <td colspan="2" align="right"> <a href="../principal.php"> Regresar </a> </td>
          </tr>
        </table>
        </div>
      </div>
    </div>
    </td>
    </tr>
    </table>
    </center>
    </div>
    <?php
    session_start();
    $cod_tpc="";
    $query="select cod_tpc from tip_contri where des_tpc='".$_POST['cmb_tipo']."'";
    $result=pg_query($query);
    if ($result) {
      $row=pg_fetch_assoc($result);
      $cod_tpc=$row['cod_tpc'];
    }
    
    if($x_op==1){
      $query="insert into contribuyente (cod_contri,nom_contri,tel_contri,nit_contri,dir_contri,email_contri,tip_contri,tcl_contri,cod_usu,fecha)"
      ." values ('". $_POST['text1'] ."','". $_POST['text2'] ."','". $_POST['text5'] ."','". $_POST['text3'] ."','". $_POST['text7'] ."','". $_POST['text4'] ."','". $cod_tpc ."','". $_POST['text6'] ."','". $_SESSION['cod_usu'] ."',now())";
    if($result=pg_query($query)){
        echo "<div class='alert alert-info'>";
        echo " Registro Agregado Correctamente a la Base de Datos";
        echo "</div>";
    }  
    }

    if($x_op==2){
      $query="update contribuyente set nom_contri='". $_POST['text2'] ."', tel_contri='". $_POST['text5'] ."' ,"
      ." email_contri='". $_POST['text4'] ."', nit_contri='". $_POST['text3'] ."', dir_contri='". $_POST['text7'] ."' ,"
      ." tcl_contri='". $_POST['text6'] ."', cod_usu='". $_SESSION['cod_usu'] ."', fecha=now() "
      ." where cod_contri='". $_POST['text1'] ."'";
      if($result=pg_query($query)){
        echo "<div class='alert alert-info'>";
        echo " Registro Actualizado Correctamente a la Base de Datos";
        echo "</div>";
      }
    }
  
  }
}else{
  echo "<br>Error de Conexión a Base de Datos...";
}


?>
</body>
</html>