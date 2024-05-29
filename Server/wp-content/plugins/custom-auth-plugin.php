<?php
/*
Plugin Name: Custom Auth Plugin
Description: Custom authentication endpoints for signup, login, and user info.
Version: 1.0
Author: Your Name
*/

add_action('rest_api_init', function () {
    register_rest_route('custom-auth/v1', '/signup', [
        'methods' => 'POST',
        'callback' => 'custom_auth_signup',
    ]);

    register_rest_route('custom-auth/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'custom_auth_login',
    ));

    register_rest_route('custom-auth/v1', '/user', [
        'methods' => 'GET',
        'callback' => 'custom_auth_get_user',
        'permission_callback' => 'custom_authenticate_user',
    ]);
});

function custom_auth_signup(WP_REST_Request $request) {
    global $wpdb;
    $params = $request->get_json_params();
    $username = sanitize_text_field($params['username']);
    $email = sanitize_email($params['email']);
    $password = $params['password'];

    if (username_exists($username) || email_exists($email)) {
        return new WP_Error('user_exists', 'User already exists', ['status' => 400]);
    }

    $hashed_password = wp_hash_password($password);
    $wpdb->insert(
        $wpdb->prefix . 'users',
        [
            'user_login' => $username,
            'user_pass' => $hashed_password,
            'user_email' => $email,
            'user_registered' => current_time('mysql'),
            'user_status' => 0,
        ]
    );

    return ['message' => 'User created'];
}

function custom_auth_login(WP_REST_Request $request) {
    global $wpdb;
    
    // Sanitize the input
    $username = sanitize_text_field($request['username']);
    $password = sanitize_text_field($request['password']);
    
    // Debug logs
    error_log("Username: $username");
    error_log("Password: $password");

    // Get user by login name
    $user = get_user_by('login', $username);

    if (!$user || !wp_check_password($password, $user->user_pass, $user->ID)) {
        return new WP_Error('invalid_credentials', 'Invalid username or password', array('status' => 403));
    }

    // Generate a token
    $token = bin2hex(random_bytes(16));

    // Debug log
    error_log("Generated token: $token");

    // Store the token in a custom table
    $table_name = $wpdb->prefix . 'user_tokens';
    $result = $wpdb->replace(
        $table_name,
        array(
            'user_id' => $user->ID,
            'token' => $token,
            'created_at' => current_time('mysql')
        ),
        array(
            '%d',
            '%s',
            '%s'
        )
    );

    // Check if token storage was successful
    if ($result === false) {
        error_log("Failed to store token: " . $wpdb->last_error);
        return new WP_Error('db_error', 'Failed to store token', array('status' => 500));
    }

    // Return the token in the response
    return array(
        'token' => $token,
        'user' => array(
            'username' => $user->user_login,
            'email' => $user->user_email
        )
    );
}

function custom_auth_get_user(WP_REST_Request $request) {
    global $wpdb;

    $token = $request->get_header('Authorization');
    if (empty($token)) {
        return new WP_Error('no_token', 'No token provided', array('status' => 403));
    }

    $table_name = $wpdb->prefix . 'user_tokens';
    $user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table_name WHERE token = %s", $token));

    if (!$user_id) {
        return new WP_Error('invalid_token', 'Invalid token', array('status' => 403));
    }

    $user = get_userdata($user_id);

    if (!$user) {
        return new WP_Error('no_user', 'No user found', array('status' => 404));
    }

    return array(
        'username' => $user->user_login,
        'email' => $user->user_email,
    );
}

function custom_authenticate_user() {
    
    return true;
}
