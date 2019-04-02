<?php
/*
Plugin Name:  KFP Aspirantes
Description:  Formulario para valorar el nivel de partida de los alumnos aspirantes. Utiliza el shortcode [kfp_form_aspirante] para que el formulario aparezca en la página o el post que desees.
Version:      0.1.1
Author:       Juanan Ruiz
Author URI:   https://kungfupress.com/
*/

// El formulario puede insertarse en cualquier sitio con este shortcode
add_shortcode('kfp_form_aspirante', 'kfp_form_aspirante');

function kfp_form_aspirante() {
    global $wpdb;
    // Crea la tabla si no existe
    $tabla_aspirantes = $wpdb->prefix . 'aspirante';
    $charset_collate = $wpdb->get_charset_collate();
    $query = "CREATE TABLE IF NOT EXISTS $tabla_aspirantes (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        correo varchar(100) NOT NULL,
        web varchar(200),
        nivel_html smallint(4) NOT NULL,
        nivel_css smallint(4) NOT NULL,
        nivel_js smallint(4) NOT NULL,
        nivel_php smallint(4) NOT NULL,
        nivel_wp smallint(4) NOT NULL,
        motivacion text,
        created_at datetime NOT NULL,
        UNIQUE (id)
        ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); // ???
    dbDelta( $query ); // ???

    // Si viene del formulario  grabamos en la base de datos
    if( $_POST['nombre'] != '' && is_email($_POST['correo']) 
        && wp_verify_nonce( $_POST['aspirante_nonce'], 'graba_aspirante')) {

        $tabla_aspirantes = $wpdb->prefix . 'aspirante'; //hace falta repetir?
        $nombre = sanitize_text_field($_POST['nombre']);
        $correo = $_POST['correo'];
        $nivel_html = (int)$_POST['nivel_html'];
        $nivel_css = (int)$_POST['nivel_css'];
        $nivel_js = (int)$_POST['nivel_js'];
        $nivel_php = (int)$_POST['nivel_php'];
        $nivel_wp = (int)$_POST['nivel_wp'];
        $motivacion = sanitize_text_field($_POST['motivacion']);
        $created_at = date('Y-m-d H:i:s');

        $wpdb->insert(
            $tabla_aspirantes,
            array(
                'nombre' => $nombre,
                'correo' => $correo,
                'nivel_html' => $nivel_html,
                'nivel_css' => $nivel_css,
                'nivel_js' => $nivel_js,
                'nivel_php' => $nivel_php,
                'nivel_wp' => $nivel_wp,
                'motivacion' => $motivacion,
                'created_at' => $created_at,
            )
        );
        echo "<p class='exito'><b>Tus datos han sido registrados</b>. Gracias por tu interés. En breve contactaré contigo.<p>";
    }
    wp_enqueue_style('css_aspirantes',plugins_url('style.css', __FILE__));
    ob_start();
    ?>
    <form action="<?php get_the_permalink(); ?>" method="post" id="form_aspirante" class="cuestionario">
        <?php wp_nonce_field('graba_aspirante', 'aspirante_nonce'); ?>
        <div class="form-input">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <div class="form-input">
            <label for='correo'>Correo</label>
            <input type="email" name="correo" id="correo" required>
        </div>
        <div class="form-input">
            <label for="nivel_html">¿Cuál es tu nivel de HTML?</label>
            <input type="radio" name="nivel_html" value="1" required> Nada
            <br><input type="radio" name="nivel_html" value="2" required> Estoy aprendiendo
            <br><input type="radio" name="nivel_html" value="3" required> Tengo experiencia
            <br><input type="radio" name="nivel_html" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_css">¿Cuál es tu nivel de CSS?</label>
            <input type="radio" name="nivel_css" value="1" required> Nada
            <br><input type="radio" name="nivel_css" value="2" required> Estoy aprendiendo
            <br><input type="radio" name="nivel_css" value="3" required> Tengo experiencia
            <br><input type="radio" name="nivel_css" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_js">¿Cuál es tu nivel de JavaScript?</label>
            <input type="radio" name="nivel_js" value="1" required> Nada
            <br><input type="radio" name="nivel_js" value="2" required> Estoy aprendiendo
            <br><input type="radio" name="nivel_js" value="3" required> Tengo experiencia
            <br><input type="radio" name="nivel_js" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_php">¿Cuál es tu nivel de PHP?</label>
            <input type="radio" name="nivel_php" value="1" required> Nada
            <br><input type="radio" name="nivel_php" value="2" required> Estoy aprendiendo
            <br><input type="radio" name="nivel_php" value="3" required> Tengo experiencia
            <br><input type="radio" name="nivel_php" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_wp">¿Cuál es tu nivel de WordPress?</label>
            <input type="radio" name="nivel_wp" value="1" required> Nada
            <br><input type="radio" name="nivel_wp" value="2" required> Estoy aprendiendo
            <br><input type="radio" name="nivel_wp" value="3" required> Tengo experiencia
            <br><input type="radio" name="nivel_wp" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="motivacion">¿Porqué quieres aprender a programar en WordPress?</label>
            <textarea name="motivacion" id="motivacion" required></textarea>
        </div>
        <div class="form-input">
            <label for="aceptacion">Mi nombre es Juan Antonio Ruiz Rivas y me 
                comprometo a custodiar de manera responsable los datos que vas 
                a enviar. Su única finalidad es la de participar en el proceso 
                explicado más arriba. 
                En cualquier momento puedes solicitar el acceso, la rectificación 
                o la eliminación de tus datos desde esta página web.</label>
            <input type="checkbox" id="aceptacion" name="aceptacion" required> Entiendo y acepto las condiciones
        </div>
        <div class="form-input">
            <input type="submit" value="Enviar">
        </div>
    </form>
    <?php

    return ob_get_clean();
}

// Aquí comienza la parte administrativa del plugin
add_action("admin_menu", "kfp_crear_menu");
    
function kfp_crear_menu() {
    add_menu_page('Formulario Aspirantes', 'Aspirantes', 'manage_options', 
        'kfp_menu_aspirante', 'admin_panel_aspirantes');
}

function admin_panel_aspirantes(){
    global $wpdb;
    $tabla_aspirantes = $wpdb->prefix . 'aspirante';
    echo '<div class="wrap"><h1>Lista de aspirantes</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th width="30%">Nombre</th><th width="20%">Correo</th><th>HTML</th><th>CSS</th><th>JS</th>
        <th>PHP</th><th>Total</th></tr></thead>';
    echo '<tbody id="the-list">';
    $aspirantes = $wpdb->get_results("SELECT * FROM $tabla_aspirantes");
    foreach ( $aspirantes as $aspirante ) 
    {
        $total = $aspirante->nivel_html + $aspirante->nivel_css + 
            $aspirante->nivel_js + $aspirante->nivel_php;
            $motivacion = esc_textarea($aspirante->motivacion);
            $nombre = esc_textarea($aspirante->nombre);
            echo "<tr><td><a href='#' title='$motivacion'>$nombre</a></td>
            <td>$aspirante->correo</td>
            <td>$aspirante->nivel_html</td><td>$aspirante->nivel_css</td>
            <td>$aspirante->nivel_js</td><td>$aspirante->nivel_php</td>
            <td>$total</td></tr>";
    }
    echo '</tbody></table></div>';
}
