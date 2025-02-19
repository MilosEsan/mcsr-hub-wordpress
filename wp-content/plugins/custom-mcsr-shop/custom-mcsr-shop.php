<?php
/*
Plugin Name: Custom MCSR Shop
Description: Plugin za upravljanje produktima u shopu.
Version: 1.0
Author: shommyEM
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Funkcija za kreiranje tabele
function create_produkti_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'produkti';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        naziv tinytext NOT NULL,
        tip_produkta tinytext NOT NULL,
        slika_produkta text NOT NULL,
        cijena_produkta float NOT NULL,
        opis_produkta mediumtext,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_produkti_table');

// Funkcija za prikaz forme i unos podataka
function produkti_form() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'produkti';
    $items = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    $current_user_id = get_current_user_id();
    
    if ($current_user_id !== 1) {
        $items = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $current_user_id", ARRAY_A);
    }
    ?>
    <div class="wrap">
        <h2>Add new product</h2>
        <form id="postForm" method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Product Name</th>
                    <td><input type="text" name="naziv" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Type</th>
                    <td>
                        <select name="tip_produkta" id="tip_produkta">
                            <option value="nakit">Brand Systems</option>
                            <option value="ostalo">Web Software</option>
                            <option value="majica">Clothing</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Pictures</th>
                    <td><input id="slika_produkta" type="file" name="slika_produkta" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Price</th>
                    <td><input type="number" step="0.01" name="cijena_produkta" value="" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Description</th>
                    <td><textarea name="opis_produkta" id="opis_produkta" style="resize: none !important;" rows="5"></textarea></td>
                </tr>
            </table>
            <button type="button" id="postProductBtn" class="button button-primary">DODAJ PRODUKT</button>
        </form>

        <h2>All Products</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Picture</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) { ?>
                    <tr>
                        <td><?php echo $item['naziv']; ?></td>
                        <td><?php 
                            if ($item['tip_produkta'] === 'majica') {
                                echo 'Clothing';
                            } else if ($item['tip_produkta'] === 'ostalo') {
                                echo 'Web Software';
                            } else {
                                echo 'Brand Systems';
                            }
                        ?></td>
                        <td><img src="<?php echo $item['slika_produkta']; ?>" alt="<?php echo $item['naziv']; ?>" width="50"></td>
                        <td><?php echo $item['cijena_produkta']; ?></td>
                        <td>
                            <form id="deleteForm" method="post" style="display:inline-block;">
                                <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>" />
                                <input id="deleteProductBtn" type="submit" value="Delete" class="button button-danger" />
                            </form>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="edit_id" value="<?php echo $item['id']; ?>" />
                                <input type="submit" value="Edit" class="button button-primary" />
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" style="display:none;">
        <div class="edit-modal-content">
            <span id="closeModal" style="float:right;">&times;</span>
            <h2>Izmeni Produkt</h2>
            <form id="editForm" method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="update_id" id="edit_id" value="" />
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Name</th>
                        <td><input type="text" name="naziv" id="edit_naziv" value="" required /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Type</th>
                        <td>
                            <select name="edit_tip_produkta" id="edit_tip_produkta">
                                <option value="majica">Majica</option>
                                <option value="nakit">Nakit</option>
                                <option value="duksevi">Duksevi</option>
                                <option value="ostalo">Ostalo</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Picture (max 2MB)</th>
                        <td><input type="file" name="edit_slika_produkta" id="edit_slika_produkta" accept="image/*" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Price</th>
                        <td><input type="number" step="0.01" name="cijena_produkta" id="edit_cijena_produkta" value="" required /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Description</th>
                        <td><textarea name="edit_opis_produkta" id="edit_opis_produkta" style="resize: none !important;" rows="5"></textarea></td>
                    </tr>
                </table>
                <button type="button" id="updateProductBtn" class="button button-primary">Update Product</button>
            </form>
        </div>
    </div>

    <style>
    #editModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
        padding-top: 60px;
    }
    .edit-modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
    }
    @media(max-width: 500px) {
        .edit-modal-content {
            width: 80%;
        }
    }
    #closeModal {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    #closeModal:hover,
    #closeModal:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .form-table {
        margin-bottom: 100px;
    }
    #updateProductBtn {
        margin-left: auto;
        margin-right: auto;
        display: block;
    }

    @media (max-width: 600px) {
        #deleteProductBtn, input[value="Izmeni"] {
            padding: 2px !important;
        }

        td {
            padding: 2px !important;
        }

        * {
            font-size: 11px !important;
        }

        .form-table {
            margin-bottom: 30px;
        }

        #postProductBtn {
            margin-bottom: 40px
        }
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

    jQuery(document).ready(function($) {
        // Prikazivanje popup prozora
        $('body').on('click', 'input[value="Izmeni"]', function(e) {
            e.preventDefault();
            
            var edit_id = $(this).siblings('input[name="edit_id"]').val();
            
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'get_produkti_items',
                    id: edit_id
                },
                success: function(response) {
                    if (response.success) {
                        var item = response.data;
                        $('#edit_id').val(item.id);
                        $('#edit_naziv').val(item.naziv);
                        $('#edit_tip_produkta').val(item.tip_produkta);
                        $('#edit_cijena_produkta').val(item.cijena_produkta);
                        $('#edit_opis_produkta').val(item.opis_produkta);
                        $('#editModal').show();
                    } else {
                        alert(response.data);
                    }
                }
            });
        });

        // Zatvaranje popup prozora
        $('#closeModal').on('click', function() {
            $('#editModal').hide();
        });

        // Slanje podataka putem REST-a
        $('#updateProductBtn').on('click', function() {
            // Preuzimanje ID-a produkta
            var updateId = $('#editForm').find('input[name="update_id"]').val();
            console.log($('#editForm').find('input[name="edit_opis_produkta"]').val())
            // Kreiranje FormData objekta
            var formData = new FormData();
            formData.append('naziv', $('#editForm').find('input[name="naziv"]').val());
            formData.append('tip_produkta', $('#editForm').find('select[name="edit_tip_produkta"]').val());
            formData.append('cijena_produkta', $('#editForm').find('input[name="cijena_produkta"]').val());
            formData.append('opis_produkta', $('#editForm').find('textarea[name="edit_opis_produkta"]').val());

            // Dodavanje slike ako je izabrana
            var slika_produkta = $('#editForm').find('input[name="edit_slika_produkta"]')[0].files[0];
            if (slika_produkta) {
                formData.append('slika_produkta', slika_produkta);
            }

            // Slanje AJAX PUT zahteva
            $.ajax({
                url: '/wp-json/custom-mcsr-shop/v1/edit/produkti/' + updateId,
                type: 'POST', 
                data: formData,
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce
                },
                processData: false, // Ne procesiraj podatke
                contentType: false, // Automatski postavi Content-Type za FormData
                success: function(response) {
                    if (response === 'Produkt je ažuriran.') {
                        location.reload();
                    } else {
                        alert('Greška prilikom ažuriranja produkta.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Greška: " + error);
                    console.log("Odgovor servera:", xhr.responseText);
                }
            });
        });


        $(document).on('click', '.button-danger', function (e) {
            e.preventDefault();

            // Pronađi ID produkta
            var deleteId = $(this).closest('form').find('input[name="delete_id"]').val();
            $.ajax({
                url: '/wp-json/custom-mcsr-shop/v1/delete/produkti/' + deleteId,
                type: 'DELETE',
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce, // WordPress automatski dodaje ovo kada enqueue-uješ JS fajl
                    'Content-Type': 'application/json',
                },
            success: function(response) {
                if (response.success) {
                        alert('Produkt je obrisan.');
                        location.reload();
                    } else {
                        alert('Greška: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Greška: " + error);
                    console.log("Odgovor servera:", xhr.responseText); // Dodaj za debugging
                }
            });
        });


        $('#postProductBtn').on('click', function () {
            // Kreiranje FormData objekta
            var formData = new FormData($('#postForm')[0]);

            $.ajax({
                url: '/wp-json/custom-mcsr-shop/v1/post/produkti/',
                type: 'POST',
                data: formData,
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce, 
                },
                processData: false, 
                contentType: false, 
                success: function (response) {
                    if (response.success) {
                        alert(response.message || 'Продукт је додат.');
                        location.reload();
                    } else {
                        alert('Грешка: ' + (response.message || 'непозната грешка.'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Грешка: " + error);
                    console.log("Odgovor servera:", xhr.responseText);
                    alert('Дошло је до грешке приликом додавања.');
                }
            });
        });


    });
    </script>

    <?php
}

// Funkcija za dodavanje menija u admin panel
function produkti_menu() {
    add_menu_page(
        'Produkti',           // Page title
        'Продајни артикли',   // Menu title
        'MANAGE_products',    // Capability
        'produkti',           // Menu slug
        'produkti_form',      // Function
        'dashicons-cart',     // Icon URL
        6                     // Position
    );
}

add_action('admin_menu', 'produkti_menu');

// AJAX handler funkcija za dobijanje produkata
function get_produkti_items() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'produkti';
    
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        if ($item) {
            wp_send_json_success($item);
        } else {
            wp_send_json_error('Produkt nije pronađen.');
        }
    } else {
        $items = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        wp_send_json_success($items);
    }
}

add_action('wp_ajax_get_produkti_items', 'get_produkti_items'); // Autentifikovani korisnici
add_action('wp_ajax_nopriv_get_produkti_items', 'get_produkti_items'); // Neautentifikovani korisnici

// Funkcija za ažuriranje produkta putem AJAX-a
function update_produkti_item(WP_REST_Request $request) {

    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }  

    $nonce = $request->get_header('X-WP-Nonce');

    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('invalid_nonce', 'Nonce vrednost je neispravna.', array('status' => 403));    
    }

    if (!current_user_can('MANAGE_products')) {
        return new WP_Error('permission_denied', 'Nemate dozvolu za ovu akciju.', array('status' => 403));    
    }

            global $wpdb;
            $table_name = $wpdb->prefix . 'produkti';
        
            // Dobijanje parametara iz REST zahteva
            $id = intval($request->get_param('id'));
            $naziv = sanitize_text_field($request->get_param('naziv'));
            $tip_produkta = sanitize_text_field($request->get_param('tip_produkta'));
            $cijena_produkta = floatval($request->get_param('cijena_produkta'));
            $opis_produkta = sanitize_text_field($request->get_param('opis_produkta'));

            // Provera i upload nove slike
            if (!empty($_FILES['slika_produkta']['name'])) {
                // Provera veličine fajla
                if ($_FILES['slika_produkta']['size'] > 2097152) { // 2MB
                    return new WP_Error('error', 'Slika je prevelika. Maksimalna veličina je 2MB.', array('status' => 400));
                }
        
                $uploadedfile = $_FILES['slika_produkta'];
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
                if ($movefile && !isset($movefile['error'])) {
                    $slika_produkta = esc_url_raw($movefile['url']);
                } else {
                    return new WP_Error('error', 'Greška prilikom upload-a slike: ' . $movefile['error'], array('status' => 400));
                }
            } else {
                // Zadrži staru sliku
                $slika_produkta = $wpdb->get_var($wpdb->prepare("SELECT slika_produkta FROM $table_name WHERE id = %d", $id));
            }
        
            // Ažuriranje produkta u bazi
            $updated = $wpdb->update(
                $table_name,
                array(
                    'naziv' => $naziv,
                    'tip_produkta' => $tip_produkta,
                    'slika_produkta' => $slika_produkta,
                    'cijena_produkta' => $cijena_produkta,
                    'opis_produkta' => $opis_produkta
                ),
                array('id' => $id)
            );
        
            if ($updated !== false) {
                return new WP_REST_Response('Produkt je ažuriran.', 200);
            } else {
                return new WP_Error('error', 'Greška prilikom ažuriranja produkta.', array('status' => 500));
            }
}

// Funkcija za dobijanje produkata putem REST API-ja - vidljivo u UI - za custommers

function get_produkti_items_api(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'produkti';

    // Dohvati filtere (tip i search) ako postoje
    $tip_produkta = $request->get_param('tip_produkta');
    $search_param = $request->get_param('search');

    // Dohvati parametre paginacije (podrazumevano: page = 1, per_page = 10)
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 20; 
    $offset = ($page - 1) * $per_page;

    // Osnovni upit – koristimo "WHERE 1=1" da olakšamo dinamičko dodavanje filtera
    $query = "SELECT * FROM $table_name WHERE 1=1";
    $params = array();

    if (!empty($tip_produkta)) {
        $query .= " AND tip_produkta = %s";
        $params[] = $tip_produkta;
    }
    if (!empty($search_param)) {
        $query .= " AND naziv LIKE %s";
        // Dodajemo "%" oko vrednosti za delimično podudaranje
        $params[] = '%' . $wpdb->esc_like($search_param) . '%';
    }

    // Prvo, izvrši upit za ukupno broj rezultata (bez paginacije)
    $total_query = "SELECT COUNT(*) FROM ($query) as temp";
    if (!empty($params)) {
        $total = $wpdb->get_var($wpdb->prepare($total_query, ...$params));
    } else {
        $total = $wpdb->get_var($total_query);
    }

    // Dodaj paginaciju: LIMIT i OFFSET
    $query .= " LIMIT %d OFFSET %d";
    $params[] = $per_page;
    $params[] = $offset;

    // Izvrši upit
    $items = $wpdb->get_results($wpdb->prepare($query, ...$params), ARRAY_A);

    // Pripremi odgovor sa paginacijom
    $response = array(
        'total'       => intval($total),
        'per_page'    => $per_page,
        'page'        => $page,
        'total_pages' => ceil($total / $per_page),
        'data'        => $items,
    );

    return new WP_REST_Response($response, 200);
}


function post_produkti_item(WP_REST_Request $request) {

    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }    

    // Provera nonce-a
    $nonce = $request->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('invalid_nonce', 'Nonce vrednost je neispravna.', array('status' => 403));
    }

    // Provera dozvola
    if (!current_user_can('MANAGE_products')) {
        return new WP_Error('permission_denied', 'Nemate dozvolu za ovu akciju.', array('status' => 403));
    }

    // Preuzimanje podataka
    $naziv = sanitize_text_field($request->get_param('naziv'));
    $tip_produkta = sanitize_text_field($request->get_param('tip_produkta'));
    $cijena_produkta = floatval($request->get_param('cijena_produkta'));
    $opis_produkta = sanitize_text_field($request->get_param('opis_produkta'));

    // Obrada slike
    if (!empty($_FILES['slika_produkta'])) {
        $uploadedfile = $_FILES['slika_produkta'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'produkti';

            // Unos podataka u bazu
            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'naziv' => $naziv,
                    'tip_produkta' => $tip_produkta,
                    'slika_produkta' => esc_url_raw($movefile['url']),
                    'cijena_produkta' => $cijena_produkta,
                    'opis_produkta' => $opis_produkta,
                    'user_id' => get_current_user_id(),
                )
            );

            if ($inserted) {
                return wp_send_json(array(
                    'success' => true,
                    'message' => 'Produkt je uspešno dodat.',
                ));
            } else {
                return wp_send_json(array(
                    'success' => false,
                    'message' => 'Greška prilikom unosa u bazu.',
                ), 500);
            }
        } else {
            return wp_send_json(array(
                'success' => false,
                'message' => 'Greška prilikom upload-a slike: ' . $movefile['error'],
            ), 500);
        }
    } else {
        return wp_send_json(array(
            'success' => false,
            'message' => 'Slika produkta nije dostavljena.',
        ), 400);
    }
}


// Funkcija za dobijanje produkata putem REST API-ja - vidljivo u UI - za custommers
function delete_produkti_item(WP_REST_Request $request) {
    $id = intval($request->get_param('id'));
    $nonce = $request->get_header('X-WP-Nonce');

    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('invalid_nonce', 'Nonce vrednost je neispravna.', array('status' => 403));    
    }

    if (!current_user_can('MANAGE_products')) {
        return new WP_Error('permission_denied', 'Nemate dozvolu za ovu akciju.', array('status' => 403));    
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'produkti';
    
    // Proveri da li postoji produkt sa datim ID-jem pre brisanja
    $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
    
    if (!$product) {
        // Produkt nije pronađen
        wp_send_json(array(
            'success' => false,
            'message' => 'Produkt sa datim ID-jem ne postoji.'
        ), 404); // Status 404 - nije pronađeno
    }

    // Pokušaj brisanja
    $deleted = $wpdb->delete($table_name, array('id' => $id), array('%d'));
    
    if ($deleted) {
        // Uspešno obrisano
        wp_send_json(array(
            'success' => true,
            'message' => 'Produkt je uspešno obrisan.'
        ));
    } else {
        // Greška pri brisanju
        wp_send_json(array(
            'success' => false,
            'message' => 'Došlo je do greške prilikom brisanja produkta.'
        ), 500); // Status 500 - greška na serveru
    }

}

//DODAVANJE I ISKLJUCIVANJE PERMISIJA, pri aktivaciji/deaktivaciji plugin-a:

function add_permissions() {
    $role1 = get_role('author');
    $role2 = get_role('administrator');
    
    if ($role1 && $role2) {
        $role1->remove_cap('edit_posts');
        $role1->remove_cap('edit_published_posts');
        $role1->remove_cap('publish_posts');
        $role1->remove_cap('delete_posts');
        $role1->remove_cap('delete_published_posts');
        
        $role1->add_cap('MANAGE_products');
        $role2->add_cap('MANAGE_products');
    }
}

register_activation_hook(__FILE__, 'add_permissions');

function custom_mcsr_shop_remove_caps() {
    $role1 = get_role('author');
    $role2 = get_role('administrator');
    
    if ($role1 && $role2) {
        $role1->remove_cap('MANAGE_products');
        $role2->remove_cap('MANAGE_products');
    }
}

register_deactivation_hook(__FILE__, 'custom_mcsr_shop_remove_caps');


// Register REST API endpoints
// Dodavanje REST API ruta
add_action('rest_api_init', function () {
    register_rest_route('custom-mcsr-shop/v2', '/produkti', array(
        'methods'  => 'GET',
        'callback' => 'get_produkti_items_api',
        'permission_callback' => '__return_true'
    ));

    // Ruta za ažuriranje proizvoda
    register_rest_route('custom-mcsr-shop/v1', '/edit/produkti/(?P<id>\d+)', array(
        'methods' => array('PUT', 'POST'),
        'callback' => 'update_produkti_item',
        'permission_callback' => function () {
            if (current_user_can('MANAGE_products')) {
                return true;
            }
            return new WP_Error('rest_forbidden', 'Nemate potrebne dozvole.', array('status' => 403));
        },
    ));

    register_rest_route('custom-mcsr-shop/v1', '/post/produkti', array(
        'methods' => 'POST',
        'callback' => 'post_produkti_item',
        'permission_callback' => function () {
            return current_user_can('MANAGE_products');
        },
    ));    

    register_rest_route('custom-mcsr-shop/v1', '/delete/produkti/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'delete_produkti_item',
        'permission_callback' => function () {
            if (current_user_can('MANAGE_products')) {
                return true;
            }
            return new WP_Error('rest_forbidden', 'Nemate potrebne dozvole.', array('status' => 403));
        },
    ));
});

function custom_mcsr_shop_enqueue_scripts() {
    wp_enqueue_script('product_list-js', plugin_dir_url(__FILE__) . 'product_list.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'custom_mcsr_shop_enqueue_scripts');
add_action('wp_enqueue_scripts', 'custom_mcsr_shop_enqueue_scripts');

function mcsr_shop_enqueue_styles() {
    wp_enqueue_style(
        'mcsr-shop-style', // Handle (jedinstveni naziv)
        plugin_dir_url(__FILE__) . 'style.css', // Putanja do CSS fajla
        array(), 
        '1.0.0' 
    );
}
add_action('wp_enqueue_scripts', 'mcsr_shop_enqueue_styles');

add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) {
        return $result; // Ako već postoji greška, vrati je
    }

    // Dozvoli GET zahteve bez autentifikacije
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return $result; // Dozvoli pristup za GET metode
    }

    // Blokiraj sve ostale metode ako korisnik nije ulogovan
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_not_logged_in', 
            'You are not currently logged in', 
            array('status' => 401)
        );
    }

    return $result; // Nema greške
});

function enqueue_custom_script() {
    wp_enqueue_script('custom_nonce', get_template_directory_uri().'/custom_nonce.js', array('jquery'), null, true);
    wp_localize_script('custom_nonce', 'myApi', array(
        'nonce1' => wp_create_nonce('nonce1_nonce'),
        'url'   => esc_url_raw(rest_url('custom-mcsr-shop/v1/edit/produkti/(?P<id>\d+)')),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');


function enqueue_edit_form_script() {
    wp_enqueue_script(
        'custom-edit-form', // Unikatni ID skripte
        get_template_directory_uri() . '/custom_nonce.js', // Putanja do vašeg JS fajla
        array('jquery', 'wp-api'), // Zavisi od jQuery i wp-api
        null,
        true
    );

    // Prosleđivanje wpApiSettings objektu
    wp_localize_script('custom-edit-form', 'wpApiSettings', array(
        'nonce' => wp_create_nonce('wp_rest'), // Kreiranje REST API nonce-a
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_edit_form_script');

?>