<?php
require_once "procedimientos.php";
$conn=conectarpg();
$query="select * from usuario where log_usu='". $_GET['xlog'] ."' and pwd_usu='". $_POST['xpwd'] ."'";
$result=pg_query($query);
if($result){
	$row=pg_fetch_assoc($result);
	if(pg_num_rows($result)>0){
		session_start();
		$_SESSION['cod_usu']=$row['cod_usu'];
		$_SESSION['nom_usu']=$row['nom_usu'];
		$_SESSION['cod_ente']=$_GET['xcod_ente'];
		$_SESSION['cod_prd']=$_GET['xcod_prd'];
		echo "pasa#".$row['nom_usu'];
	}else{
		?>
			<div class="alert alert-warning alert-dismissable">
  				<button type="button" class="close" data-dismiss="alert">&times;</button>
  				<strong>¡Acceso Negado!</strong> Verifique datos de validación de acceso.
			</div>
		<?php
	}
}
?>