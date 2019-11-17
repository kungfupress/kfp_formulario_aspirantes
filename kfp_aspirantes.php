<?php
/**
 * Plugin Name:  KFP Aspirantes
 * Description:  Formulario para valorar el nivel de partida de los alumnos aspirantes. Utiliza el shortcode [kfp_aspirante_form] para que el formulario aparezca en la página o el post que desees.
 * Version:      0.1.1
 * Author:       Juanan Ruiz
 * Author URI:   https://kungfupress.com/
 * PHP Version:  5.6
 *
 * @category Form
 * @package  KFP
 * @author   Juanan Ruiz <juananruizrivas@gmail.com>
 * @license  GPLv2 http://www.gnu.org/licenses/gpl-2.0.txt
 * @link     https://kungfupress.com
 */

// Cuando el plugin se active se crea la tabla del mismo si no existe
register_activation_hook(__FILE__, 'Kfp_Aspirante_init');

/**
 * Realiza las acciones necesarias para configurar el plugin cuando se activa
 *
 * @return void
 */
function Kfp_Aspirante_init()
{
    global $wpdb; // Este objeto global nos permite trabajar con la BD de WP
    // Crea la tabla si no existe
    $tabla_aspirantes = $wpdb->prefix . 'aspirante';
    $charset_collate = $wpdb->get_charset_collate();
    $query = "CREATE TABLE IF NOT EXISTS $tabla_aspirantes (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        correo varchar(100) NOT NULL,
        nivel_html smallint(4) NOT NULL,
        nivel_css smallint(4) NOT NULL,
        nivel_js smallint(4) NOT NULL,
        nivel_php smallint(4) NOT NULL,
        nivel_wp smallint(4) NOT NULL,
        motivacion text,
        aceptacion smallint(4) NOT NULL,
        ip varchar(300),
        created_at datetime NOT NULL,
        UNIQUE (id)
        ) $charset_collate;";
    // La función dbDelta que nos permite crear tablas de manera segura se
    // define en el fichero upgrade.php que se incluye a continuación
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query);
}

// El formulario puede insertarse en cualquier sitio con este shortcode
// El código de la función que carga el shortcode hace una doble función:
// 1-Graba los datos en la tabla si ha habido un envío desde el formulario
// 2-Muestra el formulario

add_shortcode('kfp_aspirante_form', 'Kfp_Aspirante_form');

/**
 * Crea y procesa el formulario que rellenan los aspirantes
 *
 * @return string
 */
function Kfp_Aspirante_form()
{
    global $wpdb; // Este objeto global nos permite trabajar con la BD de WP
    // Si viene del formulario  grabamos en la base de datos
    if (!empty($_POST)
        && $_POST['nombre'] != ''
        && is_email($_POST['correo'])
        && $_POST['nivel_html'] != ''
        && $_POST['nivel_css'] != ''
        && $_POST['nivel_js'] != ''
        && $_POST['nivel_php'] != ''
        && $_POST['nivel_wp'] != ''
        && $_POST['aceptacion'] == '1'
    ) {
        $tabla_aspirantes = $wpdb->prefix . 'aspirante';
        $nombre = sanitize_text_field($_POST['nombre']);
        $correo = $_POST['correo'];
        $nivel_html = (int) $_POST['nivel_html'];
        $nivel_css = (int) $_POST['nivel_css'];
        $nivel_js = (int) $_POST['nivel_js'];
        $nivel_php = (int) $_POST['nivel_php'];
        $nivel_wp = (int) $_POST['nivel_wp'];
        $motivacion = sanitize_text_field($_POST['motivacion']);
        $aceptacion = (int) $_POST['aceptacion'];
        $ip = Kfp_Obtener_IP_usuario();
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
                'aceptacion' => $aceptacion,
                'ip' => $ip,
                'created_at' => $created_at,
            )
        );
        echo "<p class='exito'><b>Tus datos han sido registrados</b>. Gracias
            por tu interés. En breve contactaré contigo.<p>";
    }
    // Carga esta hoja de estilo para poner más bonito el formulario
    wp_enqueue_style('css_aspirante', plugins_url('style.css', __FILE__));
    ob_start();
    ?>
    <form action="<?php get_the_permalink();?>" method="post" id="form_aspirante"
        class="cuestionario">
        <?php wp_nonce_field('graba_aspirante', 'aspirante_nonce');?>
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
            <br><input type="radio" name="nivel_html" value="2" required> Estoy
                aprendiendo
            <br><input type="radio" name="nivel_html" value="3" required> Tengo
                experiencia
            <br><input type="radio" name="nivel_html" value="4" required> Lo
                domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_css">¿Cuál es tu nivel de CSS?</label>
            <input type="radio" name="nivel_css" value="1" required> Nada
            <br><input type="radio" name="nivel_css" value="2" required> Estoy
                aprendiendo
            <br><input type="radio" name="nivel_css" value="3" required> Tengo
                experiencia
            <br><input type="radio" name="nivel_css" value="4" required> Lo
                domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_js">¿Cuál es tu nivel de JavaScript?</label>
            <input type="radio" name="nivel_js" value="1" required> Nada
            <br><input type="radio" name="nivel_js" value="2" required> Estoy
                aprendiendo
            <br><input type="radio" name="nivel_js" value="3" required> Tengo
                experiencia
            <br><input type="radio" name="nivel_js" value="4" required> Lo domino al
            dedillo
        </div>
        <div class="form-input">
            <label for="nivel_php">¿Cuál es tu nivel de PHP?</label>
            <input type="radio" name="nivel_php" value="1" required> Nada
            <br><input type="radio" name="nivel_php" value="2" required> Estoy
                aprendiendo
            <br><input type="radio" name="nivel_php" value="3" required> Tengo
                experiencia
            <br><input type="radio" name="nivel_php" value="4" required> Lo domino
                al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_wp">¿Cuál es tu nivel de WordPress?</label>
            <input type="radio" name="nivel_wp" value="1" required> Nada
            <br><input type="radio" name="nivel_wp" value="2" required> Estoy
            aprendiendo
            <br><input type="radio" name="nivel_wp" value="3" required> Tengo
                experiencia
            <br><input type="radio" name="nivel_wp" value="4" required> Lo domino
                al dedillo
        </div>
        <div class="form-input">
            <label for="motivacion">¿Porqué quieres aprender a programar en
                    WordPress?</label>
            <textarea name="motivacion" id="motivacion" required></textarea>
        </div>
        <div class="form-input">
            <label for="aceptacion">Mi nombre es Fulano de Tal y Cual y me
                comprometo a custodiar de manera responsable los datos que vas
                a enviar. Su única finalidad es la de participar en el proceso
                explicado más arriba.
                En cualquier momento puedes solicitar el acceso, la rectificación
                o la eliminación de tus datos desde esta página web.</label>
            <input type="checkbox" id="aceptacion" name="aceptacion" value="1"
            required> Entiendo y acepto las condiciones
        </div>
        <div class="form-input">
            <input type="submit" value="Enviar">
        </div>
    </form>
    <?php

    return ob_get_clean();
}

