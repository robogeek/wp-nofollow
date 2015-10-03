<?php

/*
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License, version 2, as
   published by the Free Software Foundation.
   
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU General Public License for more details.
     
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

function dh_nf_admin_enqueue_scripts($hook) {
    global $wp_scripts;
    wp_enqueue_script('dhnf-admin', DHNFURL . 'js/admin.js', array('jquery-ui-accordion'));
    $queryui = $wp_scripts->query('jquery-ui-core');
    $url = "https://ajax.googleapis.com/ajax/libs/jqueryui/" . $queryui->ver . "/themes/smoothness/jquery-ui.css";
    wp_enqueue_style('jquery-ui-start', $url, false, null);
}
add_action( 'admin_enqueue_scripts', 'dh_nf_admin_enqueue_scripts');

function dh_nf_admin_init() {
    add_editor_style(DHNFURL . 'css/admin-style.css');
}
add_action( 'admin_init', 'dh_nf_admin_init');

function dh_nf_admin_sidebar() {

	?>
	<div class="dh_nf_admin_banner">
		
    <p>To find out more about this plugin and other Wordpress-related work by David Herron,
	visit <a href="http://davidherron.com/wordpress">his home page</a>.</p>
	
	<p>I am very glad that you like this plugin.  Your support is greatly appreciated.
	Please make a donation using the button below:</p>
	
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="NJUEG56USPC72">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	</div>
<?php
}


add_filter( 'plugin_row_meta', 'dh_nf_row_meta', 10, 2 );

function dh_nf_row_meta( $links, $file ) {
	if ( strpos( $file, 'nofollow-external-link.php' ) !== false ) {
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NJUEG56USPC72">Donate</a>';
		$links[] = '<a href="https://github.com/robogeek/wp-nofollow">Contribute</a>';
	}
	return $links;
}

function dh_nf_admin_style() {
	global $pluginsURI;
	wp_register_style( 'dh_nf_admin_css', esc_url(plugins_url( 'css/admin-style.css', __FILE__ )) , false, '1.0' );
	wp_enqueue_style( 'dh_nf_admin_css' );
}

add_action( 'admin_enqueue_scripts', 'dh_nf_admin_style' );

add_action('admin_menu', 'dh_nf_plugin_menu');
add_action( 'admin_init', 'register_dh_nf_settings' );

function register_dh_nf_settings() {
	register_setting( 'dh-nf-settings-group', 'dh_nf_whitelist_domains' );
	register_setting( 'dh-nf-settings-group', 'dh_nf_blacklist_domains' );
	register_setting( 'dh-nf-settings-group', 'dh_nf_icons_before_after' );
	register_setting( 'dh-nf-settings-group', 'dh_nf_target_blank' );
	register_setting( 'dh-nf-settings-group', 'dh_nf_show_extlink' );
	register_setting( 'dh-nf-settings-group', 'dh_nf_show_favicon' );
}

function dh_nf_plugin_menu() {
	add_options_page('External links rel=nofollow, open in new window, favicon', 'External Links nofollow, etc',
					 'manage_options', 'dh_nf_option_page', 'dh_nf_option_page_fn');
}


function dh_nf_option_page_fn() {
	$dh_nf_whitelist_domains = get_option('dh_nf_whitelist_domains');
	$dh_nf_blacklist_domains = get_option('dh_nf_blacklist_domains');
	$dh_nf_icons_before_after = get_option('dh_nf_icons_before_after');
	$dh_nf_target_blank = get_option('dh_nf_target_blank');
	$dh_nf_show_extlink = get_option('dh_nf_show_extlink');
	$dh_nf_show_favicon = get_option('dh_nf_show_favicon');
	?>
	<div class="wrap">
		<h2>External links rel=nofollow, open in new window, favicon</h2>
		<div class="content_wrapper">
			<div class="left">
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php settings_fields( 'dh-nf-settings-group' ); ?>
                    
                    <div id="accordion">
                        
                    <h3>Control rel=nofollow</h3>
                    <div>
                
                        <p>By default all external (outbound) links will have rel=nofollow added.</p>
                        
                        <div>
                            <strong>White list</strong>: Domains which will never have rel=nofollow
                            <textarea name="dh_nf_whitelist_domains" id="dh_nf_whitelist_domains" class="large-text" placeholder="mydomain.com, my-domain.org, another-domain.net"><?php echo $dh_nf_whitelist_domains?></textarea>
                            <br /><em>Domain name <code>MUST BE</code> comma(,) separated. <!--<br />Example: facebook.com, google.com, youtube.com-->Don't need to add <code>http://</code> or <code>https://</code><br /><code>rel="nofollow"</code> will not added to "White List Domains"</em>
                        </div>
                        
                        <div>
                            <strong>Black list</strong>: Domains which will always have rel=nofollow
                            <textarea name="dh_nf_blacklist_domains" id="dh_nf_blacklist_domains" class="large-text" placeholder="mydomain.com, my-domain.org, another-domain.net"><?php echo $dh_nf_blacklist_domains?></textarea>
                            <br /><em>Domain name <code>MUST BE</code> comma(,) separated. <!--<br />Example: facebook.com, google.com, youtube.com-->Don't need to add <code>http://</code> or <code>https://</code><br /><code>rel="nofollow"</code> will be added to "Black List Domains"</em>
                        </div>
                    </div>
					
                    <h3>Open links in new window/tab?</h3>
                    <div>
                        <input type="checkbox" name="dh_nf_target_blank" value="_blank" <?php
                        if (!empty($dh_nf_target_blank) && $dh_nf_target_blank === "_blank") {
                            ?>checked<?php
                        }
                        ?> > Set target=_blank on external links?
                    </div>
                    
                    <h3>Show icon on external links?</h3>
                    <div>
                        <input type="checkbox" name="dh_nf_show_extlink" value="show" <?php
                        if (!empty($dh_nf_show_extlink) && $dh_nf_show_extlink === "show") {
                            ?>checked<?php
                        }
                        ?> > Show external link icon?
						
						<br/>
                        <input type="checkbox" name="dh_nf_show_favicon" value="show" <?php
                        if (!empty($dh_nf_show_favicon) && $dh_nf_show_favicon === "show") {
                            ?>checked<?php
                        }
                        ?> > Show favicon for destination site?
						
						<br/>
                    
						<p>Show icons before or after link?</p>
                        <input type="radio" name="dh_nf_icons_before_after" value="before" <?php
                        if (!empty($dh_nf_icons_before_after) && $dh_nf_icons_before_after === "before") {
                            ?>checked<?php
                        }
                        ?> >Before
                        <input type="radio" name="dh_nf_icons_before_after" value="after" <?php
                        if (!empty($dh_nf_icons_before_after) && $dh_nf_icons_before_after === "after") {
                            ?>checked<?php
                        }
                        ?> >After
                    </div>
                    
                    </div><!-- accordion -->
                    
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</form>
			</div>
			<div class="right">
				<?php dh_nf_admin_sidebar(); ?>
			</div>
		</div>
	</div>
<?php
}
