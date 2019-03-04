<?php
session_start();
require_once "procedimientos.php";
$conn=conectarpg();
if($conn){
$query="	select t.des_tpc, c.cod_contri, c.nom_contri, c.nit_contri,c.email_contri,"
     ."  c.dir_contri,c.tel_contri,c.tcl_contri ,"
     ."   (select count(*) "
     ."   from catast_contri"
     ."   where cod_contri=c.cod_contri) as fichas_catas,"
     ."   (select count(*)"
      ."  from vh_contri"
     ."   where cod_contri=c.cod_contri) as nro_vh,"
      ."  (select count(*)"
     ."    from (select distinct des "
      ."  from vista_03"
      ."  where cod_contri=c.cod_contri"
     ."   group by des) as a) as conceptos"
." from contribuyente c"
 ."       left join tip_contri t on t.cod_tpc=c.tip_contri"
." where c.cod_contri='". $_POST['codigo'] ."'";
	$result=pg_query($query);
	if($result){
		$row=pg_fetch_assoc($result);
		$resultado=trim($row['des_tpc'])."#".trim($row['cod_contri'])."#".trim($row['nom_contri'])."#".trim($row['nit_contri']."#".trim($row['email_contri']))."#".trim($row['tel_contri']."#".$row['tcl_contri']."#".trim($row['dir_contri'])."#".$row['fichas_catas']."#".$row['nro_vh']."#".$row['conceptos']);
		
		if(pg_num_rows($result)>0){
		 echo $resultado;
	    }else{
	    	echo "Nada";
	    }
	}else{
		echo "Nada";
	}
}
?>