add_action("admin_menu", "Kfp_Aspirante_menu");

/**
 * Agrega el menú del plugin al formulario de WordPress
 *
 * @return void
 */
function Kfp_Aspirante_menu()
{
    add_menu_page("Formulario Aspirantes", "Aspirantes", "manage_options",
        "kfp_aspirante_menu", "Kfp_Aspirante_admin", "dashicons-feedback", 75);
}

function Kfp_Aspirante_admin()
{
    global $wpdb;
    $tabla_aspirantes = $wpdb->prefix . 'aspirante';
    $aspirantes = $wpdb->get_results("SELECT * FROM $tabla_aspirantes");
    echo '<div class="wrap"><h1>Lista de aspirantes</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th width="30%">Nombre</th><th width="20%">Correo</th>';
    echo '<th>HTML</th><th>CSS</th><th>JS</th><th>PHP</th><th>WP</th><th>Total</th>';
    echo '</tr></thead>';
    echo '<tbody id="the-list">';
    foreach ($aspirantes as $aspirante) {
        $nombre = esc_textarea($aspirante->nombre);
        $correo = esc_textarea($aspirante->correo);
        $motivacion = esc_textarea($aspirante->motivacion);
        $nivel_html = (int) $aspirante->nivel_html;
        $nivel_css = (int) $aspirante->nivel_css;
        $nivel_js = (int) $aspirante->nivel_js;
        $nivel_php = (int) $aspirante->nivel_php;
        $nivel_wp = (int) $aspirante->nivel_wp;
        $total = $nivel_html + $nivel_css + $nivel_js + $nivel_php + $nivel_wp;
        echo "<tr><td><a href='#' title='$motivacion'>$nombre</a></td>";
        echo "<td>$correo</td><td>$nivel_html</td><td>$nivel_css</td>";
        echo "<td>$nivel_js</td><td>$nivel_php</td><td>$nivel_wp</td>";
		echo "<td>$total</td>";
		$url_borrar = admin_url('admin-post.php') . '?action=borra_aspirante&id='
			. $aspirante->id;
		echo "<td><a href='$url_borrar'>Borrar</a></td>";
		echo "</tr>";
    }
    echo '</tbody></table></div>';
}

/**
 * Devuelve la IP del usuario que está visitando la página
 * Código fuente: https://stackoverflow.com/questions/6717926/function-to-get-user-ip-address
 *
 * @return string
 */
function Kfp_Obtener_IP_usuario()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
        'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

// Vincula la función de borrado con un hook de admin_post
add_action('admin_post_borra_aspirante', 'Kfp_Borra_Aspirante');
/**
 * Borra un registro de aspirante usando admin-post.php
 * 
 * @return void
 */
function Kfp_Borra_Aspirante()
{
	global $wpdb;
	$url_origen = admin_url('admin.php') . '?page=kfp_aspirante_menu';
	// && current_user_can('manage_options')
	if (isset($_GET['id']) && current_user_can('manage_options')) {
		$id = (int) $_GET['id'];
		$tabla_aspirantes = $wpdb->prefix . 'aspirante';
		$wpdb->delete($tabla_aspirantes, array('id' => $id));
		$status = 'success';
	} else {
		$status = 'error';
	}
	wp_safe_redirect(
		esc_url_raw(
			add_query_arg( 'kfp_aspirante_status', $status, $url_origen )
		)
	);
}