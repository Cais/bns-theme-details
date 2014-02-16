<?php
/*
Plugin Name: BNS Theme Details
Plugin URI: http://buynowshop.com/plugins/bns-theme-details
Description: Displays theme specific details such as download count, last update, author, etc.
Version: 0.1-alpha
Text Domain: bns-td
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Theme Details
 * This plugin can be used to display the recent download count of a theme, as
 * well as various other details such as the author and when it was last
 * updated. It also includes a link to the WordPress Theme repository if it
 * exists.
 *
 * @package        BNS_Theme_Details
 * @link           http://buynowshop.com/plugins/bns-theme-details
 * @link           https://github.com/Cais/bns-theme-details
 * @link           http://wordpress.org/extend/plugins/bns-theme-details/
 * @version        0.1
 * @author         Edward Caissie <edward.caissie@gmail.com>
 * @copyright      Copyright (c) 2014, Edward Caissie
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
 */

/** Thanks to Samuel (Otto42) Wood for the code snippet inspiration. */
class BNS_Theme_Details_Widget extends WP_Widget {

	function __construct() {

		/** Widget settings */
		$widget_ops = array(
			'classname'   => 'bns-theme-details',
			'description' => __( 'Displays theme specific details such as download count, last update, author, etc.', 'bns-td' )
		);
		/** Widget control settings */
		$control_ops = array(
			'width'   => 200,
			'id_base' => 'bns-theme-details'
		);
		/** Create the widget */
		$this->WP_Widget( 'bns-theme-details', 'BNS Theme Details', $widget_ops, $control_ops );

		/**
		 * Check installed WordPress version for compatibility
		 *
		 * @package              BNS_Theme_Details
		 * @since                0.1
		 *
		 * @internal             Version 2.8 being used in reference to __return_null()
		 *
		 * @uses        (GLOBAL) wp_version
		 */
		global $wp_version;
		$exit_message = __( 'BNS Theme Details requires WordPress version 3.4 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>', 'bns-td' );
		if ( version_compare( $wp_version, "3.4", "<" ) ) {
			exit( $exit_message );
		}
		/** End if = version compare */

		/** Add widget */
		add_action( 'widgets_init', array( $this, 'load_bnstd_widget' ) );

		/** Add Shortcode */
		add_shortcode(
			'bns_theme_details', array(
				$this,
				'bns_theme_details_shortcode'
			)
		);

	}

	/**
	 * Override widget method of class WP_Widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param    $args
	 * @param    $instance
	 *
	 * @uses       BNS_Theme_Counter::theme_api_details
	 * @uses       BNS_Theme_Counter::widget_title
	 * @uses       apply_filters
	 *
	 * @return    void
	 */
	function widget( $args, $instance ) {
		extract( $args );
		/** User-selected settings */
		$title      = apply_filters( 'widget_title', $instance['title'] );
		$theme_slug = $instance['theme_slug'];
		/** The Main Options */
		$show_name              = $instance['show_name'];
		$show_author            = $instance['show_author'];
		$show_rating            = $instance['show_rating'];
		$show_number_of_ratings = $instance['show_number_of_ratings'];
		$show_last_updated      = $instance['show_last_updated'];
		$show_current_version   = $instance['show_current_version'];
		$show_downloaded_count  = $instance['show_downloaded_count'];
		$use_screenshot_link    = $instance['use_screenshot_link'];
		$use_download_link      = $instance['use_download_link'];

		$main_options = array(
			'show_name'              => $instance['show_name'],
			'show_author'            => $instance['show_author'],
			'show_rating'            => $instance['show_rating'],
			'show_number_of_ratings' => $instance['show_number_of_ratings'],
			'show_last_updated'      => $instance['show_last_updated'],
			'show_current_version'   => $instance['show_current_version'],
			'show_downloaded_count'  => $instance['show_downloaded_count'],
			'use_screenshot_link'    => $instance['use_screenshot_link'],
			'use_download_link'      => $instance['use_download_link']
		);

		/** Sanity check - make sure theme slug is not null */
		if ( null !== $theme_slug ) {

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

			/** Get the number of downloads */
			$this->theme_api_details( $theme_slug, $main_options );

			/** @var $after_widget   string - defined by theme */
			echo $after_widget;

		} else {

			echo null;

		}
		/** End if - is there a theme slug */

	}

