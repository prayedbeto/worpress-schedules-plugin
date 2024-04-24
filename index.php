<?php

/**
 * Plugin Name: Filter Options Woocommerce
 * Plugin URI: https://www.your-plugins-site.com/
 * Description: Manage options on select fields.
 * Version: 0.1
 * Author: Luis Vasquez
 * Author URI: https://www.your-site.com/
 **/

add_action('woocommerce_before_add_to_cart_button', 'mostrar_campos_personalizados');

function mostrar_campos_personalizados()
{
    // Cargar plantilla HTML
    $plantilla = wc_get_template('campos-personalizados.php');

    // Obtener ID del producto y metadatos
    $producto_id = get_the_ID();
    $data = obtener_profesores_disponibles($producto_id);

    $profesores = [];

    foreach($data as $item) {
        $exists = false;
        foreach($profesores as $profesor) {
            if($profesor['teacher_id'] == $item->teacher_id) {
                $exists = true;
            }
        }
        if($exists == false) {
            array_push($profesores, ['teacher_id' => $item->teacher_id, 'name' => $item->name]);
        }
    }

    // Mostrar la plantilla
    echo "<div class='campos-personalizados'>";
    echo "<h3>Seleccionar profesor y horario</h3>";
    echo "<div class='campo-profesor' style='width: 100%; display: flex; flex-wrap: wrap;'>";
    echo "<label for='profesor_id' style='width: 100%'>Profesor:</label>";
    echo "<select id='profesor_id' name='profesor_id' style='width: 100%' class='select-profesor'>";
    echo "<option>Selecciona</option>";
    foreach ($profesores as $profesor) {
        echo "<option value='{$profesor['teacher_id']}'>{$profesor['name']}</option>";
    }
    echo "</select>";
    echo "</div>";
    echo "<div class='campo-horario' style='width: 100%; display: flex; flex-wrap: wrap; margin-bottom: 1em;'>";
    echo "<label for='horario_id' style='width: 100%'>Lunes:</label>";
    echo "<select id='horario_id' name='horario_id' style='width: 100%'>";
    echo "<option>Selecciona</option>";
    echo "</select>";
    echo "<label for='m_horario_id' style='width: 100%'>Martes:</label>";
    echo "<select id='m_horario_id' name='m_horario_id' style='width: 100%'>";
    echo "<option>Selecciona</option>";
    echo "</select>";
    echo "<label for='x_horario_id' style='width: 100%'>Miercoles:</label>";
    echo "<select id='x_horario_id' name='x_horario_id' style='width: 100%'>";
    echo "<option>Selecciona</option>";
    echo "</select>";
    echo "<label for='j_horario_id' style='width: 100%'>Jueves:</label>";
    echo "<select id='j_horario_id' name='j_horario_id' style='width: 100%'>";
    echo "<option>Selecciona</option>";
    echo "</select>";
    echo "<label for='v_horario_id' style='width: 100%'>Viernes:</label>";
    echo "<select id='v_horario_id' name='v_horario_id' style='width: 100%'>";
    echo "<option>Selecciona</option>";
    echo "</select>";
    echo "</div>";
    echo "</div>";
}

add_action('wp_head', 'my_plugin_add_scripts_to_head');

function my_plugin_add_scripts_to_head() {
    echo '<link href="' . plugins_url('css/teachers.css', __FILE__) . '"/>';
    echo '<script src="' . plugins_url('js/teachers.js', __FILE__) . '"></script>';
  }

// Función para obtener profesores disponibles
function obtener_profesores_disponibles()
{
    global $wpdb;
    // Implementar lógica para obtener profesores disponibles
    $sql = "SELECT
            lst.id,
            lst.schedule_id,
            ls.`schedule`,
            lst.teacher_id,
            lt.`name`,
            lst.busy
        FROM
            {$wpdb->prefix}lavs_schedule_teacher lst
            INNER JOIN {$wpdb->prefix}lavs_teachers lt ON lst.teacher_id = lt.id
            INNER JOIN {$wpdb->prefix}lavs_schedules ls ON lst.schedule_id = ls.id 
        WHERE
            lst.busy = 0";

    $prepared_sql = $wpdb->prepare($sql);

	$results = $wpdb->get_results($prepared_sql);

    return $results;
}

