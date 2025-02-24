<?php
/**
 * Plugin Name: Easy Menu Icons
 * Plugin URI:  https://github.com/emonbhuiyan/Easy-Menu-Icons
 * Description: Add FontAwesome icons to WordPress menu items with options for spacing, position, and size.
 * Version: 1.0
 * Author: Emon Bhuiyan
 * Author URI: https://emon.one/
 * License: GPL2
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// --- Conditionally Load FontAwesome 6.4.2 Only if Not Already Included ---
function check_and_load_fontawesome() {
    $fontawesome_loaded = false;

    foreach (wp_styles()->registered as $style) {
        if (strpos($style->src, 'font-awesome') !== false || strpos($style->src, 'fontawesome') !== false) {
            if (strpos($style->src, '6.4.2') !== false) {
                $fontawesome_loaded = true;
                break;
            }
        }
    }

    if (!$fontawesome_loaded) {
        wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css', array(), '6.4.2');
    }
}
add_action('wp_enqueue_scripts', 'check_and_load_fontawesome');

// --- Add Custom Menu Fields (FontAwesome Class, Spacing, Position, Size) ---
function add_custom_menu_icon_field($item_id, $item) {
    $icon_class = get_post_meta($item_id, '_menu_item_icon', true);
    $icon_spacing = get_post_meta($item_id, '_menu_item_icon_spacing', true);
    $icon_position = get_post_meta($item_id, '_menu_item_icon_position', true);
    $icon_size = get_post_meta($item_id, '_menu_item_icon_size', true);
    ?>
    <p class="description description-wide">
        <label for="menu-item-icon-<?php echo $item_id; ?>">
            <?php _e('FontAwesome Icon Class:'); ?>
            <input type="text" id="menu-item-icon-<?php echo $item_id; ?>" 
                   class="widefat code edit-menu-item-custom" 
                   name="menu-item-icon[<?php echo $item_id; ?>]" 
                   value="<?php echo esc_attr($icon_class); ?>" 
                   placeholder="e.g., fas fa-home">
        </label>
    </p>
    <p class="description description-wide">
        <label for="menu-item-icon-spacing-<?php echo $item_id; ?>">
            <?php _e('Icon Spacing (px):'); ?>
            <input type="number" id="menu-item-icon-spacing-<?php echo $item_id; ?>" 
                   class="widefat code edit-menu-item-custom" 
                   name="menu-item-icon-spacing[<?php echo $item_id; ?>]" 
                   value="<?php echo esc_attr($icon_spacing ?: '8'); ?>"> 
        </label>
    </p>
    <p class="description description-wide">
        <label for="menu-item-icon-position-<?php echo $item_id; ?>">
            <?php _e('Icon Position:'); ?>
            <select name="menu-item-icon-position[<?php echo $item_id; ?>]" id="menu-item-icon-position-<?php echo $item_id; ?>">
                <option value="before" <?php selected($icon_position, 'before'); ?>>Before Text</option>
                <option value="after" <?php selected($icon_position, 'after'); ?>>After Text</option>
            </select>
        </label>
    </p>
    <p class="description description-wide">
        <label for="menu-item-icon-size-<?php echo $item_id; ?>">
            <?php _e('Icon Size (px):'); ?>
            <input type="number" id="menu-item-icon-size-<?php echo $item_id; ?>" 
                   class="widefat code edit-menu-item-custom" 
                   name="menu-item-icon-size[<?php echo $item_id; ?>]" 
                   value="<?php echo esc_attr($icon_size ?: '16'); ?>"> 
        </label>
    </p>
    <?php
}
add_action('wp_nav_menu_item_custom_fields', 'add_custom_menu_icon_field', 10, 2);

// --- Save Selected Fields ---
function save_custom_menu_icon_field($menu_id, $menu_item_db_id) {
    if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_icon', sanitize_text_field($_POST['menu-item-icon'][$menu_item_db_id]));
    }
    if (isset($_POST['menu-item-icon-spacing'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_icon_spacing', sanitize_text_field($_POST['menu-item-icon-spacing'][$menu_item_db_id]));
    }
    if (isset($_POST['menu-item-icon-position'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_icon_position', sanitize_text_field($_POST['menu-item-icon-position'][$menu_item_db_id]));
    }
    if (isset($_POST['menu-item-icon-size'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_icon_size', sanitize_text_field($_POST['menu-item-icon-size'][$menu_item_db_id]));
    }
}
add_action('wp_update_nav_menu_item', 'save_custom_menu_icon_field', 10, 2);

// --- Display Icons in Menu Frontend ---
function display_menu_icons($title, $item, $args, $depth) {
    $icon_class = get_post_meta($item->ID, '_menu_item_icon', true);
    $icon_spacing = get_post_meta($item->ID, '_menu_item_icon_spacing', true);
    $icon_position = get_post_meta($item->ID, '_menu_item_icon_position', true);
    $icon_size = get_post_meta($item->ID, '_menu_item_icon_size', true);

    if (!empty($icon_class)) {
        $icon_spacing = !empty($icon_spacing) ? $icon_spacing . 'px' : '8px';
        $icon_size = !empty($icon_size) ? $icon_size . 'px' : '16px';

        $icon_html = '<i class="' . esc_attr($icon_class) . '" style="font-size: ' . esc_attr($icon_size) . '; margin-' . ($icon_position === 'before' ? 'right' : 'left') . ': ' . $icon_spacing . ';"></i>';

        return $icon_position === 'before' ? $icon_html . $title : $title . $icon_html;
    }
    return $title;
}
add_filter('nav_menu_item_title', 'display_menu_icons', 10, 4);
