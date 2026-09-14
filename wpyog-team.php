<?php
/**
 * Plugin Name: WPYog Team
 * Plugin URI:  https://popswidgets.com/
 * Description: A clean and modern WordPress plugin to showcase your team with a beautiful popup layout.
 * Version:     1.0.22
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author:      poppinsdigital.com
 * Author URI: https://poppinsdigital.com/
 * License:     GPL v2 or later
 * Text Domain: wpyog-team
 * Domain Path: /language
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPYog_Team' ) ) :

	class WPYog_Team {

		private static $instance = null;
		private $settings;
		private $exporter;

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			$this->settings = array(
				'plugin_path'    => plugin_dir_path( __FILE__ ),
				'plugin_url'     => plugin_dir_url( __FILE__ ),
				'plugin_base'    => dirname( plugin_basename( __FILE__ ) ),
				'plugin_file'    => __FILE__,
				'plugin_version' => '1.0.22',
			);
			$this->run_plugin();
			$this->admin_functions();
			$this->exporter = new WPYog_Export_Import( $this );
		}

		// =========================================================================
		// FRONTEND
		// =========================================================================

		public function run_plugin() {
			add_action( 'init', array( $this, 'register_post_types' ) );
			add_action( 'init', array( $this, 'custom_image_size' ) );
			add_shortcode( 'wpyogteam', array( $this, 'shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

			// ---- SiteOrigin Page Builder compatibility ----
			// SiteOrigin renders at the_content priority 16, AFTER do_shortcode (priority 11).
			// This filter ensures our shortcode processes inside SiteOrigin HTML widgets.
			add_filter( 'siteorigin_panels_widget_output', 'do_shortcode' );

			// Generic widget text shortcode support (Text / HTML widgets)
			add_filter( 'widget_text',        'do_shortcode' );
			add_filter( 'widget_text_content', 'do_shortcode' );
		}

		public function custom_image_size() {
			if ( function_exists( 'add_image_size' ) ) {
				add_image_size( 'wpyog_team', 800, 800, false );
			}
		}

		public function enqueue_frontend() {
			wp_enqueue_style( 'wpyog-team-icons', plugins_url( 'css/icomoon.css', $this->settings['plugin_file'] ), array(), $this->settings['plugin_version'] );
			wp_enqueue_style( 'wpyog-team', plugins_url( 'css/team.css', $this->settings['plugin_file'] ), array(), $this->settings['plugin_version'] );
			wp_enqueue_script( 'wpyog-team', plugins_url( 'js/team-frontend.js', $this->settings['plugin_file'] ), array(), $this->settings['plugin_version'], true );
		}

		public function shortcode( $atts ) {
			$atts = shortcode_atts( array( 'id' => false ), $atts, 'wpyogteam' );
			$id   = absint( $atts['id'] );
			if ( ! $id ) {
				return '<div class="pt-error">' . esc_html__( 'Please provide a team ID.', 'wpyog-team' ) . '</div>';
			}
			$options = $this->get_options( 'wpyog_team', $id );
			if ( ! $options || empty( $options['memberlist'] ) ) {
				return '<div class="pt-error">' . esc_html__( 'No members found in this team. Check that the team has members and is saved.', 'wpyog-team' ) . '</div>';
			}


			$layout   = sanitize_key( $options['layout'] );
			$allowed  = array( 'popup' );
			if ( ! in_array( $layout, $allowed, true ) ) {
				$layout = 'popup';
			}

			$template = $this->settings['plugin_path'] . 'templates/' . $layout . '.php';
			if ( ! file_exists( $template ) ) {
				return '<div class="pt-error">' . esc_html__( 'Template not found.', 'wpyog-team' ) . '</div>';
			}

			ob_start();
			$team_query = new WP_Query( array(
				'post_type'        => 'wpyog_member',
				'post__in'         => $options['memberlist'],
				'orderby'          => 'post__in',
				'posts_per_page'   => -1,
				'post_status'      => 'publish',
				'suppress_filters' => false,
				'no_found_rows'    => true,
			) );
			include $template;
			wp_reset_postdata();
			return ob_get_clean();
		}

		// =========================================================================
		// ADMIN
		// =========================================================================

		public function admin_functions() {
			if ( ! is_admin() ) {
				return;
			}
			add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_items' ), 12 );
			add_action( 'edit_form_after_title', array( $this, 'shortcode_preview' ) );
			add_filter( 'manage_wpyog_member_posts_columns', array( $this, 'member_columns' ) );
			add_action( 'manage_wpyog_member_posts_custom_column', array( $this, 'member_column_data' ), 10, 2 );
			add_filter( 'manage_wpyog_team_posts_columns', array( $this, 'team_columns' ) );
			add_action( 'manage_wpyog_team_posts_custom_column', array( $this, 'team_column_data' ), 10, 2 );
			add_filter( 'admin_post_thumbnail_html', array( $this, 'photo_hint' ) );
			add_filter( 'admin_post_thumbnail_size', array( $this, 'admin_thumb_size' ) );
		}

		public function enqueue_admin( $hook ) {
			global $post;
			if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) ) {
				return;
			}
			if ( ! $post || ! in_array( $post->post_type, array( 'wpyog_member', 'wpyog_team' ), true ) ) {
				return;
			}
			wp_enqueue_style( 'wpyog-team-icons', plugins_url( 'css/icomoon.css', $this->settings['plugin_file'] ), array(), $this->settings['plugin_version'] );
			wp_enqueue_style( 'select2', plugins_url( 'css/select2.min.css', $this->settings['plugin_file'] ), array(), $this->settings['plugin_version'] );
			wp_enqueue_style( 'wpyog-team-admin', plugins_url( 'css/admin.css', $this->settings['plugin_file'] ), array(), $this->settings['plugin_version'] );
			wp_enqueue_script( 'select2', plugins_url( 'js/select2.min.js', $this->settings['plugin_file'] ), array( 'jquery' ), $this->settings['plugin_version'], true );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wpyog-team-admin', plugins_url( 'js/team-admin.js', $this->settings['plugin_file'] ), array( 'jquery', 'jquery-ui-sortable', 'wp-util', 'select2' ), $this->settings['plugin_version'], true );
			wp_localize_script( 'wpyog-team-admin', 'wpyogTeam', array(
				'defaultImage' => esc_url( $this->settings['plugin_url'] . 'images/default-user.png' ),
				'removeText'   => esc_html__( 'Remove', 'wpyog-team' ),
			) );
		}

		public function add_menu_items() {
			add_submenu_page(
				'edit.php?post_type=wpyog_member',
				__( 'Add Team', 'wpyog-team' ),
				__( 'Add Team', 'wpyog-team' ),
				'manage_options',
				'post-new.php?post_type=wpyog_team'
			);
			add_submenu_page(
				'edit.php?post_type=wpyog_member',
				__( 'Export / Import', 'wpyog-team' ),
				__( 'Export / Import', 'wpyog-team' ),
				'manage_options',
				'wpyog-export-import',
				array( $this->exporter, 'render_page' )
			);
		}

		public function shortcode_preview( $post ) {
			if ( 'wpyog_team' === $post->post_type && 'publish' === $post->post_status ) {
				printf(
					'<p class="pt-shortcode-preview">%1$s: <code>[wpyogteam id="%2$s"]</code> <button type="button" class="button pt-copy-sc" data-sc="[wpyogteam id=&quot;%2$s&quot;]">%3$s</button></p>',
					esc_html__( 'Shortcode', 'wpyog-team' ),
					esc_attr( $post->ID ),
					esc_html__( 'Copy', 'wpyog-team' )
				);
			}
		}

		// ---- Columns ----

		public function member_columns( $cols ) {
			return array(
				'cb'          => '<input type="checkbox" />',
				'title'       => __( 'Name', 'wpyog-team' ),
				'pt_photo'    => __( 'Photo', 'wpyog-team' ),
				'designation' => __( 'Designation', 'wpyog-team' ),
				'date'        => __( 'Date', 'wpyog-team' ),
			);
		}

		public function member_column_data( $column, $post_id ) {
			switch ( $column ) {
				case 'pt_photo':
					echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
					break;
				case 'designation':
					echo esc_html( get_post_meta( $post_id, 'wpyog_designation', true ) );
					break;
			}
		}

		public function team_columns( $cols ) {
			return array(
				'cb'        => '<input type="checkbox" />',
				'title'     => __( 'Name', 'wpyog-team' ),
				'members'   => __( 'Members', 'wpyog-team' ),
				'layout'    => __( 'Layout', 'wpyog-team' ),
				'shortcode' => __( 'Shortcode', 'wpyog-team' ),
			);
		}

		public function team_column_data( $column, $post_id ) {
			$opts = $this->get_options( 'wpyog_team', $post_id );
			switch ( $column ) {
				case 'members':
					echo count( (array) $opts['memberlist'] );
					break;
				case 'layout':
					echo esc_html( ucfirst( $opts['layout'] ) );
					break;
				case 'shortcode':
					printf( '<code>[wpyogteam id="%s"]</code>', esc_attr( $post_id ) );
					break;
			}
		}

		public function admin_thumb_size( $size ) {
			global $post_type;
			if ( 'wpyog_member' === $post_type ) {
				$size = 'wpyog_team';
			}
			return $size;
		}

		public function photo_hint( $html ) {
			global $post_type, $post;
			if ( 'wpyog_member' === $post_type && $post && ! has_post_thumbnail( $post->ID ) ) {
				$html .= '<p class="description">' . esc_html__( 'Recommended: min. 800px on the longer side.', 'wpyog-team' ) . '</p>';
			}
			return $html;
		}

		// ---- Metaboxes ----

		public function register_metaboxes() {
			add_meta_box( 'pt_member_details', __( 'Member Details', 'wpyog-team' ), array( $this, 'render_member_metabox' ), 'wpyog_member', 'normal', 'high' );
			add_meta_box( 'pt_team_details', __( 'Team Details', 'wpyog-team' ), array( $this, 'render_team_metabox' ), 'wpyog_team', 'normal', 'high' );
		}

		public function render_member_metabox( $post ) {
			wp_nonce_field( 'pt_save_post_action', 'pt_nonce' );
			$social_icons = $this->get_social_icons();
			include $this->settings['plugin_path'] . 'includes/member-details.php';
		}

		public function render_team_metabox( $post ) {
			wp_nonce_field( 'pt_save_post_action', 'pt_nonce' );
			$members_query = new WP_Query( array(
				'post_type'      => 'wpyog_member',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
			) );
			$options     = $this->get_options( 'wpyog_team', $post->ID );
			$default_img = $this->settings['plugin_url'] . 'images/default-user.png';
			include $this->settings['plugin_path'] . 'includes/team-details.php';
		}

		// ---- Save ----

		public function save_meta( $post_id, $post ) {
			// Only run for our own post types
			if ( ! in_array( $post->post_type, array( 'wpyog_member', 'wpyog_team' ), true ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
						if ( ! isset( $_POST['pt_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pt_nonce'] ) ), 'pt_save_post_action' ) ) {
				return;
			}
			$pto = get_post_type_object( $post->post_type );
			if ( ! current_user_can( $pto->cap->edit_post, $post_id ) ) {
				return;
			}
			if ( 'wpyog_member' === $post->post_type ) {
				$this->save_member_meta( $post_id );
			} elseif ( 'wpyog_team' === $post->post_type ) {
				$this->save_team_meta( $post_id );
			}
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce ('pt_nonce') is verified in save_meta() before this private method is ever called.
		private function save_member_meta( $post_id ) {
			// Simple text fields
			foreach ( array( 'wpyog_designation', 'wpyog_subtitle', 'wpyog_short_desc' ) as $field ) {
				$new = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
				if ( $new ) {
					update_post_meta( $post_id, $field, $new );
				} else {
					delete_post_meta( $post_id, $field );
				}
			}

			// Contact details repeater
			$labels   = isset( $_POST['wpyog_contact_label'] ) ? (array) wp_unslash( $_POST['wpyog_contact_label'] ) : array(); // phpcs:ignore
			$values   = isset( $_POST['wpyog_contact_value'] ) ? (array) wp_unslash( $_POST['wpyog_contact_value'] ) : array(); // phpcs:ignore
			$contacts = array();
			foreach ( $labels as $i => $label ) {
				$l = sanitize_text_field( $label );
				$v = sanitize_text_field( isset( $values[ $i ] ) ? $values[ $i ] : '' );
				if ( $l || $v ) {
					$contacts[] = array( 'label' => $l, 'value' => $v );
				}
			}
			if ( $contacts ) {
				update_post_meta( $post_id, 'wpyog_contact', $contacts );
			} else {
				delete_post_meta( $post_id, 'wpyog_contact' );
			}

			// Social icons repeater
			$icons   = isset( $_POST['wpyog_social_icon'] ) ? (array) wp_unslash( $_POST['wpyog_social_icon'] ) : array(); // phpcs:ignore
			$links   = isset( $_POST['wpyog_social_link'] ) ? (array) wp_unslash( $_POST['wpyog_social_link'] ) : array(); // phpcs:ignore
			$socials = array();
			foreach ( $icons as $i => $icon ) {
				$ic = sanitize_text_field( $icon );
				$lk = esc_url_raw( isset( $links[ $i ] ) ? $links[ $i ] : '' );
				if ( $ic ) {
					$socials[] = array( 'icon' => $ic, 'link' => $lk );
				}
			}
			if ( $socials ) {
				update_post_meta( $post_id, 'wpyog_social', $socials );
			} else {
				delete_post_meta( $post_id, 'wpyog_social' );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce ('pt_nonce') is verified in save_meta() before this private method is ever called.
		private function save_team_meta( $post_id ) {
			// Memberlist
			$new_members = isset( $_POST['memberlist'] ) ? array_map( 'absint', (array) $_POST['memberlist'] ) : array();
			if ( $new_members ) {
				update_post_meta( $post_id, 'memberlist', $new_members );
			} else {
				delete_post_meta( $post_id, 'memberlist' );
			}

			// Layout
			$layout = isset( $_POST['layout'] ) ? sanitize_key( $_POST['layout'] ) : 'popup';
			update_post_meta( $post_id, 'layout', $layout );

			// Columns
			$columns = isset( $_POST['columns'] ) ? absint( $_POST['columns'] ) : 3;
			update_post_meta( $post_id, 'columns', $columns );

			// Margin / Padding
			foreach ( array( 'margin', 'padding' ) as $box ) {
				$data = array();
				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
					$data[ $side ] = isset( $_POST[ $box . '_' . $side ] ) ? absint( $_POST[ $box . '_' . $side ] ) : 0;
				}
				update_post_meta( $post_id, 'pt_' . $box, $data );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// =========================================================================
		// CPT REGISTRATION
		// =========================================================================

		public function register_post_types() {
			if ( ! post_type_exists( 'wpyog_member' ) ) {
				register_post_type( 'wpyog_member', array(
					'labels'             => array(
						'name'               => __( 'Team Members', 'wpyog-team' ),
						'singular_name'      => __( 'Team Member', 'wpyog-team' ),
						'menu_name'          => __( 'Team', 'wpyog-team' ),
						'add_new'            => __( 'Add New Member', 'wpyog-team' ),
						'add_new_item'       => __( 'Add Team Member', 'wpyog-team' ),
						'edit_item'          => __( 'Edit Team Member', 'wpyog-team' ),
						'view_item'          => __( 'View Team Member', 'wpyog-team' ),
						'all_items'          => __( 'Members', 'wpyog-team' ),
						'search_items'       => __( 'Search Members', 'wpyog-team' ),
						'not_found'          => __( 'No members found.', 'wpyog-team' ),
						'not_found_in_trash' => __( 'No members in trash.', 'wpyog-team' ),
					),
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'capability_type'    => 'post',
					'supports'           => array( 'title', 'editor', 'thumbnail' ),
					'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSIjYTdhYWFkIiB0cmFuc2Zvcm09Im1hdHJpeCgxLDAsMCwtMSwwLDQ4MCkiIGQ9Ik0yNTUgNTAxYy0xMzUtMS0yNDQtMTExLTI0NC0yNDYgMS0xMzUgMTExLTI0NCAyNDYtMjQ0IDEzNSAxIDI0NCAxMTEgMjQ0IDI0Ni0xIDEzNS0xMTEgMjQ0LTI0NiAyNDR6IG0tMTg4LTI0NmMwIDI3IDIyIDQ5IDUwIDQ5IDI3IDEgNDktMjEgNDktNDkgMC0yNy0yMi00OS00OS00OS0yNyAwLTUwIDIyLTUwIDQ5eiBtMzA1IDE2NmMtNS02LTExLTE2LTE2LTI5bC04OS0yMzYtMTgtNDhjLTctMTgtMTUtMzEtMjMtMzktMTEtOS0yNS0xNC00My0xNC0xMSAwLTIwIDMtMjcgOC03IDYtMTEgMTQtMTEgMjUgMCA4IDMgMTUgOCAyMSA1IDUgMTIgOCAyMSA4IDkgMCAxNS0zIDIwLTcgNi00IDgtMTEgOC0xOSAxLTExLTQtMTktMTQtMjUgMSAwIDIgMCAzIDAgMjAgMCAzNiAxNCA0NiA0M2wyMCA1MC0xMDEgMjM5Yy02IDEzLTEzIDIxLTE5IDI1LTYgNC0xMiA2LTE5IDZsMCAxMmMxOC0zIDM1LTQgNTQtNCAxNiAwIDQwIDIgNzIgNGwwLTExYy05IDAtMTYtMS0yMS0xLTUtMS05LTMtMTMtNi0zLTItNS03LTUtMTMgMC02IDMtMTUgNy0yN2w2Ny0xNjMgNjAgMTU5YzQgMTEgNiAxOSA2IDI1IDAgOS0zIDE1LTEwIDE5LTYgNC0xNyA3LTMxIDdsMCAxMWMxOS0xIDM1LTEgNDYtMSAxNiAwIDMwIDEgNDIgMmwwLTExYy03LTEtMTQtNS0yMC0xMHoiLz48L3N2Zz4=',
					'show_in_rest'       => true,
				) );
			}

			if ( ! post_type_exists( 'wpyog_team' ) ) {
				register_post_type( 'wpyog_team', array(
					'labels'          => array(
						'name'               => __( 'Teams', 'wpyog-team' ),
						'singular_name'      => __( 'Team', 'wpyog-team' ),
						'menu_name'          => __( 'Teams', 'wpyog-team' ),
						'add_new'            => __( 'Add Team', 'wpyog-team' ),
						'add_new_item'       => __( 'Add Team', 'wpyog-team' ),
						'edit_item'          => __( 'Edit Team', 'wpyog-team' ),
						'all_items'          => __( 'Teams', 'wpyog-team' ),
						'not_found'          => __( 'No teams found.', 'wpyog-team' ),
						'not_found_in_trash' => __( 'No teams in trash.', 'wpyog-team' ),
					),
					'show_ui'         => true,
					'show_in_menu'    => 'edit.php?post_type=wpyog_member',
					'capability_type' => 'post',
					'supports'        => array( 'title' ),
				) );
			}
		}

		// =========================================================================
		// HELPERS
		// =========================================================================

		public function get_options( $post_type, $post_id ) {
			if ( ! get_post( $post_id ) ) {
				return false;
			}
			if ( 'wpyog_member' === $post_type ) {
				return array(
					'designation' => get_post_meta( $post_id, 'wpyog_designation', true ) ?: '',
					'subtitle'    => get_post_meta( $post_id, 'wpyog_subtitle', true ) ?: '',
					'short_desc'  => get_post_meta( $post_id, 'wpyog_short_desc', true ) ?: '',
					'contact'     => get_post_meta( $post_id, 'wpyog_contact', true ) ?: array(),
					'social'      => get_post_meta( $post_id, 'wpyog_social', true ) ?: array(),
				);
			}
			if ( 'wpyog_team' === $post_type ) {
				return array(
					'memberlist' => get_post_meta( $post_id, 'memberlist', true ) ?: array(),
					'layout'     => get_post_meta( $post_id, 'layout', true ) ?: 'popup',
					'columns'    => get_post_meta( $post_id, 'columns', true ) ?: '3',
					'margin'     => get_post_meta( $post_id, 'pt_margin', true ) ?: array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ),
					'padding'    => get_post_meta( $post_id, 'pt_padding', true ) ?: array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ),
				);
			}
			return false;
		}

		public function get_member_thumbnail( $member_id, $size = 'wpyog_team' ) {
			$default = '<img src="' . esc_url( $this->settings['plugin_url'] . 'images/default-user.png' ) . '" alt="' . esc_attr( get_the_title( $member_id ) ) . '">';
			return has_post_thumbnail( $member_id )
				? get_the_post_thumbnail( $member_id, $size )
				: $default;
		}

		public function get_member_thumbnail_url( $member_id, $size = 'thumbnail' ) {
			if ( has_post_thumbnail( $member_id ) ) {
				$src = wp_get_attachment_image_src( get_post_thumbnail_id( $member_id ), $size );
				return $src ? $src[0] : '';
			}
			return $this->settings['plugin_url'] . 'images/default-user.png';
		}

		public function validate_phone( $phone ) {
			return (bool) preg_match( '/^(?=.*[0-9])[- +()0-9]{4,15}+$/', $phone );
		}

		public function get_social_icons() {
			return array(
				'mail', 'link', 'phone', 'facebook', 'facebook2', 'instagram', 'twitter',
				'linkedin', 'linkedin2', 'youtube', 'vimeo', 'vimeo2', 'github', 'dribbble',
				'behance', 'behance2', 'pinterest', 'pinterest2', 'google-plus', 'google-plus2',
				'whatsapp', 'telegram', 'skype', 'discord', 'slack', 'spotify',
				'snapchat-ghost', 'tumblr', 'tumblr2', 'reddit', 'wordpress', 'wordpress2',
				'dropbox', 'google-drive', 'deviantart', 'twitch', 'steam', 'steam2',
				'soundcloud', 'soundcloud2', 'flickr', 'flickr2', 'vk', 'vine', 'feed', 'feed2',
				'imdb', 'yelp', 'quora', 'meetup', 'viber', 'stumbleupon', 'stumbleupon2',
				'hackernews', 'lastfm', 'lastfm2', 'delicious', 'xing', 'xing2', 'flattr',
				'foursquare', 'blogger', 'blogger2', 'ello', 'mail2', 'mail3',
			);
		}

		public function render_select( $name, $options_list, $selected = '', $placeholder = '', $extra_class = '' ) {
			static $pt_select_counter = 0;
			$pt_select_counter++;
			$uid   = 'pt-select-' . $pt_select_counter;
			$html  = sprintf( '<select name="%s" id="%s" class="pt-select %s">', esc_attr( $name ), esc_attr( $uid ), esc_attr( $extra_class ) );
			if ( $placeholder ) {
				$html .= '<option value="">' . esc_html( $placeholder ) . '</option>';
			}
			foreach ( $options_list as $val ) {
				$html .= sprintf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $val ), selected( $val, $selected, false ) );
			}
			$html .= '</select>';
			echo wp_kses( $html, array(
				'select' => array( 'name' => array(), 'id' => array(), 'class' => array() ),
				'option' => array( 'value' => array(), 'selected' => array() ),
			) );
		}
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/export-import.php';

	function wpyog_team_init() {
		WPYog_Team::get_instance();
	}
	add_action( 'plugins_loaded', 'wpyog_team_init' );

endif;