	/** End function - widget */


	/**
	 * Override update method of class WP_Widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param   $new_instance
	 * @param   $old_instance
	 *
	 * @return  array - widget options and settings
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		/** Strip tags (if needed) and update the widget settings */
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['theme_slug'] = $new_instance['theme_slug'];
		/** The Main Options */
		$instance['show_name']              = $new_instance['show_name'];
		$instance['show_author']            = $new_instance['show_author'];
		$instance['show_rating']            = $new_instance['show_rating'];
		$instance['show_number_of_ratings'] = $new_instance['show_number_of_ratings'];
		$instance['show_last_updated']      = $new_instance['show_last_updated'];
		$instance['show_current_version']   = $new_instance['show_current_version'];
		$instance['show_downloaded_count']  = $new_instance['show_downloaded_count'];
		$instance['use_screenshot_link']    = $new_instance['use_screenshot_link'];
		$instance['use_download_link']      = $new_instance['use_download_link'];

		return $instance;

	}

	/** End function - update */


	/**
	 * Overrides form method of class WP_Widget
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param   $instance
	 *
	 * @uses       _e
	 * @uses       get_field_id
	 * @uses       get_field_name
	 * @uses       wp_get_theme
	 * @uses       wp_get_theme->get_template
	 *
	 * @return  void
	 */
	function form( $instance ) {

		/** Set up some default widget settings */
		$defaults = array(
			'title'                  => $this->widget_title( $instance['theme_slug'] ),
			'theme_slug'             => wp_get_theme()->get_template(),
			/** The Main Options */
			'show_name'              => true,
			'show_author'            => true,
			'show_rating'            => true,
			'show_number_of_ratings' => true,
			'show_last_updated'      => true,
			'show_current_version'   => true,
			'show_downloaded_count'  => true,
			'use_screenshot_link'    => true,
			'use_download_link'      => true

		);
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-td' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"
				   style="width:100%;" />
		</p>

		<p>
			<label
				for="<?php echo $this->get_field_id( 'theme_slug' ); ?>"><?php _e( 'Theme Slug', 'bns-td' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'theme_slug' ); ?>"
				   name="<?php echo $this->get_field_name( 'theme_slug' ); ?>"
				   value="<?php echo $instance['theme_slug']; ?>" style="width:100%;" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_name'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_name' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_name' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_name' ); ?>"><?php _e( 'Show name?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_author'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_author' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_author' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_author' ); ?>"><?php _e( 'Show author?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_rating'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_rating' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_rating' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_rating' ); ?>"><?php _e( 'Show rating?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_number_of_ratings'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_number_of_ratings' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_number_of_ratings' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_number_of_ratings' ); ?>"><?php _e( 'Show number of ratings?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_last_updated'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_last_updated' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_last_updated' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_last_updated' ); ?>"><?php _e( 'Show last updated?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_current_version'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_current_version' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_current_version' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_current_version' ); ?>"><?php _e( 'Show current version?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_downloaded_count'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_downloaded_count' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_downloaded_count' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_downloaded_count' ); ?>"><?php _e( 'Show downloaded count?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['use_screenshot_link'], true ); ?>
				   id="<?php echo $this->get_field_id( 'use_screenshot_link' ); ?>"
				   name="<?php echo $this->get_field_name( 'use_screenshot_link' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'use_screenshot_link' ); ?>"><?php _e( 'Use screenshot link?', 'bns-td' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['use_download_link'], true ); ?>
				   id="<?php echo $this->get_field_id( 'use_download_link' ); ?>"
				   name="<?php echo $this->get_field_name( 'use_download_link' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'use_download_link' ); ?>"><?php _e( 'Use download link?', 'bns-td' ); ?></label>
		</p>

	<?php
	}

	/** End function - form */


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
	}
	/** End function - load bnstd widget */


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
					'show_name'              => true,
					'show_author'            => true,
					'show_rating'            => true,
					'show_number_of_ratings' => true,
					'show_last_updated'      => true,
					'show_current_version'   => true,
					'show_downloaded_count'  => true,
					'use_screenshot_link'    => true,
					'use_download_link'      => true

				), $atts, 'bns_theme_counter'
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

	}


	/**
	 * Theme Details
	 * The main collection of the details related to the theme as called from
	 * the WordPress Theme API
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param $theme_slug   - primary data point
	 * @param $main_options - output options
	 *
	 * @uses       BNS_Theme_Details_Widget::display_screenshot
	 * @uses       themes_api
	 * @uses       wp_get_theme
	 * @uses       wp_get_theme->get_template
	 */
	function theme_api_details( $theme_slug, $main_options ) {
		/** Pull in the Theme API file */
		include_once ABSPATH . 'wp-admin/includes/theme.php';

		/** @var object $api - contains theme details */
		$api = themes_api(
			'theme_information', array(
				'slug'   => $theme_slug,
				'fields' => array(
					'name'           => true,
					'author'         => true,
					'rating'         => true,
					'num_ratings'    => true,
					'screenshot_url' => true,
					'downloaded'     => true,
					'download_link'  => true,
					'last_updated'   => true
				)
			)
		);

		/** @var string $name - the theme name */
		$name = $api->name;

		/** @var string $author - theme author user name */
		$author = $api->author;

		/** @var integer $rating - rating converted to 5 star system */
		$rating = $api->rating / 20;

		/** @var integer $number_of_ratings */
		$number_of_ratings = $api->num_ratings;

		/** @var string $screenshot_url - link to screenshot */
		$screenshot_url = $api->screenshot_url;

		/** @var integer $count - contains total downloads value */
		$count = $api->downloaded;

		/** @var string $download_link - link to direct download from WordPress */
		$download_link = $api->download_link;

		/** @var string $last_updated - date as a numeric value */
		$last_updated = $api->last_updated;

		/** @var string $current_version - current version of theme */
		$current_version = $api->version;

		/** Sanity check - make sure there is a value for the count */
		if ( isset( $count ) ) {

			echo $this->display_screenshot( $main_options, $screenshot_url );

			echo 'Theme: ' . $name . ' by ' . $author . '<br />';
			echo 'Last updated: ' . $last_updated . ' (version ' . $current_version . ')<br />';
			echo 'Average Rating: ' . $rating . ' stars (by ' . $number_of_ratings . ' voters)' . '<br />';
			echo 'Total downloads: ' . $count . '<br />';
			echo 'Download your copy <a href="' . $download_link . '">here</a><br />';

		} else {

			_e( 'Are you using a theme from the WordPress Theme repository?', 'bns-td' );

		}
		/** End if - is count set */
	}
	/** End function - theme counter shortcode */


	/**
	 * Widget Title
	 * Returns the widget title based on the theme slug used for the output
	 *
	 * @package    BNS_Theme_Details
	 * @since      0.1
	 *
	 * @param $theme_slug
	 *
	 * @uses       wp_get_theme
	 * @uses       wp_get_theme->get
	 * @uses       wp_get_theme->get_template
	 *
	 * @return string
	 */
	function widget_title( $theme_slug ) {

		$theme_name = ( $theme_slug == wp_get_theme()->get_template() )
			? wp_get_theme()->get_template()
			: wp_get_theme( $theme_slug )->get( 'Name' );

		$title = sprintf( __( '%1$s Download Counter', 'bns-td' ), $theme_name );

		return $title;

	}    /** End function - widget title */

	/**
	 * Display Screenshot
	 * Returns the screenshot URL in its own DIV ... or returns null.
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $screenshot_url
	 *
	 * @return null|string
	 */
	function display_screenshot( $main_options, $screenshot_url ) {
		/** Check if the screenshot link is to be used */
		if ( $main_options['use_screenshot_link'] ) {

			/** Make certain there is a screenshot URL set */
			if ( isset( $screenshot_url ) ) {

				$output = '<div class="bnstd-screenshot aligncenter">';
				$output .= '<img src="' . $screenshot_url . '" />';
				$output .= '</div>';

			} else {

				$output = null;

			}

			/** End if - screenshot URL is set */

			return $output;

		} else {

			return null;

		}
		/** End if - use screenshot link */

	}
	/** End function - display screenshot */

}

/** End class - theme counter */

/** @var object $bns_td - create a new instance of the class */
$bns_td = new BNS_Theme_Details_Widget();