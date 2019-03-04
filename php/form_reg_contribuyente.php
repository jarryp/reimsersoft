<!--
''Software Desarrollado por Palacios Systems
''Consulta Web REIMSER SOFT
''Contacto: jarry.palacios@hotmail.com / 0416-1738065 
''Portal Web: www.palaciossystems.com / jpalacios@palaciossystems.com
-->
<!DOCTYPE HTML>
<html lang="es">
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
  <script type="text/javascript">

  function pasar_cod(xcod){
   document.getElementById("text1").value=xcod
   buscar_contri();
  }


  function listar_contri(){
    var xcad = document.getElementById("b_contri").value;
    if(xcad.length>3){
      $.ajax({
          type:"POST",
          url:"php/contri_listar.php",
          data: {"codigo":document.getElementById("b_contri").value},
          success: function(data){
              $("#area_listarcontri").html(data);
          }
      });     
    }else{
      alert("Ingrese Cadena de Busqueda");
    }
  }
  

    function buscar_contri(){
      var xcod = document.f_contri.text1.value;
      if(xcod.length>0){
      $.ajax({
        type:"POST",
        url:"php/contri_buscar.php",
        data:{"codigo":document.f_contri.text1.value},
        success: function(data){
          var valor = data.split("#")
          if(valor[0]!="Nada"){
          document.f_contri.cmb_tipo.value=valor[0];
          document.f_contri.text2.value=valor[2];
          document.f_contri.text3.value=valor[3];
          document.f_contri.text4.value=valor[4];
          document.f_contri.text5.value=valor[5];
          document.f_contri.text6.value=valor[6];
          document.f_contri.text7.value=valor[7]; 
          obligaciones();
        }else{
          alert("Código no Registrado en la Base de Datos");
        }
      }
      });
    }
  }

  
  function obligaciones(){
    $.ajax({
      type:"POST",
      url:"php/contri_obligaciones.php",
      data:{"codigo": document.f_contri.text1.value},
      success:function(data){
        $("#area_obligaciones").html(data);
      }
      });
  }
  </script>
</head>
<body>
<?php 
  require_once "procedimientos.php"; 
?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<table>
  <tr>
    <td valign="center" align="center">
  <br>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3>Registro de Contribuyentes</h3>
    </div>
      <div class="panel-body">

      <div class="form-group">
      <form id="f_contri" name="f_contri" method="post" action="php/contri_graba.php">
        <table bgcolor="#EFFBFB" id='tabla1' name='tabla1'>
          <tr>
            <td width="30%" align="right">Tipo de Contribuyente:-</td>
            <td width="70%">
              <select id="cmb_tipo" name="cmb_tipo">
              <option>Seleccione...</option>
                <?php
                session_start();
                $conn=conectarpg();
                if($conn){
                  $query="select * from tip_contri order by des_tpc";
                  $result=pg_query($query);
                  if($result){
                    while ($row=pg_fetch_assoc($result)) {
                      echo "<option>". $row['des_tpc'] ."</option>";
                    }
                  }
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td align="right">Cédula / RIF:- </td><td> <input id='text1' name='text1' class="form-control" type="text" size="12" maxlength="12" required onblur="buscar_contri()" /> </td>
          </tr>
          <tr>
            <td align="right">Nombres y Apellidos:- </td>
            <td> <input id='text2' name='text2' class="form-control" type="text" size="50" maxlength="50" required /> </td>
          </tr>
          <tr>
            <td align="right">NIT:- </td>
            <td> <input id='text3' name='text3' class="form-control" type="text" size="12" maxlength="12" /> </td>
          </tr>
          <tr>
            <td align="right">Email:- </td>
            <td> <input id='text4' name='text4' type="email" class="form-control" size="80" maxlength="80" placeholder="ejemplo@dominio.com" /> </td>
          </tr>
          <tr>
            <td align="right">Telefono Residencial:- </td>
            <td> <input id='text5' name='text5' type="text5" class="form-control" size="15" maxlength="15" placeholder='0276-7628833' /> </td>
          </tr>
          <tr>
            <td align="right">Telefono Celular:- </td>
            <td> <input id='text6' name='text6' type="text6" class="form-control" size="15" maxlength="15" placeholder='0416-1738065' /> </td>
          </tr>
          <tr>
            <td align="right">Dirección:- </td>
            <td>
              <textarea id='text7' name='text7' rows="4" cols="60" class="form-control" placeholder='Ingrese su dirección exacta'></textarea>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="right"> <button type="submit" class="btn btn-primary">Registrar</button>  </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <div id='area_obligaciones' name='area_obigaciones'>
                
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" id="b_contri" name="b_contri" size="50" placeholder="Ingrese cadena de busqueda >3 caracteres (Nombre ó Cèdula)" />
              <input type="button" id="boton1" value="Buscar" class="btn btn-success" onclick="listar_contri();" />
            </td>
          </tr>
          <tr>
            <td colspan="2">
            <center>
            <table>
            <tr>
            <td width="93%" align="center">
              <div class="row">
                <div>
                  <!-- <input type="text" id="b_contri" name="b_contri" size="40" />
                  <input type="button" id="boton1" value="Buscar" onclick="listar_contri();" /> -->
                  <div id="area_listarcontri" class="row">
      
                  </div>
                </div>
            </div>
            </td>
            </tr>
            </table>
            </center>
            </td>
          </tr>
        </table>
        </form>  
      </div>  
    </td>
  </tr>
</table>




</body>
</html>