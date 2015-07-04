<?php
/*
Plugin Name: BNS Theme Details
Plugin URI: http://buynowshop.com/plugins/bns-theme-details
Description: Displays theme specific details such as download count, last update, author, etc.
Version: 0.4.1
Text Domain: bns-theme-details
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Theme Details
 *
 * This plugin can be used to display the recent download count of a theme, as
 * well as various other details such as the author and when it was last
 * updated. It also includes a link to the WordPress Theme repository if it
 * exists.
 *
 * @package        BNS_Theme_Details
 * @link           http://buynowshop.com/plugins/bns-theme-details
 * @link           https://github.com/Cais/bns-theme-details
 * @link           https://wordpress.org/plugins/bns-theme-details/
 * @version        0.4.1
 * @author         Edward Caissie <edward.caissie@gmail.com>
 * @copyright      Copyright (c) 2014-2015, Edward Caissie
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.
 *
 * You may NOT assume that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to:
 *
 *      Free Software Foundation, Inc.
 *      51 Franklin St, Fifth Floor
 *      Boston, MA  02110-1301  USA
 *
 * The license for this software can also likely be found here:
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @todo           Make the download link a button?
 * @todo           Call theme details to add Author URI and/or Theme URI links?
 */

/** Thanks to Samuel (Otto42) Wood for the code snippet inspiration. */
class BNS_Theme_Details_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * The main show ...
	 *
	 * @package BNS_Theme_Details
	 * @since   0.1
	 *
	 * @uses    BNS_Theme_Details_Widget::WP_Widget (factory)
	 * @uses    BNS_Theme_Details_Widget::load_bnstd_widget
	 * @uses    BNS_Theme_Details_Widget::bns_theme_details_shortcode
	 * @uses    (GLOBAL) wp_version
	 * @uses    __
	 * @uses    add_action
	 *
	 * @version 0.4
	 * @date    December 27, 2014
	 * Added enqueue statement for scripts and styles
	 * Added CONSTANTS for "DRY" purposes and customization paths
	 * Updated `exit_message` if WordPress version is too low
	 */
	function __construct() {

		/** Widget settings */
		$widget_ops = array(
			'classname'   => 'bns-theme-details',
			'description' => __( 'Displays theme specific details such as download count, last update, author, etc.', 'bns-theme-details' )
		);
		/** Widget control settings */
		$control_ops = array(
			'width'   => 200,
			'id_base' => 'bns-theme-details'
		);
		/** Create the widget */
		parent::__construct( 'bns-theme-details', 'BNS Theme Details', $widget_ops, $control_ops );

		/**
		 * Check installed WordPress version for compatibility
		 *
		 * @internal    Version 3.6 in reference to `shortcode_atts` filter option
		 * @link        https://developer.wordpress.org/reference/functions/shortcode_atts/
		 */
		global $wp_version;
		$exit_message = sprintf( __( 'BNS Theme Details requires WordPress version 3.6 or newer. %1$s', 'bns-theme-details' ), '<a href="http://codex.wordpress.org/Upgrading_WordPress">' . __( 'Please Update!', 'bns-theme-details' ) . '</a>' );
		$exit_message .= '<br />';
		$exit_message .= sprintf( __( 'In reference to the shortcode default attributes filter. See %1$s.', 'bns-theme-details' ), '<a href="https://developer.wordpress.org/reference/functions/shortcode_atts/">' . __( 'this link', 'bns-theme-details' ) . '</a>' );
		if ( version_compare( $wp_version, "3.6", "<" ) ) {
			exit( $exit_message );
		}
		/** End if = version compare */

		/** Define some constants to save some keying */
		if ( ! defined( 'BNSTD_URL' ) ) {
			define( 'BNSTD_URL', plugin_dir_url( __FILE__ ) );
		}
		/** End if - not defined */
		if ( ! defined( 'BNSTD_PATH' ) ) {
			define( 'BNSTD_PATH', plugin_dir_path( __FILE__ ) );
		}
		/** End if - not defined */

		/** Define location for BNS plugin customizations */
		if ( ! defined( 'BNS_CUSTOM_PATH' ) ) {
			define( 'BNS_CUSTOM_PATH', WP_CONTENT_DIR . '/bns-customs/' );
		}
		/** End if - not defined */
		if ( ! defined( 'BNS_CUSTOM_URL' ) ) {
			define( 'BNS_CUSTOM_URL', content_url( '/bns-customs/' ) );
		}
		/** End if - not defined */

		/** Enqueue Scripts and Styles */
		add_action( 'wp_enqueue_scripts', array(
			$this,
			'scripts_and_styles'
		) );

		/** Add widget */
		add_action( 'widgets_init', array( $this, 'load_bnstd_widget' ) );

		/** Add Shortcode */
		add_shortcode(
			'bns_theme_details', array(
				$this,
				'bns_theme_details_shortcode'
			)
		);

	} /** End function - constructor */


	/**
	 * Enqueue Plugin Scripts and Styles
	 *
	 * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
	 *
	 * @package BNS_Inline_Asides
	 * @since   0.4
	 *
	 * @uses    (CONSTANT)   BNS_CUSTOM_PATH
	 * @uses    (CONSTANT)   BNS_CUSTOM_URL
	 * @uses    (CONSTANT)   BNSTD_PATH
	 * @uses    (CONSTANT)   BNSTD_URL
	 * @uses    BNS_Theme_Details_Widget::plugin_data
	 * @uses    wp_enqueue_script
	 * @uses    wp_enqueue_style
	 */
	function scripts_and_styles() {
		/** @var object $bnstd_data - holds the plugin header data */
		$bnstd_data = $this->plugin_data();

		/** Enqueue Scripts */
		/** Enqueue toggling script which calls jQuery as a dependency */
		/** Placeholder code ... no JavaScript is implemented at this time */
		/**  wp_enqueue_script( 'bnstd_script', BNSTD_URL . 'js/bnstd-script.js', array( 'jquery' ), $bnstd_data['Version'], true ); */

		/** Enqueue Style Sheets */
		wp_enqueue_style( 'BNSTD-Style', BNSTD_URL . 'css/bnstd-style.css', array(), $bnstd_data['Version'], 'screen' );

		/** This location is recommended as upgrade safe */
		if ( is_readable( BNS_CUSTOM_PATH . 'bnstd-custom-types.css' ) ) {
			wp_enqueue_style( 'BNSTD-Custom-Styles', BNS_CUSTOM_URL . 'bnstd-custom-styles.css', array(), $bnstd_data['Version'], 'screen' );
		}
		/** End if - is readable */

	}
	/** End function - scripts and styles */


	/**
	 * Plugin Data
	 *
	 * Returns the plugin header data as an array
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.4
	 *
	 * @uses       get_plugin_data
	 *
	 * @return array
	 */
	function plugin_data() {

		/** Call the wp-admin plugin code */
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		/** @var $plugin_data - holds the plugin header data */
		$plugin_data = get_plugin_data( __FILE__ );

		return $plugin_data;

	}
	/** End function - plugin data */


	/**
	 * Override widget method of class WP_Widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param    $args
	 * @param    $instance
	 *
	 * @uses       BNS_Theme_Details::replace_spaces
	 * @uses       BNS_Theme_Details::theme_api_details
	 * @uses       BNS_Theme_Details::widget_title
	 * @uses       apply_filters
	 *
	 * @return    void
	 *
	 * @version    0.4
	 * @date       December 28, 2014
	 * Change sanity check to ensure `$theme_slug` is not empty versus not `null`
	 */
	function widget( $args, $instance ) {
		extract( $args );
		/** User-selected settings */
		$title      = apply_filters( 'widget_title', $instance['title'] );
		$theme_slug = $this->replace_spaces( $instance['theme_slug'] );
		/** The Main Options */
		$main_options['use_screenshot_link']    = $instance['use_screenshot_link'];
		$main_options['show_name']              = $instance['show_name'];
		$main_options['show_author']            = $instance['show_author'];
		$main_options['show_last_updated']      = $instance['show_last_updated'];
		$main_options['show_current_version']   = $instance['show_current_version'];
		$main_options['show_rating']            = $instance['show_rating'];
		$main_options['show_number_of_ratings'] = $instance['show_number_of_ratings'];
		$main_options['show_description']       = $instance['show_description'];
		$main_options['show_downloaded_count']  = $instance['show_downloaded_count'];
		$main_options['use_download_link']      = $instance['use_download_link'];
		$main_options['show_changelog']         = $instance['show_changelog'];

		/** Sanity check - make sure theme slug is not empty / blank */
		if ( ! empty( $theme_slug ) ) {

			/** @var $before_widget string - define by theme */
			echo $before_widget;

			/* Title of widget (before and after defined by themes). */
			if ( $title <> null ) {
				/**
				 * @var $before_title   string - defined by theme
				 * @var $after_title    string - defined by theme
				 */
				echo $before_title . $title . $after_title;
			}
			/** End if - title is null or empty */

			/** Get the theme details */
			$this->theme_api_details( $theme_slug, $main_options );

			/** @var $after_widget   string - defined by theme */
			echo $after_widget;

		} else {

			echo null;

		}
		/** End if - is there a theme slug */

	} /** End function - widget */


	/**
	 * Override update method of class WP_Widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param   $new_instance
	 * @param   $old_instance
	 *
	 * @uses       BNS_Theme_Details::replace_spaces
	 *
	 * @return  array - widget options and settings
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		/** Strip tags (if needed) and update the widget settings */
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['theme_slug'] = $this->replace_spaces( $new_instance['theme_slug'] );
		/** The Main Options */
		$instance['use_screenshot_link']    = $new_instance['use_screenshot_link'];
		$instance['show_name']              = $new_instance['show_name'];
		$instance['show_author']            = $new_instance['show_author'];
		$instance['show_last_updated']      = $new_instance['show_last_updated'];
		$instance['show_current_version']   = $new_instance['show_current_version'];
		$instance['show_rating']            = $new_instance['show_rating'];
		$instance['show_number_of_ratings'] = $new_instance['show_number_of_ratings'];
		$instance['show_description']       = $new_instance['show_description'];
		$instance['show_downloaded_count']  = $new_instance['show_downloaded_count'];
		$instance['use_download_link']      = $new_instance['use_download_link'];
		$instance['show_changelog']         = $new_instance['show_changelog'];

		return $instance;

	} /** End function - update */


	/**
	 * Overrides form method of class WP_Widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param   $instance
	 *
	 * @uses       BNS_Theme_Details::replace_spaces
	 * @uses       BNS_Theme_Details::widget_title
	 * @uses       _e
	 * @uses       get_field_id
	 * @uses       get_field_name
	 * @uses       wp_get_theme
	 * @uses       wp_get_theme->get_template
	 * @uses       wp_parse_args
	 *
	 * @return  void
	 */
	function form( $instance ) {

		/** Set up some default widget settings */
		$defaults = array(
			'title'                  => __( 'Theme Details', 'bns-theme-details' ),
			'theme_slug'             => $this->replace_spaces( wp_get_theme()->get_template() ),
			/** The Main Options */
			'use_screenshot_link'    => true,
			'show_name'              => true,
			'show_author'            => true,
			'show_last_updated'      => true,
			'show_current_version'   => true,
			'show_rating'            => true,
			'show_number_of_ratings' => true,
			'show_description'       => false,
			'show_downloaded_count'  => true,
			'use_download_link'      => true,
			'show_changelog'         => false

		);
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-theme-details' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"
			       style="width:100%;" />
		</p>

		<p>
			<label
				for="<?php echo $this->get_field_id( 'theme_slug' ); ?>"><?php _e( 'Theme Slug', 'bns-theme-details' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'theme_slug' ); ?>"
			       name="<?php echo $this->get_field_name( 'theme_slug' ); ?>"
			       value="<?php echo $instance['theme_slug']; ?>" style="width:100%;" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['use_screenshot_link'], true ); ?>
			       id="<?php echo $this->get_field_id( 'use_screenshot_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'use_screenshot_link' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'use_screenshot_link' ); ?>"><?php _e( 'Use screenshot link?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_name'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_name' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_name' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_name' ); ?>"><?php _e( 'Show name?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_author'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_author' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_author' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_author' ); ?>"><?php _e( 'Show author?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_last_updated'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_last_updated' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_last_updated' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_last_updated' ); ?>"><?php _e( 'Show last updated?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_current_version'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_current_version' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_current_version' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_current_version' ); ?>"><?php _e( 'Show current version?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_rating'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_rating' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_rating' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_rating' ); ?>"><?php _e( 'Show rating?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_number_of_ratings'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_number_of_ratings' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_number_of_ratings' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_number_of_ratings' ); ?>"><?php _e( 'Show number of ratings?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_description'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_description' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_description' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_description' ); ?>"><?php _e( 'Show description?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_downloaded_count'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_downloaded_count' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_downloaded_count' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_downloaded_count' ); ?>"><?php _e( 'Show downloaded count?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['use_download_link'], true ); ?>
			       id="<?php echo $this->get_field_id( 'use_download_link' ); ?>"
			       name="<?php echo $this->get_field_name( 'use_download_link' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'use_download_link' ); ?>"><?php _e( 'Use download link?', 'bns-theme-details' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_changelog'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_changelog' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_changelog' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_changelog' ); ?>"><?php _e( 'Show the changelog?', 'bns-theme-details' ); ?></label>
		</p>

	<?php
	} /** End function - form */


	/**
	 * Register widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @uses       register_widget
	 *
	 * @return  void
	 */
	function load_bnstd_widget() {

		register_widget( 'BNS_Theme_Details_Widget' );

	} /** End function - load bnstd widget */


	/**
	 * BNS Theme Details Shortcode
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param   $atts
	 *
	 * @uses       __return_null
	 * @uses       shortcode_atts
	 * @uses       the_widget
	 * @uses       wp_get_theme
	 * @uses       wp_get_theme->get_template
	 *
	 * @return  string
	 *
	 * @version    0.4
	 * @date       December 27, 2014
	 * Corrected filter reference to indicate `bns_theme_details`
	 */
	function bns_theme_details_shortcode( $atts ) {

		/** Let's start by capturing the output */
		ob_start();

		/** Pull the widget together for use elsewhere */
		the_widget(
			'BNS_Theme_Details_Widget',
			$instance = shortcode_atts(
				array(
					'title'                  => __return_null(),
					'theme_slug'             => wp_get_theme()->get_template(),
					/** The Main Options */
					'use_screenshot_link'    => true,
					'show_name'              => true,
					'show_author'            => true,
					'show_last_updated'      => true,
					'show_current_version'   => true,
					'show_rating'            => true,
					'show_number_of_ratings' => true,
					'show_description'       => true,
					'show_downloaded_count'  => true,
					'use_download_link'      => true,
					'show_changelog'         => true

				), $atts, 'bns_theme_details'
			),
			$args = array(
				/** clear variables defined by theme for widgets */
				$before_widget = '',
				$after_widget = '',
				$before_title = '',
				$after_title = '',
			)
		);

		/** Get the_widget output and put it into its own variable */
		$bns_theme_details_content = ob_get_clean();

		/** Return the widget output for the shortcode to use */

		return $bns_theme_details_content;

	} /** End function - shortcode */


	/**
	 * Theme Details
	 *
	 * The main collection of the details related to the theme as called from
	 * the WordPress Theme API and derived elsewhere
	 *
	 * @package     BNS_Theme_Details
	 * @sub-package Output
	 * @since       0.1
	 *
	 * @param $theme_slug   - primary data point
	 * @param $main_options - output options
	 *
	 * @uses        BNS_Theme_Details_Widget::changelog
	 * @uses        BNS_Theme_Details_Widget::display_download_count
	 * @uses        BNS_Theme_Details_Widget::display_download_link
	 * @uses        BNS_Theme_Details_Widget::display_name_and_author
	 * @uses        BNS_Theme_Details_Widget::display_rating_and_voters
	 * @uses        BNS_Theme_Details_Widget::display_screenshot
	 * @uses        BNS_Theme_Details_Widget::display_updated_and_version
	 * @uses        _e
	 * @uses        themes_api
	 *
	 * @version     0.4
	 * @date        December 28, 2014
	 * Added `changelog` to output
	 * Switched order of "Theme Name and Author" with the "Screenshot"
	 */
	function theme_api_details( $theme_slug, $main_options ) {
		/** Pull in the Theme API file */
		include_once ABSPATH . 'wp-admin/includes/theme.php';

		/** @var object $api - contains theme details */
		$api = themes_api(
			'theme_information', array(
				'slug' => $theme_slug
			)
		);

		/** @var string $screenshot_url - link to screenshot */
		$screenshot_url = $api->screenshot_url;

		/** @var string $name - the theme name */
		$name = $api->name;

		/** @var string $author - theme author user name */
		$author = $api->author;

		/** @var string $last_updated - date as a numeric value */
		$last_updated = $api->last_updated;

		/** @var string $current_version - current version of theme */
		$current_version = $api->version;

		/** @var integer $rating - rating converted to 5 star system */
		$rating = $api->rating / 20;

		/** @var integer $number_of_ratings */
		$number_of_ratings = $api->num_ratings;

		/** @var string $description - theme description */
		$description = $api->sections['description'];

		/** @var integer $count - contains total downloads value */
		$count = $api->downloaded;

		/** @var string $download_link - link to direct download from WordPress */
		$download_link = $api->download_link;

		/** Sanity check - make sure there is a value for the name */
		if ( isset( $name ) ) {

			echo $this->display_name_and_author( $main_options, $name, $author );

			echo $this->display_screenshot( $main_options, $screenshot_url );

			echo $this->display_updated_and_version( $main_options, $last_updated, $current_version );

			echo $this->display_rating_and_voters( $main_options, $rating, $number_of_ratings );

			echo $this->display_description( $main_options, $description );

			echo $this->display_download_count( $main_options, $count );

			echo $this->display_download_link( $main_options, $download_link );

			echo $this->changelog( $main_options, $current_version, $name );

		} else {

			_e( 'Are you using a theme from the WordPress Theme repository?', 'bns-theme-details' );

		}
		/** End if - is count set */

	} /** End function - theme api details */


	/**
	 * Widget Title
	 *
	 * Returns the widget title based on the theme slug used for the output
	 *
	 * @package        BNS_Theme_Details
	 * @since          0.1
	 *
	 * @internal       Not currently used ... to be removed?
	 *
	 * @param $theme_slug
	 *
	 * @uses           __
	 * @uses           apply_filters
	 * @uses           wp_get_theme
	 * @uses           wp_get_theme->get
	 * @uses           wp_get_theme->get_template
	 *
	 * @return string
	 */
	function widget_title( $theme_slug ) {

		$theme_name = ( $theme_slug == wp_get_theme()->get_template() )
			? wp_get_theme()->get_template()
			: wp_get_theme( $theme_slug )->get( 'Name' );

		$title = '<span class="bnstd-widget-title">' . sprintf( __( '%1$s Details', 'bns-theme-details' ), $theme_name ) . '</span>';

		return apply_filters( 'bnstd_widget_title', $title );

	} /** End function - widget title */


	/**
	 * Display Name and Author
	 *
	 * Returns the theme name and the theme author if they are set; or returns
	 * null if they are not set
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $name
	 * @param $author
	 *
	 * @uses           __
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @return null|string
	 *
	 * @version        0.4
	 * @date           December 28, 2014
	 * Added more specificity to the output for more finely tuned styles
	 */
	function display_name_and_author( $main_options, $name, $author ) {

		/**
		 * Make sure there is a theme name set (redundant but also consistent)
		 * and it is to be shown
		 */
		if ( isset( $name ) && ( true == $main_options['show_name'] ) ) {

			$output = '<div class="bnstd-theme-name">' . sprintf( __( '%1$s %2$s', 'bns-theme-details' ), sprintf( '<span class="bnstd-theme-label">%1$s</span>', __( 'Theme:', 'bns-theme-details' ) ), $name ) . '</div>';

			/** Make sure there is an author name set and it is to be shown */
			if ( isset( $author ) && ( true == $main_options['show_author'] ) ) {

				$output = '<div class="bnstd-theme-name-and-author">'
				          . sprintf( __( '%1$s %2$s %3$s', 'bns-theme-details' ), sprintf( '<span class="bnstd-theme-label">%1$s</span>', __( 'Theme:', 'bns-theme-details' ) ), '<span class="bnstd-theme-name">' . $name . '</span>', sprintf( '<span class="bnstd-theme-by-author">by %1$s</span>', '<span class="bnstd-theme-author">' . $author . '</span>' ) )
				          . '</div>';

				return apply_filters( 'bnstd_display_name_and_author', $output );

			}

			/** End if - author name is set */

			return apply_filters( 'bnstd_display_name_only', $output );

		} elseif ( ! ( true == $main_options['show_name'] ) && ( true == $main_options['show_author'] ) ) {

			$output = '<div class="bnstd-theme-by-author">' . sprintf( __( 'By %1$s', 'bns-theme-details' ), '<span class="bnstd-theme-author">' . $author . '</span>' ) . '</div>';

			return apply_filters( 'bnstd_display_author_only', $output );

		} else {

			return apply_filters( 'bnstd_display_name_and_author', __return_null() );

		}
		/** End if - theme name is set */

	} /** End function - display name and author */


	/**
	 * Display Screenshot
	 *
	 * Returns the screenshot URL in its own DIV ... or returns null.
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @param $main_options
	 * @param $screenshot_url
	 *
	 * @return null|string
	 *
	 * @version        0.4
	 * @date           December 27, 2014
	 * Wrapped `<img />` tag in a `<p />` tag for better display compatibility
	 */
	function display_screenshot( $main_options, $screenshot_url ) {

		/** Check if the screenshot link is set and is to be used */
		if ( isset( $screenshot_url ) && ( true == $main_options['use_screenshot_link'] ) ) {

			$output = '<div class="bnstd-screenshot aligncenter">';
			$output .= '<p><img src="' . $screenshot_url . '" /></p>';
			$output .= '</div>';

			return apply_filters( 'bnstd_display_screenshot', $output );

		} else {

			return apply_filters( 'bnstd_display_screenshot', __return_null() );

		}
		/** End if - use screenshot link */

	} /** End function - display screenshot */


	/**
	 * Display Updated and Version
	 *
	 * Returns the last updated date and the current theme version if the are
	 * set or  null if they are not set
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $last_updated
	 * @param $current_version
	 *
	 * @uses           __
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @return null|string
	 *
	 * @version        0.4
	 * @date           December 28, 2014
	 * Added more specificity to the output for more finely tuned styles
	 */
	function display_updated_and_version( $main_options, $last_updated, $current_version ) {

		/** Make sure the last updated is set and it is to be shown */
		if ( isset( $last_updated ) && ( true == $main_options['show_last_updated'] ) ) {

			$output = '<div class="bnstd-updated">' . sprintf( __( 'Last updated: %1$s', 'bns-theme-details' ), $last_updated ) . '</div>';

			/** Make sure the current version is set and it is to be shown */
			if ( isset( $current_version ) && ( true == $main_options['show_current_version'] ) ) {

				$output = '<div class="bnstd-updated-and-version">'
				          . sprintf(
						__( '%1$s %2$s %3$s', 'bns-td' ),
						'<span class="bnstd-updated-label">Last updated:</span>',
						'<span class="bnstd-updated">' . $last_updated . '</span>',
						'<span class="bnstd-version">' . sprintf( __( '(%1$s %2$s)', 'bns-theme-details' ), '<span class="bnstd-version-label">version</span>', $current_version ) . '</span>'
					) . '</div>';

				return apply_filters( 'bnstd_display_updated_and_version', $output );

			}

			/** End if - current version is set */

			return apply_filters( 'bnstd_display_updated_only', $output );

		} elseif ( ! ( true == $main_options['show_last_updated'] ) && ( true == $main_options['show_current_version'] ) ) {

			$output = '<div class="bnstd-version">' . sprintf( __( '%1$s %2$s', 'bns-theme-details' ), '<span class="bnstd-version-label">Current version:</span>', $current_version ) . '</div>';

			return apply_filters( 'bnstd_display_version_only', $output );

		} else {

			return apply_filters( 'bnstd_display_updated_and_version', __return_null() );

		}
		/** End if - last updated is set */

	} /** End function - display updated and version */


	/**
	 * Display Ratings and Voters
	 *
	 * Return the star rating of the theme and the number of voters if set, or
	 * retrun null if they are not
	 *
	 * @package        BNT_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $rating
	 * @param $number_of_ratings
	 *
	 * @uses           __
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @return null|string
	 *
	 * @version        0.4
	 * @date           December 28, 2014
	 * Added more specificity to the output for more finely tuned styles
	 */
	function display_rating_and_voters( $main_options, $rating, $number_of_ratings ) {

		/** Check if rating is set an if it should be shown */
		if ( isset( $rating ) && ( true == $main_options['show_rating'] ) ) {

			$output = '<div class="bnstd-rating">' . sprintf(
					__( '%1$s %2$s %3$s', 'bns-theme-details' ),
					'<span class="bnstd-rating-label">Average Rating:</span>',
					'<span class="bnstd-rating">' . $rating . '</span>',
					'<span class="bnstd-rating-stars">stars</span>'
				) . '</div>';

			/** Check if number of ratings is set and if it should be shown */
			if ( isset( $number_of_ratings ) && ( true == $main_options['show_number_of_ratings'] ) ) {

				$output = '<div class="bnstd-rating-and-voters">' . sprintf(
						__( '%1$s %2$s %3$s %4$s', 'bns-theme-details' ),
						'<span class="bnstd-rating-label">Average Rating:</span>',
						'<span class="bnstd-rating">' . $rating . '</span>',
						'<span class="bnstd-rating-stars">stars</span>',
						'<span class="bnstd-voters">' . sprintf( __( '(by %1$s voters)', 'bns-theme-details' ), $number_of_ratings ) . '</span>'
					) . '</div>';

				return apply_filters( 'bnstd_display_rating_and_voters', $output );

			}

			/** End if - number of ratings is set */

			return apply_filters( 'bnstd_display_rating_only', $output );

		} else {

			return apply_filters( 'bnstd_display_rating_and_voters', __return_null() );

		}
		/** End if - rating is set */

	} /** End function - display rating and voters */


	/**
	 * Display Download Count
	 *
	 * Returns the download count
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $count
	 *
	 * @uses           __
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @return string
	 *
	 * @version        0.4
	 * @date           December 28, 2014
	 * Added more specificity to the output for more finely tuned styles
	 */
	function display_download_count( $main_options, $count ) {

		/** Check if the count is set and is to be shown */
		if ( isset( $count ) && ( true == $main_options['show_downloaded_count'] ) ) {

			$output = '<div class="bnstd-download-count">' . sprintf( __( '%1$s %2$s', 'bns-theme-details' ), '<span class="bnstd-download-count-label">Total downloads:</span>', $count ) . '</div>';

			return apply_filters( 'bnstd_display_download_count', $output );

		} else {

			return apply_filters( 'bnstd_display_download_count', __return_null() );

		}
		/** End if - show count */

	} /** End function - display download count */


	/**
	 * Display Description
	 *
	 * Returns the theme description
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $description
	 *
	 * @uses           __
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @return string
	 */
	function display_description( $main_options, $description ) {

		/** Check if the count is set and is to be shown */
		if ( isset( $description ) && ( true == $main_options['show_description'] ) ) {

			$output = '<div class="bnstd-description">' . $description . '</div>';

			return apply_filters( 'bnstd_display_description', $output );

		} else {

			return apply_filters( 'bnstd_display_description', __return_null() );

		}
		/** End if - show description */

	} /** End function - display description */


	/**
	 * Display Download Link
	 *
	 * Return the download link if it is set or return null if it is not
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $download_link
	 *
	 * @uses           __
	 * @uses           __return_null
	 * @uses           apply_filters
	 *
	 * @return null|string
	 */
	function display_download_link( $main_options, $download_link ) {

		/** Check if download link is set and if it should be shown */
		if ( isset( $download_link ) && ( true == $main_options['use_download_link'] ) ) {

			$output = '<div class="bnstd-download-link">'
			          . sprintf( __( '%1$s %2$s', 'bns-theme-details' ), '<span class="bnstd-download-link-label">Download your copy:</span>', '<a class="bnstd-download-link-url" href="' . $download_link . '">' . __( 'click here', 'bns-theme-details' ) . '</a>' ) . '</div>';

			return apply_filters( 'bnstd_display_download_link', $output );

		} else {

			return apply_filters( 'bnstd_display_download_link', __return_null() );

		}
		/** End if - download link is set */

	} /** End function - display download link */


	/**
	 * Changelog
	 *
	 * @package     BNS_Theme_Details
	 * @sub-package Output
	 * @since       0.4
	 *
	 * @uses        BNS_Theme_Details_Widget::replace_spaces
	 * @uses        __
	 * @uses        apply_filters
	 * @uses        wp_get_theme
	 * @uses        wp_kses_post
	 * @uses        wp_remote_get
	 *
	 * @internal    Uses BNS Inline Asides to minimize the display by default
	 * @link        https://wordpress.org/plugins/bns-inline-asides
	 *
	 * @param $main_options
	 * @param $current_version
	 * @param $name
	 *
	 * @return mixed|null|void
	 */
	function changelog( $main_options, $current_version, $name ) {

		$changelog_lines = null;

		if ( true == $main_options['show_changelog'] ) {

			/** Spice things up by leveraging BNS Inline Asides */
			if ( ! is_plugin_active( 'bns-inline-asides/bns-inline-asides.php' ) ) {

				echo sprintf( '<div class="bnstd-changelog-label">%1$s</div>', __( 'Changelog:', 'bns-theme-details' ) );

			}
			/** End if - is plugin active */

			$theme_slug     = $this->replace_spaces( wp_get_theme( $name )->get_template() );
			$changelog_path = wp_remote_get( 'https://themes.svn.wordpress.org/' . $theme_slug . '/' . $current_version . '/changelog.txt' );

			$output  = ! is_wp_error( $changelog_path ) && ! empty( $changelog_path['body'] ) ? $changelog_path['body'] : null;
			$changes = (array) preg_split( '~[\r\n]+~', $output );

			/** @var string $changelog_lines - begin output string */
			$changelog_lines = '<div class="bnstd_changelog">';

			$ul = false;

			foreach ( $changes as $index => $line ) {

				if ( preg_match( '~^=\s*(.*)\s*=$~i', $line ) ) {

					if ( $ul ) {
						$changelog_lines .= '</ul><div style="clear: left;"></div>';
					}
					/** End if - unordered list created */

					// $changelog_lines .= '<hr/>';

				}
				/** End if - non-blank line */

				/** @var string $return_value - body of message */
				$return_value = '';

				if ( preg_match( '~^\s*\*\s*~', $line ) ) {

					if ( ! $ul ) {
						$return_value = '<ul">';
						$ul           = true;
					}
					/** End if - unordered list not started */

					$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
					$return_value .= '<li style=" ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . $line . '</li>';

				} else {

					if ( $ul ) {
						$return_value = '</ul><div style="clear: left;"></div>';
						$return_value .= '<p>' . $line . '</p>';
						$ul = false;
					} else {
						$return_value .= '<p>' . $line . '</p>';
					}
					/** End if - unordered list started */

				}
				/** End if - non-blank line */

				$changelog_lines .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $return_value ) );

			}
			/** End foreach - line parsing */

			$changelog_lines .= '</div>';

			/** Spice things up by leveraging BNS Inline Asides */
			if ( is_plugin_active( 'bns-inline-asides/bns-inline-asides.php' ) ) {

				$changelog_lines = do_shortcode( '[aside type=changelog status=closed]' . $changelog_lines . '[/aside]' );

			}
			/** End if - is plugin active */

		}

		return apply_filters( 'bnstd_changelog_output', $changelog_lines );

	} /** End function - changelog */


	/**
	 * Replace Spaces
	 *
	 * Takes a string, changes it to lower case and replaces the spaces with a
	 * single hyphen by default
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @internal       Compliments of the Opus Primus framework theme by Cais.
	 * @link           http://opusprimus.com
	 *
	 * @param   string $text
	 * @param   string $replacement
	 *
	 * @return  string
	 */
	function replace_spaces( $text, $replacement = '-' ) {
		/** @var $new_text - initial text set to lower case */
		$new_text = esc_attr( strtolower( $text ) );
		/** replace whitespace with a single space */
		$new_text = preg_replace( '/\s\s+/', ' ', $new_text );
		/** replace space with a hyphen to create nice CSS classes */
		$new_text = preg_replace( '/\\040/', $replacement, $new_text );

		/** Return the string with spaces replaced by the replacement variable */

		return $new_text;

	}
	/** End function - replace spaces */


}

