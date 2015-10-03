<?php
/*
 *Plugin Name: External links manipulator - nofollow, open in new tab, favicon
 Plugin URI: http://davidherron.com/content/external-links-nofollow-favicon-open-external-window-etc-wordpress
 Description: Process outbound (external) links in content, optionally adding rel=nofollow or target=_blank attributes, and optionally adding icons.
 Version: 1.3.0
 Author: David Herron
 Author URI: http://davidherron.com/wordpress
 slug: external-links-nofollow
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


define("DHNFDIR",plugin_dir_path( __FILE__ ));
define("DHNFURL",plugin_dir_url( __FILE__ ));
// define("DHNFSLUG",dirname(plugin_basename(__FILE__)));

if (is_admin()) {
	require_once DHNFDIR.'admin.php';
}

add_filter('the_content', 'dh_nf_urlparse2');

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
			$curtarget = $a->getAttribute('target');
			if (!empty($dh_nf_target_blank)
			 && $dh_nf_target_blank === "_blank"
			 && (!isset($curtarget) || $curtarget == '')
			) {
				$a->setAttribute('target', '_blank');
			}
			
			// Add the favicon
			if (!$hasImages
			 && !empty($dh_nf_show_favicon)
			 && $dh_nf_show_favicon === "show"
			 && !$a->attributes->getNamedItem('data-no-favicon')
			) {
				$img = $html->createElement('img');
				$img->setAttribute('src', 'http://www.google.com/s2/favicons?domain=' . $urlParts['host']);
				$img->setAttribute('style', 'display: inline-block; padding-right: 4px;');
				if (empty($dh_nf_icons_before_after)
				 || (!empty($dh_nf_icons_before_after) && $dh_nf_icons_before_after === "before")
				) {
					$a->insertBefore($img, $a->firstChild);
				} else {
					$a->appendChild($img);
				}
			}
				
			// Add external link icon
			if (!empty($dh_nf_show_extlink)
			 && $dh_nf_show_extlink === "show"
			) {
				$img = $html->createElement('img');
				$img->setAttribute('src', esc_url(plugins_url( 'images/extlink.png', __FILE__ )));
				if (empty($dh_nf_icons_before_after)
				 || (!empty($dh_nf_icons_before_after) && $dh_nf_icons_before_after === "before")
				) {
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

function dh_nf_domainEndsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && stripos($haystack, $needle, $temp) !== FALSE);
}
