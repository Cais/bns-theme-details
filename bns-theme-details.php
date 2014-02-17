<?php
/*
Plugin Name: BNS Theme Details
Plugin URI: http://buynowshop.com/plugins/bns-theme-details
Description: Displays theme specific details such as download count, last update, author, etc.
Version: 0.1-beta
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
 *
 * @todo           Add hooks where relevant
 * @todo           Finish i18n implementation
 * @todo           Make the download link a button?
 * @todo           Call theme details to add Author URI and/or Theme URI links?
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
		$main_options = array(
			'use_screenshot_link'    => $instance['use_screenshot_link'],
			'show_name'              => $instance['show_name'],
			'show_author'            => $instance['show_author'],
			'show_last_updated'      => $instance['show_last_updated'],
			'show_current_version'   => $instance['show_current_version'],
			'show_rating'            => $instance['show_rating'],
			'show_number_of_ratings' => $instance['show_number_of_ratings'],
			'show_downloaded_count'  => $instance['show_downloaded_count'],
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

			/** Get the theme details */
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
		$instance['use_screenshot_link']    = $new_instance['use_screenshot_link'];
		$instance['show_name']              = $new_instance['show_name'];
		$instance['show_author']            = $new_instance['show_author'];
		$instance['show_last_updated']      = $new_instance['show_last_updated'];
		$instance['show_current_version']   = $new_instance['show_current_version'];
		$instance['show_rating']            = $new_instance['show_rating'];
		$instance['show_number_of_ratings'] = $new_instance['show_number_of_ratings'];
		$instance['show_downloaded_count']  = $new_instance['show_downloaded_count'];
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
			'use_screenshot_link'    => true,
			'show_name'              => true,
			'show_author'            => true,
			'show_last_updated'      => true,
			'show_current_version'   => true,
			'show_rating'            => true,
			'show_number_of_ratings' => true,
			'show_downloaded_count'  => true,
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
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['use_screenshot_link'], true ); ?>
				   id="<?php echo $this->get_field_id( 'use_screenshot_link' ); ?>"
				   name="<?php echo $this->get_field_name( 'use_screenshot_link' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'use_screenshot_link' ); ?>"><?php _e( 'Use screenshot link?', 'bns-td' ); ?></label>
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
			<input class="checkbox" type="checkbox" <?php checked( ( bool ) $instance['show_downloaded_count'], true ); ?>
				   id="<?php echo $this->get_field_id( 'show_downloaded_count' ); ?>"
				   name="<?php echo $this->get_field_name( 'show_downloaded_count' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_downloaded_count' ); ?>"><?php _e( 'Show downloaded count?', 'bns-td' ); ?></label>
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
					'use_screenshot_link'    => true,
					'show_name'              => true,
					'show_author'            => true,
					'show_last_updated'      => true,
					'show_current_version'   => true,
					'show_rating'            => true,
					'show_number_of_ratings' => true,
					'show_downloaded_count'  => true,
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
	 * @uses       BNS_Theme_Details_Widget::display_name_and_author
	 * @uses       BNS_Theme_Details_Widget::display_updated_and_version
	 * @uses       BNS_Theme_Details_Widget::display_rating_and_voters
	 * @uses       BNS_Theme_Details_Widget::display_download_count
	 * @uses       BNS_Theme_Details_Widget::display_download_link
	 * @uses       _e
	 * @uses       themes_api
	 */
	function theme_api_details( $theme_slug, $main_options ) {
		/** Pull in the Theme API file */
		include_once ABSPATH . 'wp-admin/includes/theme.php';

		/** @var object $api - contains theme details */
		$api = themes_api(
			'theme_information', array(
				'slug' => $theme_slug,
				/** 'fields' => array(
				 * 'name'           => true,
				 * 'author'         => true,
				 * 'rating'         => true,
				 * 'num_ratings'    => true,
				 * 'screenshot_url' => true,
				 * 'downloaded'     => true,
				 * 'download_link'  => true,
				 * 'last_updated'   => true
				 * ) */
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

		/** @var integer $count - contains total downloads value */
		$count = $api->downloaded;

		/** @var string $download_link - link to direct download from WordPress */
		$download_link = $api->download_link;

		/** Sanity check - make sure there is a value for the count */
		if ( isset( $count ) ) {

			echo $this->display_screenshot( $main_options, $screenshot_url );

			echo $this->display_name_and_author( $main_options, $name, $author );

			echo $this->display_updated_and_version( $main_options, $last_updated, $current_version );

			echo $this->display_rating_and_voters( $main_options, $rating, $number_of_ratings );

			echo $this->display_download_count( $main_options, $count );

			echo $this->display_download_link( $main_options, $download_link );

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
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $theme_slug
	 *
	 * @uses           __
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

		/** Check if the screenshot link is set and is to be used */
		if ( isset( $screenshot_url ) && $main_options['use_screenshot_link'] ) {

			$output = '<div class="bnstd-screenshot aligncenter">';
			$output .= '<img src="' . $screenshot_url . '" />';
			$output .= '</div>';

			return $output;

		} else {

			return null;

		}
		/** End if - use screenshot link */

	} /** End function - display screenshot */

	/**
	 * Display Name and Author
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
	 * @return null|string
	 */
	function display_name_and_author( $main_options, $name, $author ) {

		/** Make sure there is a theme name set and it is to be shown */
		if ( isset( $name ) && $main_options['show_name'] ) {

			$output = '<div class="bnstd-theme-name">';
			$output .= 'Theme: ' . $name;

			/** Make sure there is an author name set and it is to be shown */
			if ( isset( $author ) && $main_options['show_author'] ) {

				$output .= ' by ' . '<span class="bnstd-theme-author">' . $author . '</span>';

			}
			/** End if - author name is set */

			$output .= '</div>';

			return $output;

		} elseif ( ! $main_options['show_name'] && $main_options['show_author'] ) {

			return '<div class="bnstd-theme-author">' . 'By ' . $author . '</div>';

		} else {

			return null;

		}
		/** End if - theme name is set */

	}
	/** End function - display name and author */


	/**
	 * Display Updated and Version
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
	 * @return null|string
	 */
	function display_updated_and_version( $main_options, $last_updated, $current_version ) {

		/** Make sure the last updated is set and it is to be shown */
		if ( isset( $last_updated ) && $main_options['show_last_updated'] ) {

			$output = '<div class="bnstd-last-updated">';
			$output .= 'Last updated: ' . $last_updated;

			/** Make sure the current version is set and it is to be shown */
			if ( isset( $current_version ) && $main_options['show_current_version'] ) {

				$output .= ' <span class="bnstd-current-version">(version ' . $current_version . ')</span>';

			}
			/** End if - current version is set */

			$output .= '</div>';

			return $output;

		} elseif ( ! $main_options['show_last_updated'] && $main_options['show_current_version'] ) {

			return '<div class="bnstd-current-version">' . 'Current version: ' . $current_version . '</div>';

		} else {

			return null;

		}
		/** End if - last updated is set */

	}
	/** End function - display updated and version */


	/**
	 * Display Ratings and Voters
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
	 * @return null|string
	 */
	function display_rating_and_voters( $main_options, $rating, $number_of_ratings ) {

		/** Check if rating is set an if it should be shown */
		if ( isset( $rating ) && $main_options['show_rating'] ) {

			$output = '<div class="bnstd-rating">';
			$output .= 'Average Rating: ' . $rating . ' stars';

			/** Check if number of ratings is set and if it should be shown */
			if ( isset( $number_of_ratings ) && $main_options['show_number_of_ratings'] ) {

				$output .= ' <span class="bnstd-voters">(by ' . $number_of_ratings . ' voters)</span>';

			} else {

				$output .= null;

			}
			/** End if - number of ratings is set */

			$output .= '</div>';

			return $output;

		} else {

			return null;

		}
		/** End if - rating is set */

	}
	/** End function - display rating and voters */


	/**
	 * Display Download Count
	 * Returns the download count
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $count
	 *
	 * @return string
	 */
	function display_download_count( $main_options, $count ) {

		/** Check if download count is to be shown */
		if ( $main_options['show_downloaded_count'] ) {

			return '<div class="bnstd-download-count">Total downloads: ' . $count . '</div>';

		} else {

			return null;

		}
		/** End if - show count */

	}
	/** End function - display download count */


	/**
	 * Display Download Link
	 * Return the download link if it is set or return null if it is not
	 *
	 * @package        BNS_Theme_Details
	 * @sub-package    Output
	 * @since          0.1
	 *
	 * @param $main_options
	 * @param $download_link
	 *
	 * @return null|string
	 */
	function display_download_link( $main_options, $download_link ) {

		/** Check if download link is set and if it should be shown */
		if ( isset( $download_link ) && $main_options['use_download_link'] ) {

			return '<div class="bnstd-download-link">Download your copy <a class="bnstd-download-link-url" href="' . $download_link . '">here</a></div>';

		} else {

			return null;

		}
		/** End if - download link is set */

	}
	/** End function - display download link */


}

/** End class - theme counter */

/** @var object $bns_td - create a new instance of the class */
$bns_td = new BNS_Theme_Details_Widget();