<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once "procedimientos.php";
if($conn=conectarpg()){
      if($_GET['xcontrol_op']=="form_cuentasb"){
        ?>
          <div class="col-lg-8 col-md-8 col-sm-8">
           <div class="panel panel-default">
           <div class="panel-heading">Cuentas Bancarias</div>
           <div class="panel-body">
            <form role="form" class="form-inline">
                <div class="form-group">
                    <div class="col-lg-3"><label>Código</label></div>
                    <input type="text" id="id_cb" maxlength="10" size="10" class="form-control" required />
                    <br>
                    <div class="col-lg-3"><label>Banco</label></div>
                    <select id="cmb_banco" class="form-control">
                        <?php llcmb_bancos(); ?>
                    </select>
                    <br> 
                    <div class="col-lg-3"><label>Nro. Cuenta</label></div>
                    <input type="text" id="ncuenta" size="24" maxlength="24" required class="form-control" >
                    <br>
                    <div class="col-lg-3"><label>Uso:</label></div>
                    <textarea id='t_uso' cols="30" rows="3" class="form-control"></textarea>
                    <br>
                    <center>
                    <button type="button" class="btn btn-primary">Registrar</button>
                    <button type="reset" class="btn btn-success">Cancelar</button>
                    </center>
                    <br>
                    <div class="row" id="listado">
                       <br>
                         <?php lista_cuentasb(); ?> 
                    </div>
                </div>
            </form>
            </div>
            </div>
        </div>
        <?php 
        }
}else{
    echo "Error de Conexión a la Base de Datos";
}

?>
