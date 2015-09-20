=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://example.com/
Tags: nofollow, external links, target blank
Requires at least: 4.0.1
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Process outbound (external) links in content, optionally adding rel=nofollow or target=_blank attributes, and optionally adding icons.

== Description ==

This plugin controls several useful attributes of external links.  

* Control, based on the domain of the outbound link, whether or not to add `rel=nofollow`.  
* Control whether to open outbound links in new windows (`target=_blank`)
* Control whether to add visual cues for outbound links, including the favicon for the target site, and/or a generic external link icon

Unlike other `nofollow` icons which force every outbound link to be nofollow'd (or not), this plugin lets you select which domains get what treatment.  It includes two lists of domains, a white list and a black list.  Domains on the blacklist are always nofollow'd, while those on the whitelist never are.

It's useful to your visitors to let them know which links will take them off your site.  This plugin will show icons either before or after the link.  One icon is the favicon for the target site, and the other is a generic external link icon.

== Installation ==

Simply search for this plugin from the plugins section of your blog admin, use automatic install and activate Nofollow Case by Case.

A manual installation can be done by downloading the plugin from its [github repository](https://github.com/robogeek/wp-nofollow).

== Changelog ==

