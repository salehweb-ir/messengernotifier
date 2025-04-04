<?php
namespace MessengerNotifier\Admin;

if (!defined('ABSPATH')) exit;

class Settings {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'ensure_contact_page']);
    }

    public function add_menu() {
        add_options_page(
            __('Messenger Notifier Settings', 'messengernotifier'),
            __('Messenger Notifier', 'messengernotifier'),
            'manage_options',
            'messengernotifier',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('messengernotifier_settings', 'messengernotifier_general_settings');
        register_setting('messengernotifier_settings', 'messengernotifier_contact_form_page_id');
        register_setting('messengernotifier_settings', 'messengernotifier_contact_form_template');
    }

    public function ensure_contact_page() {
        $page_id = get_option('messengernotifier_contact_form_page_id');

        if (!$page_id || get_post_status($page_id) !== 'publish') {
            $page_data = [
                'post_title'   => __('Contact Admin', 'messengernotifier'),
                'post_content' => '[messenger_contact_form]',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ];
            $page_id = wp_insert_post($page_data);
            if (!is_wp_error($page_id)) {
                update_option('messengernotifier_contact_form_page_id', $page_id);
            }
        }
    }

    public function render_settings_page() {
        $active_tab = $_GET['tab'] ?? 'general';

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Messenger Notifier Settings', 'messengernotifier') . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=messengernotifier&tab=general" class="nav-tab ' . ($active_tab == 'general' ? 'nav-tab-active' : '') . '">' . esc_html__('General', 'messengernotifier') . '</a>';
        echo '<a href="?page=messengernotifier&tab=contact" class="nav-tab ' . ($active_tab == 'contact' ? 'nav-tab-active' : '') . '">' . esc_html__('Contact Page', 'messengernotifier') . '</a>';
        echo '</h2>';

        echo '<form method="post" action="options.php">';
        settings_fields('messengernotifier_settings');

        if ($active_tab === 'general') {
            $options = get_option('messengernotifier_general_settings');
            echo '<table class="form-table">';
            echo '<tr><th scope="row">' . esc_html__('Enable Eitaa', 'messengernotifier') . '</th>';
            echo '<td><input type="checkbox" name="messengernotifier_general_settings[enable_eitaa]" value="1" ' . checked(1, $options['enable_eitaa'] ?? 0, false) . '></td></tr>';
            // Add more general options here
            echo '</table>';
        }

        if ($active_tab === 'contact') {
            $page_id = get_option('messengernotifier_contact_form_page_id');
            if ($page_id && get_post_status($page_id) === 'publish') {
                $url = get_permalink($page_id);
                echo '<p>' . sprintf(__('User contact page: <a href="%s" target="_blank">%s</a>', 'messengernotifier'), esc_url($url), esc_html($url)) . '</p>';
            } else {
                echo '<p>' . esc_html__('Contact page not found.', 'messengernotifier') . '</p>';
            }

            $selected_template = get_option('messengernotifier_contact_form_template', 'default');
            echo '<h3>' . esc_html__('Select Template (future use)', 'messengernotifier') . '</h3>';
            echo '<select name="messengernotifier_contact_form_template">';
            echo '<option value="default" ' . selected('default', $selected_template, false) . '>default.php</option>';
            // Add more templates if needed
            echo '</select>';
        }

        submit_button();
        echo '</form>';
        echo '</div>';
    }
}
