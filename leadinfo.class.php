<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Leadinfo')) {
    class Leadinfo
    {
        public static function register_plugin_scripts()
        {
            $leadinfo_id = sanitize_html_class(get_option('leadinfo_id'));
            if (!$leadinfo_id) {
                return;
            }

            if (!is_admin()) {
                ?>
                <!-- Leadinfo tracking code -->
                <script> (function(l,e,a,d,i,n,f,o){if(!l[i]){l.GlobalLeadinfoNamespace=l.GlobalLeadinfoNamespace||[]; l.GlobalLeadinfoNamespace.push(i);l[i]=function(){(l[i].q=l[i].q||[]).push(arguments)};l[i].t=l[i].t||n; l[i].q=l[i].q||[];o=e.createElement(a);f=e.getElementsByTagName(a)[0];o.async=1;o.src=d;f.parentNode.insertBefore(o,f);} }(window,document,"script","https://cdn.leadinfo.net/ping.js","leadinfo","<?php echo esc_js($leadinfo_id); ?>")); </script>
                <?php
            }
        }

        public function plugin_admin_add_page()
        {
            add_submenu_page('options-general.php', "Leadinfo", "Leadinfo", 'manage_options', 'leadinfo', array($this, 'add_settings'));
        }


        public function add_settings()
        {
            $option = 'leadinfo_id';
            $error = false;
            $name = get_option($option);

            if(isset($_GET['save']) && isset($_GET['leadinfo_id']) && current_user_can('manage_options') && check_admin_referer('leadinfo_tracking_form')) {
                $matched = preg_match('/^(LI\-)([0-9A-Z]+)$/', $_GET['leadinfo_id']);

                if($matched === 0) {
                    $error = true;
                } else {
                    $name = $_GET['leadinfo_id'];
                    update_option($option, $name);
                }
            }

            print '<div class="wrap">
                    <h2>Leadinfo Settings</h2>
    
                    <h4>Plugin Configuration</h4>
                    <p>If you don’t have an account yet get one free at <a href="https://www.leadinfo.com/?utm_source=wordpress" target="_blank">leadinfo.com</a></p>
                    
                    <p><b>Configuration Options</b></p>
                    
                    <ol>
                        <li>Visit your Leadinfo Portal, go to "Settings" and select under ‘Trackers’ the URL of the website you wish to track.</li>
                        <li>Copy your Leadinfo Site ID, starting with LI-xxx.</li>
                        <li>Return to WordPress and go to Settings > Leadinfo to paste your Leadinfo Site ID.</li>
                    </ol>
                    <form action="">
                    ' . wp_nonce_field('leadinfo_tracking_form') . '
                        <input type="hidden" name="page" value="leadinfo">
                        <div class="fieldwrap">
                            <label class="" for="leadinfo_id">Enter your Leadinfo Site ID here</label><br />
                            <input type="text" name="leadinfo_id" size="80" value="' . sanitize_html_class($name) . '" placeholder="LI-1234567890" id="leadinfo_id" spellcheck="false" autocomplete="off" />';

            if($error) {
                print '<div class="error notice">
                            <p>Incorrect Leadinfo Site ID, please try again.</p>
                        </div>';
            }

            print '
                        </div>
                        <br />
                        <div id="action">
                            <input name="save" type="submit" class="button button-primary button-large" id="save" accesskey="p" value="Save" />
                        </div>
                        <div class="clear"></div>
                    </form>
				</div>';
        }


        public function run()
        {
            add_action('admin_menu', array($this, 'plugin_admin_add_page'));
            add_action('wp_footer', array('Leadinfo', 'register_plugin_scripts'));
        }
    }
}
