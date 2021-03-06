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
    $url = "//ajax.googleapis.com/ajax/libs/jqueryui/" . $queryui->ver . "/themes/smoothness/jquery-ui.css";
    wp_enqueue_style('jquery-ui-start', $url, false, null);
}
add_action('admin_enqueue_scripts', 'dh_nf_admin_enqueue_scripts');

function dh_nf_admin_init() {
    add_editor_style(DHNFURL . 'css/admin-style.css');
}
add_action('admin_init', 'dh_nf_admin_init');

function dh_nf_admin_sidebar() {

	?>
	<div class="dh_nf_admin_banner">

    <p>To find out more about this plugin and other Wordpress-related work by David Herron,
	visit <a href="https://davidherron.com/wordpress">his home page</a>.</p>

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


add_filter('plugin_row_meta', 'dh_nf_row_meta', 10, 2);

function dh_nf_row_meta($links, $file) {
	if (strpos($file, 'nofollow-external-link.php') !== false) {
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NJUEG56USPC72">Donate</a>';
		$links[] = '<a href="https://github.com/robogeek/wp-nofollow">Contribute</a>';
	}
	return $links;
}

function dh_nf_admin_style() {
	global $pluginsURI;
	wp_register_style('dh_nf_admin_css', esc_url(plugins_url( 'css/admin-style.css', __FILE__ )) , false, '1.0');
	wp_enqueue_style('dh_nf_admin_css');
}

add_action('admin_enqueue_scripts', 'dh_nf_admin_style');

add_action('admin_menu', 'dh_nf_plugin_menu');
add_action('admin_init', 'register_dh_nf_settings');

function register_dh_nf_settings() {
	register_setting('dh-nf-settings-nofollow', 'dh_nf_whitelist_domains');
	register_setting('dh-nf-settings-nofollow', 'dh_nf_blacklist_domains');
	register_setting('dh-nf-settings-target', 'dh_nf_target_blank');
	register_setting('dh-nf-settings-icons', 'dh_nf_icons_before_after');
	register_setting('dh-nf-settings-icons', 'dh_nf_show_extlink');
	register_setting('dh-nf-settings-icons', 'dh_nf_show_favicon');

	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_br');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_ca');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_cn');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_com');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_com_au');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_co_jp');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_co_uk');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_de');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_es');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_fr');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_in');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_it');
	register_setting('dh-nf-settings-amazon', 'dh_nf_affproduct_amazon_mx');

	register_setting('dh-nf-settings-amazon-buy-now', 'dh_nf_amazon_buynow_target');
	register_setting('dh-nf-settings-amazon-buy-now', 'dh_nf_amazon_buynow_display');

	register_setting('dh-nf-settings-rakuten', 'dh_nf_affproduct_rakuten_id');
	register_setting('dh-nf-settings-rakuten', 'dh_nf_affproduct_rakuten_mids');

	register_setting('dh-nf-settings-zazzle', 'dh_nf_affproduct_zazzle_id');

}

function dh_nf_plugin_menu() {
	add_options_page('External & Affiliate Links Processor, rel=nofollow, open in new window, favicon',
					 'External & Affiliate Links Processor',
					 'manage_options', 'dh_nf_option_page', 'dh_nf_option_page_fn');
}


