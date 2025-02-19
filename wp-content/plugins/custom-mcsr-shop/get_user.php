<?php
function add_admin_user_info_to_js() {
    // Dobijanje trenutnog korisnika
    $current_user = wp_get_current_user();
    
    // Prosleđivanje podataka o korisniku JavaScript-u
    $user_data = array(
        'id' => $current_user->ID,
        'username' => $current_user->user_login,
        'email' => $current_user->user_email,
        'display_name' => $current_user->display_name,
        'roles' => $current_user->roles,
    );

    // Enqueue skriptu
    wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . 'get_user.js', array('jquery'), null, true);

    // Lokalizacija skripte sa podacima o korisniku
    wp_localize_script('get_user', 'currentUser', $user_data);
}
add_action('admin_enqueue_scripts', 'add_admin_user_info_to_js');

function enqueue_get_user_script() {
    // Registruj skriptu i dodaj je na front-end
    wp_enqueue_script(
        'get-user-js', // Handle za skriptu
        plugin_dir_url(__FILE__) . 'get_user.js', // Putanja do fajla
        array('jquery'), // Zavisnosti (u ovom slučaju jQuery)
        null, // Verzija
        true // Učitava skriptu u footer-u
    );
}
add_action('wp_enqueue_scripts', 'enqueue_get_user_script');


?>