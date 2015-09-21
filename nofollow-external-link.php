<?php
/*
Plugin Name: External links nofollow, open in new tab, favicon 
Plugin URI: http://davidherron.com/content/external-links-nofollow-favicon-open-external-window-etc-wordpress
Description: Process outbound (external) links in content, optionally adding rel=nofollow or target=_blank attributes, and optionally adding icons.
Version: 1.0.12
Author: David Herron
Author URI: http://davidherron.com/wordpress
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

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

function dh_nf_admin_sidebar() {

	?>
	<div class="dh_nf_admin_banner">
	I am very glad that you like this plugin.
	Your support is greatly appreciated.
	Please make a donation using the button below:
	
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
	}
	return $links;
}

function dh_nf_admin_style() {
	global $pluginsURI;
	wp_register_style( 'dh_nf_admin_css', plugins_url( 'wp-nofollow/css/admin-style.css' ) , false, '1.0' );
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
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Set target=_blank on external links?</th>
				<td>
				    <input type="checkbox" name="dh_nf_target_blank" value="_blank" <?php
				        if (!empty($dh_nf_target_blank) && $dh_nf_target_blank === "_blank") {
				            ?>checked<?php
				        }
				    ?> > 
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Show external link icon?</th>
				<td>
				    <input type="checkbox" name="dh_nf_show_extlink" value="show" <?php
				        if (!empty($dh_nf_show_extlink) && $dh_nf_show_extlink === "show") {
				            ?>checked<?php
				        }
				    ?> > 
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Show external link favicon?</th>
				<td>
				    <input type="checkbox" name="dh_nf_show_favicon" value="show" <?php
				        if (!empty($dh_nf_show_favicon) && $dh_nf_show_favicon === "show") {
				            ?>checked<?php
				        }
				    ?> > 
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Show icons before or after link?</th>
				<td>
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
				</td>
			</tr>
			<tr valign="top"><td colspan="2">
				Control the rel=nofollow attribute.  By default all external (outbound) links will have
				rel=nofollow added.  Any domains listed in the White list will never have this attribute,
				while any in the Black list will always have this attribute.
			</td></tr>
			<tr valign="top">
			<th scope="row">White List Domains</th>
			<td><textarea name="dh_nf_whitelist_domains" id="dh_nf_whitelist_domains" class="large-text" placeholder="mydomain.com, my-domain.org, another-domain.net"><?php echo $dh_nf_whitelist_domains?></textarea>
            <br /><em>Domain name <code>MUST BE</code> comma(,) separated. <!--<br />Example: facebook.com, google.com, youtube.com-->Don't need to add <code>http://</code> or <code>https://</code><br /><code>rel="nofollow"</code> will not added to "White List Domains"</em></td>
			</tr>
			<tr valign="top">
			<th scope="row">Blacklist Domains</th>
			<td><textarea name="dh_nf_blacklist_domains" id="dh_nf_blacklist_domains" class="large-text" placeholder="mydomain.com, my-domain.org, another-domain.net"><?php echo $dh_nf_blacklist_domains?></textarea>
            <br /><em>Domain name <code>MUST BE</code> comma(,) separated. <!--<br />Example: facebook.com, google.com, youtube.com-->Don't need to add <code>http://</code> or <code>https://</code><br /><code>rel="nofollow"</code> will be added to "Black List Domains"</em></td>
			</tr>
		</table>
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

add_filter( 'the_content', 'dh_nf_urlparse2');

function dh_nf_urlparse2($content) {
    
	//$ownDomain = get_option('home');
	$ownDomain = $_SERVER['HTTP_HOST'];
			
	// whitelist
	$white_list_domains_list = array();
	if(get_option('dh_nf_whitelist_domains')!='') {
		$white_list_domains_list = explode(",",get_option('dh_nf_whitelist_domains'));
	}
	
	// blacklist
	$black_list_domains_list = array();
	if(get_option('dh_nf_blacklist_domains')!='') {
		$black_list_domains_list = explode(",",get_option('dh_nf_blacklist_domains'));
	}
	
	$dh_nf_icons_before_after = get_option('dh_nf_icons_before_after');
	$dh_nf_target_blank = get_option('dh_nf_target_blank');
	$dh_nf_show_extlink = get_option('dh_nf_show_extlink');
	$dh_nf_show_favicon = get_option('dh_nf_show_favicon');
			
    try {
        $html = new DOMDocument(null, 'UTF-8');
        @$html->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $content);

        foreach ($html->getElementsByTagName('a') as $a) {

            // Skip if there's no href=
            $url = $a->attributes->getNamedItem('href');
            if (!$url) {
                continue;
            }
            
            // Skip if the link is internal, or if it has "#whatever"
            $urlParts = parse_url($url->textContent);
            if (!$urlParts || empty($urlParts['host']) || !empty($urlParts['fragment'])) {
                continue;
            }
            
            // Skip if the link points to our own domain (hence, is local)
            if (dh_nf_domainEndsWith($urlParts['host'], $ownDomain)) {
                continue;
            }
            
            $hasImages = false;            
            $imgs = $a->getElementsByTagName('img');
            if ($imgs->length > 0) {
                $hasImages = true;
            }

			// true means add nofollow, false means not (is in whitelist)
			$domainNoFollow = true;
			
			if (count($white_list_domains_list) > 0) {
				$white_list_domains_list = array_filter($white_list_domains_list);
				foreach ($white_list_domains_list as $domain) {
					$domain = trim($domain);
					if ($domain != '') {
						$domainCheck = dh_nf_domainEndsWith($urlParts['host'], $domain);
						if ($domainCheck === false) {
							continue;
						} else {
							$domainNoFollow = false;
							break;
						}
					}
				}	
			}
			
			// false means not in BlackList, true means in BlackList & add nofollow
			$domainInBlackList = false;
			$noBlackList = false;
			if (count($black_list_domains_list) > 0) {
				$black_list_domains_list = array_filter($black_list_domains_list);
				foreach ($black_list_domains_list as $domain) {
					$domain = trim($domain);
					if ($domain != '') {
						$domainCheck = dh_nf_domainEndsWith($urlParts['host'], $domain);
						if($domainCheck === false) {
							continue;
						} else {
							$domainInBlackList = true;
							$domainNoFollow = true;
							break;
						}
					}
				}
				if (!$domainInBlackList) $domainNoFollow = false;
			} else {
				$noBlackList = true;
			}
			
			// Add rel=nofollow
			if ($domainNoFollow || $domainInBlackList) {
			    $a->setAttribute('rel', 'nofollow');
			}
			
			// $a->setAttribute('data-domain-no-follow', $domainNoFollow ? "true" : "false");
			// $a->setAttribute('data-domain-in-black-list', $domainInBlackList ? "true" : "false");
			
			// Add target=_blank if there's no target=
			if (!empty($dh_nf_target_blank) 
			  && $dh_nf_target_blank === "_blank"
			  && empty($a->getAttribute('target'))) {
			    $a->setAttribute('target', '_blank');
			}
			
            // Add the favicon
            if (!$hasImages
             && !empty($dh_nf_show_favicon)
             && $dh_nf_show_favicon === "show"
             && !$a->attributes->getNamedItem('data-no-favicon')) {
                $img = $html->createElement('img');
                $img->setAttribute('src', 'http://www.google.com/s2/favicons?domain=' . $urlParts['host']);
                $img->setAttribute('style', 'display: inline-block; padding-right: 4px;');
                if (empty($dh_nf_icons_before_after)
                || (!empty($dh_nf_icons_before_after) && $dh_nf_icons_before_after === "before")) {
                    $a->insertBefore($img, $a->firstChild);
                } else {
                    $a->appendChild($img);
                }
            }
            
            // Add external link icon
            if (!empty($dh_nf_show_extlink)
             && $dh_nf_show_extlink === "show") {
                $img = $html->createElement('img');
                $img->setAttribute('src', plugins_url( 'wp-nofollow/images/extlink.png' ));
                if (empty($dh_nf_icons_before_after)
                || (!empty($dh_nf_icons_before_after) && $dh_nf_icons_before_after === "before")) {
                    $a->insertBefore($img, $a->firstChild);
                } else {
                    $a->appendChild($img);
                }
            }
        }
        return $html->saveHTML();
    } catch (Exception $e) {
        return $content;
    }
}

function dh_nf_url_parse( $content ) {

	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
		if( !empty($matches) ) {
			
			//$ownDomain = get_option('home');
			$ownDomain = $_SERVER['HTTP_HOST'];
			
			// whitelist
			$white_list_domains_list = array();
			if(get_option('dh_nf_whitelist_domains')!='') {
				$white_list_domains_list = explode(",",get_option('dh_nf_whitelist_domains'));
			}
			
			// blacklist
			$black_list_domains_list = array();
			if(get_option('dh_nf_blacklist_domains')!='') {
				$black_list_domains_list = explode(",",get_option('dh_nf_blacklist_domains'));
			}
			
			for ($i=0; $i < count($matches); $i++)
			{
			
				$tag  = $matches[$i][0];
				$tag2 = $matches[$i][0];
				$url  = $matches[$i][0];
					
				// bypass #more type internal link
				$res = preg_match('/href(\s)*=(\s)*"#[a-zA-Z0-9-_]+"/',$url);
				if($res) {
					continue;
				}
				
				$pos = strpos($url,$ownDomain);
				if ($pos === false) {
					
					// true means add nofollow, false means not (is in whitelist)
					$domainNoFollow = true;
					
					if(count($white_list_domains_list)>0) {
						$white_list_domains_list = array_filter($white_list_domains_list);
						foreach($white_list_domains_list as $domain) {
							$domain = trim($domain);
							if($domain!='') {
								$domainCheck = strpos($url,$domain);
								if($domainCheck === false) {
									continue;
								} else {
									$domainNoFollow = false;
									break;
								}
							}
						}	
					}
					
					// false means not in BlackList, true means in BlackList & add nofollow
					$domainInBlackList = false;
					$noBlackList = false;
					if(count($black_list_domains_list)>0) {
						$black_list_domains_list = array_filter($black_list_domains_list);
						foreach($black_list_domains_list as $domain) {
							$domain = trim($domain);
							if($domain!='') {
								$domainCheck = strpos($url,$domain);
								if($domainCheck === false) {
									continue;
								} else {
									$domainInBlackList = true;
									$domainNoFollow = true;
									break;
								}
							}
						}
						if (!$domainInBlackList) $domainNoFollow = false;
					} else {
						$noBlackList = true;
					}
					
					$noFollow = '';
	
					// add target=_blank to url
					$pattern = '/target\s*=\s*"\s*_blank\s*"/';
					preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
					if( count($match) < 1 )
						$noFollow .= ' target="_blank"';
						
					//exclude domain or add nofollow
					if($domainNoFollow || $domainInBlackList) {
						$pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
						preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
						if( count($match) < 1 )
							$noFollow .= ' rel="nofollow"';
					}
					
					// add nofollow/target attr to url
					$tag = rtrim ($tag,'>');
					$tag .= $noFollow.'>';
					$content = str_replace($tag2,$tag,$content);
				}
			}
		}
	}
	
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}


function dh_nf_domainEndsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && stripos($haystack, $needle, $temp) !== FALSE);
}
