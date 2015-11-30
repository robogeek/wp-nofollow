=== External & Affiliate Links Processor ===
Contributors: reikiman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NJUEG56USPC72
Tags: nofollow, rel nofollow, nofollow external link, nofollow external links, nofollow links, rel=nofollow, nofollow content links, dofollow, external links, external link, external-links, link, links, target blank, affiliate links
Requires at least: 4.0.1
Tested up to: 4.4
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Process outbound (external) links to make useful changes, including adding affiliate ID tags, rel=nofollow or target=_blank attributes, and adding icons as a visual cue to readers.

== Description ==

This plugin controls several useful attributes of external links.

* Add affiliate ID tags to links to sites where this makes sense
* Control, based on the domain of the outbound link, whether or not to add `rel=nofollow`.
* Control whether to open outbound links in new windows (`target=_blank`)
* Control whether to add visual cues for outbound links, including the favicon for the target site, and/or a generic external link icon

Unlike other `nofollow` plugins which force every outbound link to be nofollow'd (or not), this plugin lets you select which domains get what treatment.  It includes two lists of domains, a white list and a black list.  Domains on the blacklist are always nofollow'd, while those on the whitelist never are.

It's useful to your visitors to let them know which links will take them off your site.  This plugin will show icons either before or after the link.  One icon is the favicon for the target site, and the other is a generic external link icon.

Sometimes you want to quickly and easily make an affiliate link, requiring a correctly formatted link according to affiliate network specifications.  Services like VigLink or Skimlinks can simplify the hassle of remembering the correct formatting of each affiliate network, by using JavaScript to convert natural links into affiliated links.  While convenient, these networks take a percentage of your affiliate commissions.

This plugin lets you make a simple natural link to the destination, and the plugin rewrites it with the correct affiliate ID codes.  For certain affiliate networks.  You don't have to remember the correct formatting for the affiliate network, you simply make the link and the plugin automatically rewrites it for you (using the <a href="https://github.com/robogeek/affiliate-link-processor">AffiliateLinkProcessor</a> library).  It also interacts well if you're using VigLink or Skimlinks, because those services can continue handling whatever links this plugin does not touch.  Supported networks are:

* Amazon.com (and all known international Amazon sites)
* Sites on the Linkshare/Rakuten network
* Zazzle.com


== Installation ==

Simply search for this plugin from the plugins section of your blog admin, use automatic install and activate External Links Nofollow.

A manual installation can be done by downloading the plugin from its [github repository](https://github.com/robogeek/wp-nofollow).

== Changelog ==

= 1.4.4 =
* Add shortcodes for Amazon direct-to-shopping-cart buttons

= 1.4.3 =
* Tested to work on Wordpress 4.4
* Improving documentation on admin page
* No code changes

= 1.4.2 =
* With apologies to Canada, belatedly added support for amazon.ca

= 1.4.1 =
* Change filter to run at low priority, so it runs after shortcodes

= 1.4.0 =
* Support adding affiliate ID tags to affiliate-able links
* Rebranding to make the name clearer

= 1.3.0 =
* Redo admin screen to have accordion effect

= 1.2.3 =
* Remove some admin screen text, no other change

= 1.2.2 =
* Fix the URL to reference plugin files

= 1.2.1 =
* Cleanup minor problems after deployment to wordpress.org

= 1.2 =
* Initial revision for wordpress.org
