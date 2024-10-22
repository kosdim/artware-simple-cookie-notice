<?php
/*
 * Plugin Name: Artware Simple Cookie Notice
 * Description: A simple plugin to show a cookie notice with a 1-week expiration.
 * Version: 1.0.0
 * Author: Artware Dev Team
 * Author URI: https://www.artware.gr/
 * Text Domain: artware-cookie
 */

function display_cookie_notice() {
    // Check if the cookie has already been accepted.
    if (!isset($_COOKIE['cookie_accepted'])) {
        wp_enqueue_style('artware-simple-cookie-notice', plugin_dir_url(__FILE__) . 'style.css');
?>
        <div id="cookie-notice" class="cookieNotice">
            <h2><?php echo __( 'Cookie Notice', 'artware-cookie' ); ?></h2>
            <p><?php echo __( 'We use cookies to improve your experience. By using our site, you agree to our use of cookies.', 'artware-cookie' ); ?> <a href="<?php echo get_home_url(); ?>/cookie-policy/"><?php echo __( 'Learn more', 'artware-cookie' ); ?></a></p>
            <button id="accept-cookie"><?php echo __( 'Accept', 'artware-cookie' ); ?></button>
        </div>
        
        <script>
            document.getElementById("accept-cookie").addEventListener("click", function() {
                document.getElementById("cookie-notice").style.display = "none";
                var date = new Date();
                date.setTime(date.getTime() + (7*24*60*60*1000)); // 7 days expiration.
                document.cookie = "cookie_accepted=true; expires=" + date.toUTCString() + "; path=/";
            });
        </script>
<?php
    }
}
add_action('wp_footer', 'display_cookie_notice');
?>