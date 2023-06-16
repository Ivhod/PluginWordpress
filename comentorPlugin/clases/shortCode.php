<?php 

class shortCode{

    //Obtenemos la id de la encuesta que seleccionemos
    public function ObtenerEncuesta($encuestaid){ 
        global $wpdb;
        $tabla = "{$wpdb->prefix}encuestas";
        $query = "SELECT * FROM $tabla WHERE EncuestaId = $encuestaid";
        $datos = $wpdb->get_results($query,ARRAY_A);

        if(empty($datos)){
            $datos = array(); //por si los datos llegan vacios que lo convierta igual en un array
        }
        return $datos[0];
    }

    //todos las preguntas que se envien
    public function ObtenerEncuestaDetalle($encuestaid){ 
        global $wpdb;
        $tabla = "{$wpdb->prefix}encuestas_detalle";
        $query = "SELECT * FROM $tabla WHERE EncuestaId = $encuestaid";
        $datos = $wpdb->get_results($query,ARRAY_A);

        if(empty($datos)){
            $datos = array(); //por si los datos llegan vacios que lo convierta igual en un array
        }
        return $datos;
    }

    //codigo de formulario principio
    public function principioForm($titulo){ 
        $html = "
        <div class='wrap'>
            <h4>$titulo</h4>
            <br>
            <form method='POST'>
        
        ";

        return $html;
    }

    //codigo del cierre del formulario
    public function finalForm(){ 
        $html = "
            <br>
                <input type='submit' id='btnguardar' name='btnguardar' class='page-tittle-action' value='Enviar'>
            </form>
            </div>
        ";

        return $html;
    }

    function input($detalleid,$pregunta,$tipo){
        $html = "";
        if($tipo == 1){
            $html = "
            <div class='form-group'>
            <p><b>$pregunta</b></p>
            <div class='col-sm-8'>
                <select class='form-control' id='$detalleid' name='$detalleid'>
                    <option value='SI'>SI</option>
                    <option value='NO'>NO</option>
                </select>
            </div>
            ";
        }elseif($tipo == 2){

        }else{

        }
        return $html;
    }

    //todo el codigo del medio
    function crearEncuesta($encuestaid){
        $enc= $this->ObtenerEncuesta($encuestaid);
        $nombre= $enc['Nombre'];

        //obtenemos todas las preguntas
        $preguntas="";
        $listaPreguntas = $this->ObtenerEncuestaDetalle($encuestaid);
        foreach($listaPreguntas as $key => $value){
            $detalleid = $value['DetalleId'];
            $pregunta = $value['Pregunta'];
            $tipo = $value['Tipo'];
            $encid = $value['EncuestaId'];

            if($encid==$encuestaid){
                $preguntas .= $this->input($detalleid,$pregunta,$tipo);
            }
        }
        $html = $this->principioForm($nombre);
        $html .= $preguntas;
        $html .= $this->finalForm();

        return $html;
    }
    
    function GuardarDetalle($datos){

        global $wpdb;
        $tabla = $tabla="{$wpdb->prefix}encuestas_respuesta";
        $respuesta = $wpdb->insert($tabla,$datos);
        return $respuesta;
    }

}



?>