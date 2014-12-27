=== BNS Theme Details ===
Contributors: cais
Donate link: http://buynowshop.com/
Tags: themes, counter, plugin, widget, shortcode, details, download, author, update, rating
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 0.4
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Displays theme specific details such as download count, last update, author, etc.

== Description ==

This plugin can be used to display the recent download count of a theme, as well as various other details such as the author and when it was last updated. It also includes a link to the WordPress Theme repository if it exists.

== Installation ==

1. Go to 'Plugins' menu under your Dashboard
2. Click on the 'Add New' link
3. Search for BNS Theme Details
4. Install.
5. Activate through the 'Plugins' menu.
6. Place the BNS Theme Details widget appropriately in the Appearance > Widgets section of the dashboard; or use the default `[ bns_theme_details ]` shortcode (without the spaces) in a page or post.

Reading this article for further assistance with plugin installation: http://wpfirstaid.com/2009/12/plugin-installation/

/** ---- */

Shortcode Usage:

* `[ bns_theme_details ]` - this is the default (less the spaces) which will display the current active theme on the site (provided it is hosted on the WordPress repository).

Shortcode parameters (and their defaults):

* `title => __return_null()` ... returns nothing
* `theme_slug => wp_get_theme()->get_template()` ... uses the current theme; the theme name can also be used in place of its slug provided they are similar
* `use_screenshot_link => true` ... displays main screenshot
* `show_name => true` ... displays the theme name
* `show_author => true` ... displays the author name
* `show_last_updated => true` ... displays when the theme was last updated
* `show_current_version => true` ... displays the current theme version
* `show_rating => true` ... displays the current "star" rating of the theme
* `show_number_of_ratings => true` ... displays the number of ratings the theme has received
* `show_description => true` ... displays theme description (this is false by default in the widget section)
* `show_downloaded_count => true` ... displays the total download count
* `use_download_link => true` ... displays a download link pointing to the current version of the theme in the WordPress Theme repository

== Frequently Asked Questions ==
Q: Why am I not seeing any Theme Details?

This plugin currently only handles those themes that can be found in the WordPress Theme repository.

== Screenshots ==
* To be developed

== Other Notes ==
* Copyright 2014  Edward Caissie  (email : edward.caissie@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 2,
  as published by the Free Software Foundation.

  You may NOT assume that you can use any other version of the GPL.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  The license for this software can also likely be found here:
  http://www.gnu.org/licenses/gpl-2.0.html

== Upgrade Notice ==
Please stay current with your WordPress installation, your active theme, and your plugins.

== Changelog ==
= 0.4 =
* Released ...
* Added "in plugin update message" functionality
* Added changelog to output (requires theme to use `changelog.txt` file)
* Updated `exit_message` if WordPress version is too low
* Corrected shortcode filter reference to indicate `bns_theme_details`
* Modified `textdomain` to use full plugin slug `bns-theme-details`
* Updated required version to 3.6 in reference to the shortcode filter option
* Wrapped `<img />` tag in a `<p />` tag for better display compatibility

= 0.3 =
* Released February 2014
* Correct shortcode parameters not recognizing boolean checks

= 0.2 =
* Released February 2014
* Added theme description to output

= 0.1 =
* Initial release - February 2014