<?php
/*
 * Plugin Name: WooCommerce Product Featured Specifications
 * Plugin URI: https://github.com/truongchauhien/woocommerce-product-featured-specifications
 * Text Domain: woocommerce-product-featured-specifications
 * Domain Path: /languages
 */

register_activation_hook(__FILE__, 'wpfs_activate');
register_deactivation_hook(__FILE__, 'wpfs_deactivate');

function wpfs_activate() {

}

function wpfs_deactivate() {

}

add_action( 'init', 'wpfs_load_textdomain' );
function wpfs_load_textdomain() {
	load_plugin_textdomain( 'woocommerce-product-featured-specifications', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

add_action('add_meta_boxes', 'wpfs_add_product_featured_specifications_box');
function wpfs_add_product_featured_specifications_box() {
    add_meta_box(
        'wpfs_featured_specifications_box',
        __('Featured Specifications', 'woocommerce-product-featured-specifications'),
        'wpfs_display_featured_specifications_editor',
        'product'
    );
}

function wpfs_display_featured_specifications_editor($post) {
    $meta = get_post_meta($post->ID, 'wpfs_featured_specifications', true);
    echo '<input type="hidden" name="wpfs_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    echo '<table class="wpfs-featured-specifications">';
    echo '<thead>';
    echo '  <tr>';
    echo '      <td></td>';
    printf('    <td>%s</td>', esc_html(__('Title', 'woocommerce-product-featured-specifications')));
    printf('    <td>%s</td>', esc_html(__('Description', 'woocommerce-product-featured-specifications')));
    echo '  </tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '  <tr class="wpfs-specification-template">';
    echo '      <td>';
    echo '          <span class="wpfs-specification-drag">↕️</span>';
    echo '      </td>';
    echo '      <td>';
    echo '          <input type="text" value="">';
    echo '      </td>';
    echo '      <td>';
    echo '          <input type="text" value="">';
    echo '      </td>';
    echo '      <td>';
    printf('          <button class="button wpfs-specification-delete">%s</button>', esc_html(__('Delete', 'woocommerce-product-featured-specifications')));
    echo '      </td>';
    echo '  </tr>';
    if (!empty($meta)) {
        $specifications = json_decode($meta, true);
        foreach ($specifications as $specification) {
            $title = esc_attr($specification['title']);
            $description = esc_attr($specification['description']);

            echo '  <tr class="wpfs-specification">';
            echo '      <td>';
            echo '          <span class="wpfs-specification-drag">↕️</span>';
            echo '      </td>';

            echo '      <td>';
            echo "          <input type=\"text\" name=\"wpfs-specification-title[]\" value=\"{$title}\">";
            echo '      </td>';

            echo '      <td>';
            echo "          <input type=\"text\" name=\"wpfs-specification-description[]\" value=\"{$description}\">";
            echo '      </td>';

            echo '      <td>';
            printf('          <button class="button wpfs-specification-delete">%s</button>', esc_html(__('Delete', 'woocommerce-product-featured-specifications')));
            echo '      </td>';
            echo '  </tr>';
        }
    }
    echo '</tbody>';
    echo '<tfoot>';
    echo '  <td>';
    echo '  </td>';
    echo '  <td>';
    printf('    <button class="button wpfs-specification-add-button">%s</button>', esc_html(__('Add a featured specification', 'woocommerce-product-featured-specifications')));
    echo '  </td>';
    echo '  <td>';
    echo '  </td>';
    echo '  <td>';
    echo '  </td>';
    echo '</tfoot>';
    echo '</table>';
}

add_action('save_post', 'wpfs_save_featured_specifications');
function wpfs_save_featured_specifications($post_id) {
    if (array_key_exists('wpfs-specification-title', $_POST) &&
        array_key_exists('wpfs-specification-description', $_POST)) {        
        $titles = $_POST['wpfs-specification-title'];
        $descriptions = $_POST['wpfs-specification-description'];

        $specifications = array();
        foreach ($titles as $index => $title) {
            $specifications[] = [
                'title' => $title,
                'description' => $descriptions[$index]
            ];
        }

        update_post_meta(
            $post_id,
            'wpfs_featured_specifications',
            json_encode($specifications, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS)
        );
    }
}

add_filter( 'woocommerce_short_description', 'wpfs_add_featured_specification_table');
function wpfs_add_featured_specification_table($short_description) {
    global $post;
    $meta = get_post_meta($post->ID, 'wpfs_featured_specifications', true);

    if (!$meta) {
        return;
    }
    
    $html = '';

    $html .= '<table class="wpfs-featured-specification-table">';
    $html .= '    <tbody>';
    $specifications = json_decode($meta, true);
    foreach ($specifications as $specification) {
        $title = esc_html($specification['title']);
        $description = esc_html($specification['description']);
        $html .= '        <tr>';
        $html .= "            <td>{$title}</td>";
        $html .= "            <td>{$description}</td>";
        $html .= '        </tr>';
    }
    $html .= '    </tbody>';
    $html .= '</table>';

    $short_description .= $html;
    return $short_description;
}

add_action('admin_enqueue_scripts', 'wpfs_add_admin_scripts');
function wpfs_add_admin_scripts() {
    if (!is_admin()) {
        return;
    }

    wp_enqueue_script('wpfs_featured_specifications_editor_js', plugin_dir_url(__FILE__) . '/admin/js/featured-specifications-editor.js', array('jquery'), false, true);
    wp_enqueue_style('wpfs_featured_specifications_editor_css', plugin_dir_url(__FILE__) . '/admin/css/featured-specifications-editor.css');
}

add_action('wp_enqueue_scripts', 'wpfs_add_scripts');
function wpfs_add_scripts() {
    wp_enqueue_style('wpfs_featured_specifications_css', plugin_dir_url(__FILE__) . '/public/css/featured-specifications.css');
}
