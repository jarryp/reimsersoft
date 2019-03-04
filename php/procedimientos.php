<?php

function conectarpg()
   {

    /////////// VARIABLES DE CONECCION

    $server   = "localhost";
    $puerto   = "5432";
    $database = "bd_ingre_junin_20181125"; //"bd_ingre_alcjunin"; //  //"bd_ingre";
    $usuario  = "postgres";
    $clave    = "123456";  //"@Lc@lD1A2107";

    /////////// CREA LA CONEXION A POSTGRESQL

    $link = pg_connect("host=$server port=$puerto dbname=$database
user=$usuario password=$clave");

    /////////// SI EXISTE ALGUN EMITE EL ERROR MEDIANTE UN MENSAJE

    if (!$link)
     {
      print("<script languaje=javascript> alert('Error al conectar al
servidor : $server; en la base de datos : $database'); </script>");
error_reporting(E_ALL ^ E_NOTICE);
      exit;
     }

    return $link;
   }

    function llena_cmb_agnos(){
        $agno_ini=2014;
        for($i=date("Y");$i>=$agno_ini;$i--){
            echo "<option value='".$i."'>".$i."</option>";
        }
    }


    function llcmb_bancos(){
        $sql="select cod_ban, nom_ban from banco order by nom_ban";
        echo "<option value='XSLE'>Seleccione...</option>";
        if($result=pg_query($sql)){
            while($row=pg_fetch_assoc($result)){
                echo "<option value='".$row['cod_ban']."'>".$row['nom_ban']."</option>";
            }
        }
    }

    function lista_cuentasb(){
        $sql="select b.nom_ban, c.cod_cuen, c.num_cuen, c.usu_cuen
             from cuentas_b c
             left join banco b on b.cod_ban=c.cod_ban
             order by b.nom_ban, c.num_cuen";
        if($result=pg_query($sql)){
            echo "<table class='table table-hover table-bordered' width='100%'>
                  <tr bgcolor='lightsteelblue'>
                  <th>Banco</th><th>Código</th><th>Numero de Cuenta</th><th>Uso</th><th>Acción</th>
                  </tr>";
            while($row=pg_fetch_assoc($result)){
                $codigo=$row['cod_cuen'];
                echo "<tr>
                        <td>".$row['nom_ban']."</td>
                        <td>".$row['cod_cuen']."</td>
                        <td>".$row['num_cuen']."</td>
                        <td>".$row['usu_cuen']."</td>
                        <td align='center'>";
                ?>
                         <a href="javascript:busc_cuentasb('<?php echo trim($codigo); ?>')" title='Editar...'>
                            <img src='img/iconos/png/base-de-datos.png' />
                         </a>
                         <a href="javascript:elm_cuentasb('<?php echo trim($codigo); ?>')" title="Eliminar...">
                            <img src='img/iconos/png/error.png' />
                         </a>
                <?php echo "
                        </td>
                     </tr>";
            }
            echo "</table>";
        }
    }


   ?>
