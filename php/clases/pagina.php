<?php
    class Pagina {
        
        public $titulo = 'Alcaldía de Miranda del Edo. Caracoco';
        public $colorpag = '#ef2308';
        public $agnoini = 2014;
        
        function get_titulo() {
           echo $this->titulo;
        }
        
        function get_colorpag() {
           echo $this->colorpag;
        }
        
        function get_agno_ini(){
            return $this->agnoini;
        }
    }
?>