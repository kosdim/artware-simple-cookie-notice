<?php
/*
 * Plugin Name: Artware Simple Cookie Notice
 * Description: A simple plugin to show a cookie notice with customizable expiration and link, supporting multilingual plugins (WPML and Polylang).
 * Version: 1.0.1
 * Author: Artware Dev Team
 * Author URI: https://www.artware.gr/
 * Text Domain: artware-cookie
 */

// Add options page in the WordPress admin.
function artware_add_cookie_notice_settings_page() {
    add_options_page(
        'Cookie Notice Settings', 
        'Cookie Notice', 
        'manage_options', 
        'artware-cookie-notice', 
        'artware_cookie_notice_settings_page'
    );
}
add_action('admin_menu', 'artware_add_cookie_notice_settings_page');

// Get current language (WPML or Polylang).
function artware_get_current_language() {
    if (function_exists('pll_current_language')) {
        return pll_current_language(); // For Polylang.
    } elseif (defined('ICL_LANGUAGE_CODE')) {
        return ICL_LANGUAGE_CODE; // For WPML.
    }

    return 'default'; // Default language.
}

// Options page content.
function artware_cookie_notice_settings_page() {
    ?>
    <div class="wrap">
        <h1>Cookie Notice Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('artware_cookie_notice_settings_group');
                do_settings_sections('artware-cookie-notice');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings.
function artware_register_cookie_notice_settings() {
    $current_language = artware_get_current_language();

    register_setting('artware_cookie_notice_settings_group', 'artware_cookie_notice_expiration_days_' . $current_language);
    register_setting('artware_cookie_notice_settings_group', 'artware_cookie_notice_learn_more_link_' . $current_language);
    
    add_settings_section('artware_cookie_notice_main', 'Main Settings', null, 'artware-cookie-notice');
    
    add_settings_field('cookie_expiration_days', 'Cookie Expiration (days)', 'artware_cookie_expiration_days_callback', 'artware-cookie-notice', 'artware_cookie_notice_main');
    add_settings_field('learn_more_link', 'Learn More Link', 'artware_learn_more_link_callback', 'artware-cookie-notice', 'artware_cookie_notice_main');
}
add_action('admin_init', 'artware_register_cookie_notice_settings');

function artware_cookie_expiration_days_callback() {
    $current_language = artware_get_current_language();
    $expiration_days = get_option('artware_cookie_notice_expiration_days_' . $current_language, 7);
    echo "<input type='number' name='artware_cookie_notice_expiration_days_" . esc_attr($current_language) . "' value='" . esc_attr($expiration_days) . "' min='1' />";
}

function artware_learn_more_link_callback() {
    $current_language = artware_get_current_language();
    $learn_more_link = get_option('artware_cookie_notice_learn_more_link_' . $current_language, get_home_url() . '/cookie-policy/');
    echo "<input type='url' name='artware_cookie_notice_learn_more_link_" . esc_attr($current_language) . "' value='" . esc_attr($learn_more_link) . "' />";
}

// Show cookie.
function display_cookie_notice() {
    // Check if the cookie has already been accepted.
    if (!isset($_COOKIE['cookie_accepted'])) {
        wp_enqueue_style('artware-simple-cookie-notice', plugin_dir_url(__FILE__) . 'style.css');

        // Get current language.
        $current_language = artware_get_current_language();

        // Fetch options for the current language
        $expiration_days = get_option('artware_cookie_notice_expiration_days_' . $current_language, 7);
        $learn_more_link = get_option('artware_cookie_notice_learn_more_link_' . $current_language, get_home_url() . '/cookie-policy/');
?>
        <div id="cookie-notice" class="cookieNotice">
            <h2><?php echo __( 'Cookie Notice', 'artware-cookie' ); ?></h2>
            <p><?php echo __( 'We use cookies to improve your experience. By using our site, you agree to our use of cookies.', 'artware-cookie' ); ?> <a href="<?php echo esc_url($learn_more_link); ?>"><?php echo __( 'Learn more', 'artware-cookie' ); ?></a></p>
            <button id="accept-cookie"><?php echo __( 'Accept', 'artware-cookie' ); ?></button>
        </div>
        
        <script>
            document.getElementById("accept-cookie").addEventListener("click", function() {
                document.getElementById("cookie-notice").style.display = "none";
                var date = new Date();
                date.setTime(date.getTime() + (<?php echo intval($expiration_days); ?>*24*60*60*1000)); // Cookie expiration.
                document.cookie = "cookie_accepted=true; expires=" + date.toUTCString() + "; path=/";
            });
        </script>
<?php
    }
}
add_action('wp_footer', 'display_cookie_notice');
?>