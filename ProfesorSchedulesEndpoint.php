<?php

class ProfesorSchedulesEndpoint
{
    public function register_routes()
    {
        add_action('rest_api_init', function () {
            register_rest_route('lavs-filter-options/v1', '/schedules', [
                'methods' => 'GET',
                'callback' => [__CLASS__, 'get_schedules'],
                'permission' => 'read',
            ]);
        });
    }

    public static function get_schedules($request)
    {
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
}

function get_profesor_schedules($teacher_id)
{
    global $wpdb;
    // Implementar lógica para obtener profesores disponibles
    $sql = "SELECT
            lst.id,
            lst.schedule_id,
            ls.`schedule`,
            lst.teacher_id,
            lst.busy 
        FROM
            wp_lavs_schedule_teacher lst
            INNER JOIN wp_lavs_schedules ls ON lst.schedule_id = ls.id 
        WHERE
            lst.busy = 0 
            AND lst.teacher_id = %d";

    $prepared_sql = $wpdb->prepare($sql, $teacher_id);

    $results = $wpdb->get_results($prepared_sql);

    return $results;
}
