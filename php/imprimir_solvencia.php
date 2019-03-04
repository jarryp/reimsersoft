<?php	

require_once "../html2pdf/html2pdf.class.php";
//setlocale(LC_ALL,"es_ES");
setlocale(LC_TIME, 'spanish');

	$html=" <page backtop='10mm' backbottom='7mm' backleft='20mm' backright='10mm'>
	        <div align='center'>
			<img src='../img/logos/banner.jpg' width='650' /><br>
	        </div>
	        <p align='center'>
	        REPUBLICA BOLIVARIANA DE VENEZUELA<br>
	        ALCALDIA DEL MUNICIPIO MIRANDA <br>
	        DIRECCIÓN DE HACIENDA MUNICIPAL <br>
	        MIRANDA ESTADO CARABOBO 
	        </p>
	        <div align='center'>
	        <h3>CERTIFICACIÓN DE SOLVENCIA MUNICIPAL</h3>
	        </div>";

	        $html.= "<p align='right'>";
	        $html.=  "Miranda, ".strftime("%A, %d de %B del %Y");
	        $html.=  "</p>
	        <p align='justify'>
	        LA SUSCRITO, <strong>LCDA. REBECA ZERPA</strong>, TITULAR DE LA CEDULA DE IDENTIDAD N° <b>V-XX-XXX-XXX</b>, ACTUANDO EN SU CARACTER DE 
	        DIRECTORA DE HACIENDA MUNICIPAL DE LA ALCALDIA DEL MUNICIPIO MIRANDA ESTADO CARABOBO, SEGUN RESOLUCION N° XX-XXX 
	        DE FECHA XX DE XXX DE XXXX, PUBLICADA EN GACETA MUNICIPAL N° XX-XXXX; HAGO CONSTAR POR MEDIO DE LA PRESENTE, QUE 
	        EL CIUDADANO (A) / ORGANIZACIÓN: <b>".$_GET['xnom_contri']."</b> TITULAR DE LA CEDULA DE IDENTIDAD / RIF N°: <b>".$_GET['xcod_contri']."</b>";

	        $html.=  "</p></page>";


	       try {
					$pdf = new HTML2PDF(); // Creamos una instancia de la clase HTML2FPDF
					$pdf -> WriteHTML($html);//Volcamos el HTML contenido en la variable $html para crear el contenido del PDF
					$pdf -> Output('solvencia'.$_GET['xcod_contri'].'.pdf', 'I');//Volcamos el pdf generado con nombre 'doc.pdf'. En este caso con el parametro 'D' forzamos la descarga del mismo.
				}catch (HTML2PDF_exception $e) {
					echo $e;
			exit;
}

?>