function dh_nf_option_page_fn() {
	$dh_nf_whitelist_domains = get_option('dh_nf_whitelist_domains');
	$dh_nf_blacklist_domains = get_option('dh_nf_blacklist_domains');
	$dh_nf_icons_before_after = get_option('dh_nf_icons_before_after');
	$dh_nf_target_blank = get_option('dh_nf_target_blank');
	$dh_nf_show_extlink = get_option('dh_nf_show_extlink');
	$dh_nf_show_favicon = get_option('dh_nf_show_favicon');

	$dh_nf_affproduct_amazon_com_au = get_option('dh_nf_affproduct_amazon_com_au');
	$dh_nf_affproduct_amazon_br     = get_option('dh_nf_affproduct_amazon_br');
	$dh_nf_affproduct_amazon_ca     = get_option('dh_nf_affproduct_amazon_ca');
	$dh_nf_affproduct_amazon_cn     = get_option('dh_nf_affproduct_amazon_cn');
	$dh_nf_affproduct_amazon_com    = get_option('dh_nf_affproduct_amazon_com');
	$dh_nf_affproduct_amazon_co_jp  = get_option('dh_nf_affproduct_amazon_co_jp');
	$dh_nf_affproduct_amazon_co_uk  = get_option('dh_nf_affproduct_amazon_co_uk');
	$dh_nf_affproduct_amazon_de     = get_option('dh_nf_affproduct_amazon_de');
	$dh_nf_affproduct_amazon_es     = get_option('dh_nf_affproduct_amazon_es');
	$dh_nf_affproduct_amazon_fr     = get_option('dh_nf_affproduct_amazon_fr');
	$dh_nf_affproduct_amazon_in     = get_option('dh_nf_affproduct_amazon_in');
	$dh_nf_affproduct_amazon_it     = get_option('dh_nf_affproduct_amazon_it');
	$dh_nf_affproduct_amazon_mx     = get_option('dh_nf_affproduct_amazon_mx');

	$dh_nf_amazon_buynow_target     = get_option('dh_nf_amazon_buynow_target');
	$dh_nf_amazon_buynow_display    = get_option('dh_nf_amazon_buynow_display');

	$dh_nf_affproduct_rakuten_id    = get_option('dh_nf_affproduct_rakuten_id');
	$dh_nf_affproduct_rakuten_mids  = get_option('dh_nf_affproduct_rakuten_mids');

	$dh_nf_affproduct_zazzle_id     = get_option('dh_nf_affproduct_zazzle_id');

	?>
	<div class="wrap">
		<h2>External &amp; Affiliate Links Processor, rel=nofollow, open in new window, favicon</h2>
		<div class="content_wrapper">
			<div class="left">

                <div id="accordion">

                    <h3>Control rel=nofollow</h3>
                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-nofollow'); ?>

                        <p>By default (nothing in either list) all external (outbound) links will have rel=nofollow added.  If you have a blacklist, the only domains to get rel=nofollow are the ones in the blacklist -- unless the domain is in the whitelist.  If you have no blacklist, then the whitelist domains do not receive rel=nofollow and every other domain does.</p>

                        <div>
                            <strong>White list</strong>: Domains which will never have rel=nofollow
                            <textarea name="dh_nf_whitelist_domains"
									  id="dh_nf_whitelist_domains"
									  class="large-text"
									  placeholder="mydomain.com, my-domain.org, another-domain.net"><?php
									  echo $dh_nf_whitelist_domains;
							?></textarea>
                            <br /><em>Domain name <code>MUST BE</code> comma(,) separated. <!--<br />Example: facebook.com, google.com, youtube.com-->Don't need to add <code>http://</code> or <code>https://</code><br /><code>rel="nofollow"</code> will not added to "White List Domains"</em>
                        </div>

                        <div>
                            <strong>Black list</strong>: Domains which will always have rel=nofollow
                            <textarea name="dh_nf_blacklist_domains"
									  id="dh_nf_blacklist_domains"
									  class="large-text"
									  placeholder="mydomain.com, my-domain.org, another-domain.net"><?php
									  echo $dh_nf_blacklist_domains;
							?></textarea>
                            <br /><em>Domain name <code>MUST BE</code> comma(,) separated. <!--<br />Example: facebook.com, google.com, youtube.com-->Don't need to add <code>http://</code> or <code>https://</code><br /><code>rel="nofollow"</code> will be added to "Black List Domains"</em>
                        </div>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>

                    <h3>Open links in new window/tab?</h3>
                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-target'); ?>
                        <input type="checkbox" name="dh_nf_target_blank" value="_blank" <?php
                        if (!empty($dh_nf_target_blank) && $dh_nf_target_blank === "_blank") {
                            ?>checked<?php
                        }
                        ?> > Set target=_blank on external links?
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>

                    <h3>Show icon on external links?</h3>
                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-icons'); ?>
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
						<br/>

						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>

                    <h3>Amazon Affiliate Links</h3>

                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-amazon'); ?>
						<div>
						<label for="dh_nf_affproduct_amazon_com_au">
							<strong><?php _e('Amazon.COM.AU (Australia) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_com_au"
							   name="dh_nf_affproduct_amazon_com_au"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_com_au); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_br">
							<strong><?php _e('Amazon.BR (Brazil) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_br"
							   name="dh_nf_affproduct_amazon_br"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_br); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_ca">
							<strong><?php _e('Amazon.CA (Canada) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_ca"
							   name="dh_nf_affproduct_amazon_ca"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_ca); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_cn">
							<strong><?php _e('Amazon.CN (China) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_cn"
							   name="dh_nf_affproduct_amazon_cn"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_cn); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_com">
							<strong><?php _e('Amazon.COM (USA) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_com"
							   name="dh_nf_affproduct_amazon_com"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_com); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_co_jp">
							<strong><?php _e('Amazon.CO.JP (Japan) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_co_jp"
							   name="dh_nf_affproduct_amazon_co_jp"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_co_jp); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_co_uk">
							<strong><?php _e('Amazon.CO.UK (United Kingdom) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_co_uk"
							   name="dh_nf_affproduct_amazon_co_uk"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_co_uk); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_de">
							<strong><?php _e('Amazon.DE (Germany) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_de"
							   name="dh_nf_affproduct_amazon_de"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_de); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_es">
							<strong><?php _e('Amazon.ES (Spain) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_es"
							   name="dh_nf_affproduct_amazon_es"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_es); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_fr">
							<strong><?php _e('Amazon.FR (France) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_fr"
							   name="dh_nf_affproduct_amazon_fr"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_fr); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_in">
							<strong><?php _e('Amazon.IN (India) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_in"
							   name="dh_nf_affproduct_amazon_in"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_in); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_it">
							<strong><?php _e('Amazon.IT (Italy) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_it"
							   name="dh_nf_affproduct_amazon_it"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_it); ?>">
						</div>
						<div>
						<label for="dh_nf_affproduct_amazon_mx">
							<strong><?php _e('Amazon.MX (Mexico) Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_amazon_mx"
							   name="dh_nf_affproduct_amazon_mx"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_amazon_mx); ?>">
						</div>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>

                    <h3>Amazon 'Buy Now' buttons</h3>
                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-amazon-buy-now'); ?>

                        <p><input type="checkbox" name="dh_nf_amazon_buynow_target" value="_blank" <?php
                        if (!empty($dh_nf_amazon_buynow_target) && $dh_nf_amazon_buynow_target === "_blank") {
                            ?>checked<?php
                        }
                        ?> > Set target=_blank on 'Buy Now' buttons?</p>

						<p>Display 'Buy Now' buttons inline?</p>
                        <input type="radio" name="dh_nf_amazon_buynow_display" value="block" <?php
                        if (!empty($dh_nf_amazon_buynow_display) && $dh_nf_amazon_buynow_display === "block") {
                            ?>checked<?php
                        }
                        ?> >Block
                        <input type="radio" name="dh_nf_amazon_buynow_display" value="inline" <?php
                        if (!empty($dh_nf_amazon_buynow_display) && $dh_nf_amazon_buynow_display === "inline") {
                            ?>checked<?php
                        }
                        ?> >Inline
						<br/>

						<p>Shortcodes are provided for creating an add-directly-to-shopping-cart button.  By assisting your readers to add products directly to their shopping cart, it's claimed that Amazon will insert a 90 day cookie in the readers browser as opposed to the 1 day cookie that's normally used.  Many claim this will expand your earning potential through Amazon.</p>
						<p>The shortcode's supported are as follows.</p>

						<pre>
Canada: [extlink_amazon_ca_buy asin="... the ASIN for a product ..."]

USA: [extlink_amazon_com_buy asin="... the ASIN for a product ..."]

Japan: [extlink_amazon_co_jp_buy asin="... the ASIN for a product ..."]

United Kingdom: [extlink_amazon_co_uk_buy asin="... the ASIN for a product ..."]

Germany: [extlink_amazon_de_buy asin="... the ASIN for a product ..."]

Spain: [extlink_amazon_es_buy asin="... the ASIN for a product ..."]

France: [extlink_amazon_fr_buy asin="... the ASIN for a product ..."]

Italy: [extlink_amazon_it_buy asin="... the ASIN for a product ..."]
						</pre>

						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>

                    <h3>Rakuten Affiliate Links</h3>

                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-rakuten'); ?>

						<div>
						<label for="dh_nf_affproduct_rakuten_id"><strong><?php _e( 'Linkshare/Rakuten Affiliate Code:' ); ?></strong></label>
						<input id="dh_nf_affproduct_rakuten_id" name="dh_nf_affproduct_rakuten_id" type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_rakuten_id); ?>">
						</div>

						<p>Enter your Linkshare/Rakuten affiliate code.  To get this code:</p>
						<ul style="list-style: disc; list-style-position: inside;">
							<li>Sign up with the <a href="//click.linksynergy.com/fs-bin/click?id=PPTIpcZ17qI&offerid=311675.10000156&type=3&subid=0&LSNSUBSITE=LSNSUBSITE">Rakuten Affiliate Marketing program</a></li>
							<li>Join one or more programs</li>
							<li>Click on Programs => My Advertisers</li>
							<li>Click on one of them, then click on Get Links</li>
							<li>Select one of the link types, select one of the links offered</li>
							<li>Click on <em>Get Link</em> and in the dialog box look the URL provided.  Copy the value of the <tt>id=</tt> parameter in the URL.</li>
						</ul>
						<p>Every link Rakuten generates for you has the same <tt>id=</tt> parameter.  It's different from the affiliate ID that shows elsewhere on the Rakuten dashboard.
						</p>

						<textarea name="dh_nf_affproduct_rakuten_mids" width="100%" rows="10" cols="50"><?php
							echo $dh_nf_affproduct_rakuten_mids;
						?></textarea>

						<p>Enter Merchant ID's for the Rakuten programs you've joined.  For each program
						add a line of text in the format "domain merchantID".
						The simplest way to get the Merchant ID is that while viewing your
						list of merchants, simply hovering over the merchant name shows the Merchant ID.
						The Merchant ID number is also shown on the program information page for each program.</p>

