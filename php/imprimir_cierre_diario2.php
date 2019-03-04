<?php
session_start();
require_once "procedimientos.php";
require_once "../html2pdf/html2pdf.class.php";
error_reporting(E_ALL ^ E_NOTICE);
//setlocale(LC_ALL,"es_ES");
setlocale(LC_TIME, 'spanish');

if($conn=conectarpg()){
$html=" <page backtop='20mm' backbottom='10mm' backleft='20mm' backright='20mm'>

<page_header>
    <table style='width: 100%;'>
        <tr>
            <td style='text-align: center;    width: 95%'>
                <img src='../img/logos/banner.jpg' width='700' height='60' />
            </td>
        </tr>
        <tr>
         <td style='text-align: center;    width: 95%'>
         <strong>DIRECCIÓN DE HACIENDA MUNICIPAL</strong>
         </td>
        </tr>
    </table>
</page_header>


<page_footer>
    <table style='width: 100%; border: solid 1px black;''>
        <tr>
            <td style='text-align: left;    width: 50%'>
            <a href='http://www.palaciossystems.com'>http://www.palaciossystems.com</a>
            </td>
            <td style='text-align: right;    width: 50%'>".date('d/m/Y')." pagina [[page_cu]]/[[page_nb]]</td>
        </tr>
    </table>
</page_footer>
       <div align='center'>
          <h3>CIERRE DIARIO ".$_GET['xfecha']."</h3>
        </div>
        <div align='center'>
        <p align='center'>
        <strong>Liquidación por Conceptos (Tributos - Tasas)</strong>
        <table width='90%' border='1'  cellspacing='0' cellpadding='2' bordercolor='1'>
        <tr>
          <td align='center'><strong>Cod_Presupuesto</strong></td>
          <td align='center'><strong>Concepto</strong></td>
          <td align='center'><strong>Nro. Recibos </strong></td>
          <td align='center'><strong>Monto Recaudado (Bs.)</strong></td>
        </tr>";
        $query="select c.cod_spa, c.des, sum(r.neto) as total, count(*) as nro "
        ." from recb_deta_1 r"
        ." left join concepto c on c.cod=r.cod_c "
        ." left join recibos re on re.n_recibo=r.n_recibo "
        ." where r.cod_prd='".$_SESSION['cod_prd']."' and re.fecha='".$_GET['xfecha']."' and re.est='A'"
        ." group by c.cod_spa, c.des "
        ." order by c.cod_spa, c.des ";
        if($result=pg_query($query)){
         while($row=pg_fetch_assoc($result)){
           $html.="<tr>
                      <td> ".$row['cod_spa']." </td>
                      <td> ".ucfirst(strtolower($row['des']))." </td>
                      <td align='right'> ".$row['nro']." </td>
                      <td align='right'> ".number_format($row['total'],2,',', '.')." </td>
                   </tr>";
          $acu1=$acu1+$row['nro'];
          $acu2=$acu2+$row['total'];
         }
        $html.= "<tr>
     		<td colspan='2'  align='right'><strong>Totales.....</strong></td>
     		<td align='right'><strong>". $acu1 ."</strong></td>
     		<td align='right'><strong>". number_format($acu2,2,',', '.') ."</strong></td>
     		</tr>
        </table>
        <br>
           <strong>Resumen de Bancos</strong>

         <table width='100%' border='1'  cellspacing='0' cellpadding='2' bordercolor='1'>
         <tr>
           <td><strong>Banco</strong></td>
           <td><strong>Nro. de Cuenta</strong></td>
           <td><strong>Cantidad</strong></td>
           <td><strong>Monto</strong></td>
         </tr>
            ";

            $query="select distinct nom_ban, num_cuen, sum(monto) as total, count(*) as cant"
        		." from vista_04"
        		." where fecha='". $_GET['xfecha'] ."'"
        		." group by nom_ban, num_cuen"
        		." order by nom_ban, num_cuen";

        		if($result1=pg_query($query)){
              while($row=pg_fetch_assoc($result1)){
                $acu3=$acu3+$row['cant'];
        				$acu4=$acu4+$row['total'];
                $html.="<tr>
                         <td>". $row['nom_ban'] ."</td>
                         <td>". $row['num_cuen'] ."</td>
                         <td>". $row['cant'] ."</td>
                         <td align='right'>". number_format($row['total'],2,',', '.') ."</td>
                        </tr>";
              }
              $html.="<tr>
                         <td colspan='2' align='right'><strong>Totales...</strong></td>
                         <td align='right'><b>". $acu3 ."</b></td>
                         <td align='right'><b>". number_format($acu4,2,',', '.') ."</b></td>
                      </tr>";
            }

            $html.="</table>
            <br>
                  <strong> Resumen por Cuentas y Formas de Pago</strong>
                        <table border='1'  cellspacing='0' cellpadding='2' bordercolor='1'>
                         <tr>
                           <td><strong>Banco</strong></td>
                           <td><strong>Nro. de Cuenta</strong></td>
                           <td><strong>Forma de Pago</strong></td>
                           <td><strong>Cant (Uni)</strong></td>
                           <td><strong>Monto Liquidado</strong></td>
                         </tr>";
          $query="select distinct nom_ban, num_cuen, nom_fp, sum(monto) as total, count(*) as cant
        		 from vista_04
        		 where fecha='".$_GET['xfecha']."'
        		 group by nom_ban, num_cuen, nom_fp
        		 order by nom_ban, num_cuen, nom_fp ";
             if($result3=pg_query($query)){
               $acu8=0;
               $acu9=0;
               while ($row=pg_fetch_assoc($result3)) {
                 $acu8=$acu8+$row['cant'];
                 $acu9=$acu9+$row['total'];
                 $html.="<tr>
                           <td>".$row['nom_ban']."</td>
                           <td>".$row['num_cuen']."</td>
                           <td>".$row['nom_fp']."</td>
                           <td align='right'>".$row['cant']."</td>
                           <td align='right'>".number_format($row['total'],2,',', '.')."</td>
                         </tr>";
               }
               $html.="<tr>
                          <td colspan='3' align='right'><strong>Totales...</strong></td>
                          <td align='right'><strong>".$acu8."</strong></td>
                          <td align='right'><strong>".number_format($acu9,2,',', '.')."</strong></td>
                       </tr>";
             }
                     $html.="</table>
                     <br>
                    <strong>Resumen por Cajas</strong>
                      <table border='1'  cellspacing='0' cellpadding='2' bordercolor='1'>
                        <tr>
                          <td><strong>Nro. Caja</strong></td>
                          <td><strong>Recibos Activos</strong></td>
                          <td><strong>Recibos Nulos</strong></td>
                          <td><strong>Total Procesado (Bs.)</strong></td>
                        </tr>";

                        $query="select a.*,"
                         				." (select count(*) from recibos "
                         				."  where est='A' and fecha='". $_GET['xfecha'] ."' and cod_caja=a.cod_caja) as activos,"
                         				."  (select count(*) from recibos "
                         				."    where est='N' and fecha='". $_GET['xfecha'] ."' and cod_caja=a.cod_caja) as nulos"
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
                  					." where r.fecha='". $_GET['xfecha'] ."') as a"
                  					." group by cod_caja"
                  					." order by cod_caja) as a";
                      if($result2=pg_query($query)){
                        $acu5=0;
                        $acu6=0;
                        $acu7=0;
                        while($row=pg_fetch_assoc($result2)){
                          $html.="<tr>
                                    <td>".$row['cod_caja']."</td>
                                    <td align='right'>".$row['activos']."</td>
                                    <td align='right'>".$row['nulos']."</td>
                                    <td align='right'>".number_format($row['total'],2,',','.')."</td>
                                  </tr>";

                                  $acu5=$acu5+$row['total'];
                      						$acu6=$acu6+$row['activos'];
                      						$acu7=$acu7+$row['nulos'];
                        }
                      }

          $html.=" <tr>
                    <td align='right'><b>Totales...</b></td>
                    <td align='right'><b>".$acu6."</b></td>
                    <td align='right'><b>".$acu7."</b></td>
                    <td align='right'><b>".number_format($acu5,2,',','.')."</b></td>
                   </tr>
                      </table>";
        }

      $html.="
              </p>
              </div>
              </page>";

        try {
         $pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 3); // Creamos una instancia de la clase HTML2FPDF
         $pdf -> pdf->SetDisplayMode('fullpage');
         $pdf -> WriteHTML($html);//Volcamos el HTML contenido en la variable $html para crear el contenido del PDF
         $pdf -> Output('cierre_diario_'.$_GET['xfecha'].'.pdf', 'I');
       }catch (HTML2PDF_exception $e) {
         echo $e;
         exit;
       }

       //Cierre de Conexión
       pg_close($conn);
//fin de conexión
}else{
  echo "Error de Conexión a la Base de Datos...";
}

?>