/** End class - theme details */

/** @var object $bns_td - create a new instance of the class */
$bns_td = new BNS_Theme_Details_Widget();


/**
 * BNS Theme Details In Plugin Update Message
 *
 * @package BNS_Theme_Details
 * @since   0.4
 *
 * @uses    get_transient
 * @uses    is_wp_error
 * @uses    set_transient
 * @uses    wp_kses_post
 * @uses    wp_remote_get
 *
 * @param $args
 */
function BNS_Theme_Details_in_plugin_update_message( $args ) {

	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	$bnstd_data = get_plugin_data( __FILE__ );

	$transient_name = 'bnstd_upgrade_notice_' . $args['Version'];
	if ( false == ( $upgrade_notice = get_transient( $transient_name ) ) ) {

		/** @var string $response - get the readme.txt file from WordPress */
		$response = wp_remote_get( 'https://plugins.svn.wordpress.org/bns-theme-details/trunk/readme.txt' );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$matches = null;
		}
		$regexp         = '~==\s*Changelog\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $bnstd_data['Version'] ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $response['body'], $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( $bnstd_data['Version'], $version, '<' ) ) {

				/** @var string $upgrade_notice - start building message (inline styles) */
				$upgrade_notice = '<style type="text/css">
							.bnstd_plugin_upgrade_notice { padding-top: 20px; }
							.bnstd_plugin_upgrade_notice ul { width: 50%; list-style: disc; margin-left: 20px; margin-top: 0; }
							.bnstd_plugin_upgrade_notice li { margin: 0; }
						</style>';

				/** @var string $upgrade_notice - start building message (begin block) */
				$upgrade_notice .= '<div class="bnstd_plugin_upgrade_notice">';

				$ul = false;

				foreach ( $notices as $index => $line ) {

					if ( preg_match( '~^=\s*(.*)\s*=$~i', $line ) ) {

						if ( $ul ) {
							$upgrade_notice .= '</ul><div style="clear: left;"></div>';
						}
						/** End if - unordered list created */

						$upgrade_notice .= '<hr/>';

					}
					/** End if - non-blank line */

					/** @var string $return_value - body of message */
					$return_value = '';

					if ( preg_match( '~^\s*\*\s*~', $line ) ) {

						if ( ! $ul ) {
							$return_value = '<ul">';
							$ul           = true;
						}
						/** End if - unordered list not started */

						$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
						$return_value .= '<li style=" ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . $line . '</li>';

					} else {

						if ( $ul ) {
							$return_value = '</ul><div style="clear: left;"></div>';
							$return_value .= '<p>' . $line . '</p>';
							$ul = false;
						} else {
							$return_value .= '<p>' . $line . '</p>';
						}
						/** End if - unordered list started */

					}
					/** End if - non-blank line */

					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $return_value ) );

				}
				/** End foreach - line parsing */

				$upgrade_notice .= '</div>';

			}
			/** End if - version compare */

		}
		/** End if - response message exists */

		/** Set transient - minimize calls to WordPress */
		set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );

	}
	/** End if - transient check */

	echo $upgrade_notice;

}

/** End function - in plugin update message */


/** Add Plugin Update Message */
add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ), 'BNS_Theme_Details_in_plugin_update_message' );