<p>This ends up looking somewhat like this:</p>

<pre>
refurb.io 40098
dreamstime.com 39291
marketing.rakuten.com 560
rakuten.com 36342
buy.com 36342
shambhala.com 35631
</pre>


						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>


                    <h3>Zazzle Affiliate Links</h3>

                    <div>
						<form method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('dh-nf-settings-zazzle'); ?>
						<div>
						<label for="dh_nf_affproduct_zazzle_id">
							<strong><?php _e('Zazzle.com Affiliate Code:'); ?></strong>
						</label>
						<input id="dh_nf_affproduct_zazzle_id"
							   name="dh_nf_affproduct_zazzle_id"
							   type="text"
							   value="<?php echo esc_attr($dh_nf_affproduct_zazzle_id); ?>">
						</div>

						<p>Enter your Associate ID code from Zazzle.  It's easy to get one - simply
						<a href="//www.zazzle.com/lgn/signin?rf=238131690118791619">sign up for a new account</a>,
						log into your account, then go to the
						<a href="//www.zazzle.com/my/associate/associate?rf=238131690118791619">Associate Center</a>.</p>

						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
                    </div>



                </div><!-- accordion -->

			</div>
			<div class="right">
				<?php dh_nf_admin_sidebar(); ?>
			</div>
		</div>
	</div>
<?php
}