add_action( 'rest_api_init', 'prefix_register_example_routes' );

function get_profesor_schedules($teacher_id)
{
    global $wpdb;
    // Implementar lógica para obtener profesores disponibles
    $sql = "SELECT
            lst.id,
            lst.schedule_id,
            ls.`schedule`,
            lst.teacher_id,
            lst.busy,
            lst.day
        FROM
            {$wpdb->prefix}lavs_schedule_teacher lst
            INNER JOIN {$wpdb->prefix}lavs_schedules ls ON lst.schedule_id = ls.id 
        WHERE
            lst.busy = 0 
            AND lst.teacher_id = %d";

    $prepared_sql = $wpdb->prepare($sql, $teacher_id);

    $results = $wpdb->get_results($prepared_sql);

    return $results;
}

function prefix_get_endpoint_phrase($request) {
    // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
    $profesorId = $request->get_param('teacher_id');

    // Query the database to fetch schedules for the given profesor ID
    $schedules = get_profesor_schedules($profesorId); // Replace with your actual function

    if ($schedules) {
        return new WP_REST_Response([
            'status' => 'success',
            'data' => $schedules,
        ]);
    } else {
        return new WP_REST_Response([
            'status' => 'error',
            'message' => 'No schedules found for profesor ID: ' . $profesorId,
        ], 404);
    }
}

function prefix_register_example_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.   
    // require('ProfesorSchedulesEndpoint.php');
    // $profesorSchedulesEndpoint = new ProfesorSchedulesEndpoint();
    // $profesorSchedulesEndpoint->register_routes();
    register_rest_route( 'lavs-filter-options/v1', '/phrase', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => 'prefix_get_endpoint_phrase',
    ));
};

register_activation_hook( __FILE__, 'jal_install' );
register_activation_hook( __FILE__, 'jal_install_data' );

global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {
	global $wpdb;
	global $jal_db_version;
	
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();

	// $sql = "CREATE TABLE $table_name (
	// 	id mediumint(9) NOT NULL AUTO_INCREMENT,
	// 	time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	// 	name tinytext NOT NULL,
	// 	text text NOT NULL,
	// 	url varchar(55) DEFAULT '' NOT NULL,
	// 	PRIMARY KEY  (id)
	// ) $charset_collate;";
	$table_name = $wpdb->prefix . 'lavs_teachers';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	$table_name = $wpdb->prefix . 'lavs_schedules';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        schedule varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	$table_name = $wpdb->prefix . 'lavs_schedule_teacher';
    
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        schedule_id mediumint(9) NOT NULL,
        teacher_id mediumint(9) NOT NULL,
        day char(1) NOT NULL,
        busy tinyint(1) DEFAULT 0,
		PRIMARY KEY  (id)
	) $charset_collate;";

    dbDelta( $sql );

    $table_name = $wpdb->prefix . 'lavs_schedule_item';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		cart_id varchar(255) NOT NULL,
		product_id mediumint(9) NOT NULL,
		user_id mediumint(9) DEFAULT 0,
        teacher_id mediumint(9) NOT NULL,
        schedule_id mediumint(9) NOT NULL,
        day char(1) NOT NULL,
        add_to_cart mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

function jal_install_data() {
	global $wpdb;
	
	$welcome_name = 'Mr. WordPress';
	$welcome_text = 'Congratulations, you just completed the installation!';
	
	$table_name = $wpdb->prefix . 'liveshoutbox';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => current_time( 'mysql' ), 
			'name' => $welcome_name, 
			'text' => $welcome_text, 
		) 
	);
}

add_action('woocommerce_add_to_cart', 'validate_session_before_cart_add_action', 10, 3);

function validate_session_before_cart_add_action($cart, $product_id, $quantity = 1) {
    global $wpdb;

    $currentPath = $_SERVER['REQUEST_URI'];

    if (!is_user_logged_in()) {
        // User is not logged in, redirect to login page
        
        wp_redirect('\/login?redirect='. $currentPath);
        // wc_add_notice(__('Please log in to add products to your cart.', 'woocommerce'), 'error');
        exit;
    }
}

add_action('woocommerce_add_to_cart', 'my_after_add_to_cart_function', 11, 6);

