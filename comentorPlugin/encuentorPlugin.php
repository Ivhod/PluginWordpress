<?php 
/**
 * Plugin Name: EncuentorPlugin
 *  Plugin URI: https://comentorP.es
 *  Description: Plugin de prueba
 *  Version: 1.0.0
 * Author: Ivan Hidalgo Dominguez
 */
require_once dirname(__FILE__).'/clases/shortCode.php';


function Activar(){ //Funcion que actua cuando se activa el plugin en WordPress, Primero crea todas las tablas si no existen
    global $wpdb;

    $table_name = $wpdb->prefix . 'encuestas';
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        `EncuestaId` INT NOT NULL AUTO_INCREMENT,
        `Nombre` VARCHAR(45) NULL,
        `ShortCode` VARCHAR(45) NULL,
        PRIMARY KEY (`EncuestaId`)
    );";

    // Ejecutar la consulta SQL para crear la la tabla "encuestas_detalle"
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_name2 = $wpdb->prefix . 'encuestas_detalle';
    $sql2 = "CREATE TABLE IF NOT EXISTS {$table_name2} (
        `DetalleId` INT NOT NULL AUTO_INCREMENT,
        `EncuestaId` INT NULL,
        `Pregunta` VARCHAR(150) NULL,
        `Tipo` VARCHAR(45) NULL,
        PRIMARY KEY (`DetalleId`)
    );";

    // Ejecutar la consulta SQL para crear la tabla "encuestas_respuesta"
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql2);

    $table_name3 = $wpdb->prefix . 'encuestas_respuesta';
    $sql3 = "CREATE TABLE IF NOT EXISTS {$table_name3} (
        `RespuestaId` INT NOT NULL AUTO_INCREMENT,
        `DetalleId` INT NULL,
        `Codigo` VARCHAR(45) NULL,
        `Respuesta` VARCHAR(45) NULL,
        PRIMARY KEY (`RespuestaId`)
    );";

    // Ejecutar la consulta SQL
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql3);
}


function Desactivar(){
    
}

function Borrar(){
    
}

register_activation_hook(__FILE__,'Activar'); //Enlazar funcion de activar con el boton de activar plugin de WORDPRESS

register_deactivation_hook(__FILE__,'Desactivar'); //Enlazar funcion de desactivar el boton de desactivar plugin de WORDPRESS


add_action('admin_menu','CrearMenu'); //Funcion de accion para añadir un menu de administrador

function CrearMenu(){
    add_menu_page(
        'Encuentor', //titulo de la pagina
        'EncuentorPlugin Menu', //Titulo del menu
        'manage_options', //Solo puede ver el menu los admins
        plugin_dir_path(__FILE__).'admin/lista_encuesta.php', //slug
        null, //funcion del contenido
        plugin_dir_url(__FILE__).'admin/img/icono.png', //icono
        '2' //Posicion del plugin en el menu
    );

}

function MostrarContenido(){
    echo "<h1>Contenido de la pagina</h1>";
}


//Enlace de Bootstrap

function IncluirBootstrapJS($hook){
    //echo "<script>console.log('$hook')</script>";
    if($hook != "comentorPlugin/admin/lista_encuesta.php"){
        return;
    }

    wp_enqueue_script('bootstrapJS',plugins_url('admin/bootstrap/js/bootstrap.min.js',__FILE__),array('jquery'));
}
add_action('admin_enqueue_scripts','IncluirBootstrapJS');

//Enlace de Bootstrap CSS

function IncluirBootstrapCSS($hook){
    
    if($hook != "comentorPlugin/admin/lista_encuesta.php"){
        return;
    }

    wp_enqueue_style('bootstrapCSS',plugins_url('admin/bootstrap/css/bootstrap.min.css',__FILE__));
}
add_action('admin_enqueue_scripts','IncluirBootstrapCSS');


//Enlace de JS

function IncluirJS($hook){
    
    if($hook != "comentorPlugin/admin/lista_encuesta.php"){
        return;
    }

    wp_enqueue_script('JsExterno',plugins_url('admin/js/lista_encuesta.js',__FILE__),array('jquery'));
    wp_localize_script('JsExterno','SolicitudesAjax',[
        'url' => admin_url('admin-ajax.php'),
        'seguridad' => wp_create_nonce('seg')
    ]);
}
add_action('admin_enqueue_scripts','IncluirJS');


function EliminarEncuesta(){
    $nonce = $_POST['nonce'];
    if(!wp_verify_nonce($nonce,'seg')){
        die('No tiene permisos');
    }
    $id = $_POST['id'];
    
    global $wpdb;

    $tabla = "{$wpdb->prefix}encuestas";
    $tabla2 = "{$wpdb->prefix}encuestas_detalle";

    $wpdb->delete($tabla,array('EncuestaId'=> $id)); //funcion de wordpress que borra la tabla
    $wpdb->delete($tabla2,array('EncuestaId'=> $id));
    return true;
}

add_action('wp_ajax_peticionEliminar','EliminarEncuesta');


//ShortCode

function imprimirShortCode($atts) {
    $_short= new shortCode;
    //obtener id por parametro
    $id= $atts['id'];
    //Accion del boton
    if(isset($_POST['btnguardar'])){
        var_dump($_POST);
        $listadePreguntas= $_short->ObtenerEncuestaDetalle($id);
        $codigo = uniqid();
        foreach ($listadePreguntas as $key => $value) {
            $idpregunta = $value['DetalleId'];

            if(isset($_POST[$idpregunta])){
                $valortxt= $_POST[$idpregunta];
                $datos = [
                    'DetalleId'=> $idpregunta,
                    'Codigo'=> $codigo,
                    'Respuesta'=> $valortxt
                ];
                $_short->GuardarDetalle($datos);
            }
        }
        return "Encuesta Enviada";
    }
    //Respuesta
    $html = $_short->crearEncuesta($id);
    return $html;
}

add_shortcode("ENC","imprimirShortCode");