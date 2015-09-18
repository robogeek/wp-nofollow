<?php
/*
Plugin Name: DSHNofollow 
Plugin URI: http://davidherron.com
Description: Control which external links have <code>rel=&quot;nofollow&quot;</code> and <code>target=&quot;_blank&quot;</code> aded to them.  It can be configured so all external links get these attributes, and a white-list and black-list give finer grained control.  The <strong>white list domains</strong>, if specified, will not to get the <code>rel=&quot;nofollow&quot;</code> attribute.  The <strong>black list domains</strong>, if specified, is a precise list of the domains which get the <code>rel=&quot;nofollow&quot;</code> attribute.  If no black list is specified, then all external links are nofollow'd (unless the domain is in the white list).
Version: 1.0.5
Author: David Herron
Author URI: http://davidherron.com
License: GPL2
*/

function dh_nf_admin_sidebar() {

	$banners = array(
		
	);
	//shuffle( $banners );
	?>
	<div class="dh_admin_banner">
	<?php
	$i = 0;
	foreach ( $banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '"><img width="261" height="190" src="' . plugins_url( 'images/' . $banner['img'], __FILE__ ) . '" alt="' . esc_attr( $banner['alt'] ) . '"/></a><br/><br/>';
		$i ++;
	}
	?>
	</div>
<?php
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
}

function dh_nf_plugin_menu() {
	add_options_page('Nofollow for external link', 'NoFollow ExtLink',
			 'manage_options', 'dh_nf_option_page', 'dh_nf_option_page_fn');
}

function dh_nf_option_page_fn() {
	$dh_nf_whitelist_domains = get_option('dh_nf_whitelist_domains');
	$dh_nf_blacklist_domains = get_option('dh_nf_blacklist_domains');
	?>
	<div class="wrap">
	<h2>Nofollow for external link Options</h2>
	<div class="content_wrapper">
	<div class="left">
	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php settings_fields( 'dh-nf-settings-group' ); ?>
		<table class="form-table">
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
			// TBD Control this with an Admin option
			if (empty($a->getAttribute('target'))) {
			    $a->setAttribute('target', '_blank');
			}
			
			// Add the favicon
			// TBD Control this with an Admin option
            if (!$hasImages && !$a->attributes->getNamedItem('data-no-favicon')) {
                $img = $html->createElement('img');
                $img->setAttribute('src', 'http://www.google.com/s2/favicons?domain=' . $urlParts['host']);
                $img->setAttribute('style', 'display: inline-block; padding-right: 4px;');
                $a->insertBefore($img, $a->firstChild);
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