function my_after_add_to_cart_function($cart_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    $user_id = get_current_user_id();
    $teacher_id = $_POST['profesor_id'];
    $schedule_id = $_POST['horario_id'];
    $m_schedule_id = $_POST['m_horario_id'];
    $x_schedule_id = $_POST['x_horario_id'];
    $j_schedule_id = $_POST['j_horario_id'];
    $v_schedule_id = $_POST['v_horario_id'];
    $add_to_cart = $_POST['add-to-cart'];
    
    global $wpdb;
    
    $result = $wpdb->insert( 
		$wpdb->prefix . "lavs_schedule_item", 
		[
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'user_id' => $user_id,
			'teacher_id' => $teacher_id, 
			'schedule_id' => $schedule_id, 
            'day' => 'L',
			'add_to_cart' => $add_to_cart, 
        ] 
	);
    $result = $wpdb->insert( 
		$wpdb->prefix . "lavs_schedule_item", 
		[
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'user_id' => $user_id,
			'teacher_id' => $teacher_id, 
			'schedule_id' => $m_schedule_id, 
            'day' => 'M',
			'add_to_cart' => $add_to_cart, 
        ] 
	);
    $result = $wpdb->insert( 
		$wpdb->prefix . "lavs_schedule_item", 
		[
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'user_id' => $user_id,
			'teacher_id' => $teacher_id, 
			'schedule_id' => $x_schedule_id, 
            'day' => 'X',
			'add_to_cart' => $add_to_cart, 
        ] 
	);
    $result = $wpdb->insert( 
		$wpdb->prefix . "lavs_schedule_item", 
		[
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'user_id' => $user_id,
			'teacher_id' => $teacher_id, 
			'schedule_id' => $j_schedule_id, 
            'day' => 'J',
			'add_to_cart' => $add_to_cart, 
        ] 
	);
    $result = $wpdb->insert( 
		$wpdb->prefix . "lavs_schedule_item", 
		[
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'user_id' => $user_id,
			'teacher_id' => $teacher_id, 
			'schedule_id' => $v_schedule_id, 
            'day' => 'V',
			'add_to_cart' => $add_to_cart, 
        ] 
	);
}

// add_action('woocommerce_checkout_create_order_line_item', 'lavs_woocommerce_checkout_create_order_line_item', 11, 4);

// function lavs_woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order ) {
//     global $wpdb;

// }


add_action('woocommerce_order_status_changed', 'my_after_checkout_function', 11, 4);

function my_after_checkout_function($order_id, $previous_status, $new_status, $order) {
    global $wpdb;

    if ($new_status === 'on-hold' || $new_status === 'processing') {
        // Order checkout is completed, perform specific actions

        $user_id = get_current_user_id();
        // $sql = "SELECT
        //         lsi.teacher_id, lsi.schedule_id, lsi.day
        //     FROM
        //         {$wpdb->prefix}woocommerce_order_items woi
        //         INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim ON woi.order_item_id = woim.order_item_id
        //         INNER JOIN {$wpdb->prefix}lavs_schedule_item lsi on woim.meta_value = lsi.add_to_cart
        //     WHERE
        //         order_id = %d
        //         AND woim.meta_key LIKE '_product_id'";
        $sql = "SELECT
                    * 
                FROM
                    wp_lavs_schedule_item 
                WHERE
                    user_id = %d";

        $prepared_sql = $wpdb->prepare($sql, $user_id);

        $results = $wpdb->get_results($prepared_sql);
        
        foreach($results as $result) {
            $sql = "UPDATE " . $wpdb->prefix . "lavs_schedule_teacher 
                SET busy = 1 
                WHERE
                    teacher_id = %d 
                    AND schedule_id = %d
                    AND `day` = %s";
    
            $prepared_sql = $wpdb->prepare($sql, $result->teacher_id, $result->schedule_id, $result->day);
    
            $result = $wpdb->query($prepared_sql);
        }

        $sql = "DELETE FROM " . $wpdb->prefix . "lavs_schedule_item
            WHERE user_id = %d";
        
        $prepared_sql = $wpdb->prepare($sql, $user_id);

        $result = $wpdb->query($prepared_sql);
    }
}