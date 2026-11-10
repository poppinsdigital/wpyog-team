<?php
/**
 * Export / Import handler for WPYog Team
 *
 * Handles:
 *  - JSON export of all wpyog_member and wpyog_team posts with meta + photo URLs
 *  - JSON import with image sideloading and member-ID remapping across teams
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPYog_Export_Import {

	/** @var WPYog_Team */
	private $plugin;

	public function __construct( $plugin_instance ) {
		$this->plugin = $plugin_instance;
		add_action( 'admin_post_wpyog_export', array( $this, 'handle_export' ) );
		add_action( 'admin_post_wpyog_import', array( $this, 'handle_import' ) );
	}

	// =========================================================================
	// ADMIN PAGE
	// =========================================================================

	public function render_page() {
		$messages = array();

		if ( isset( $_GET['wpyog_import'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$status = sanitize_key( $_GET['wpyog_import'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			switch ( $status ) {
				case 'success':
					$members = absint( $_GET['members'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$teams   = absint( $_GET['teams']   ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$messages[] = array(
						'type' => 'success',
						'text' => sprintf(
							/* translators: 1: number of members imported, 2: number of teams imported */
							__( 'Import complete: %1$d member(s) and %2$d team(s) created.', 'wpyog-team' ),
							$members,
							$teams
						),
					);
					break;
				case 'no_file':
					$messages[] = array( 'type' => 'error', 'text' => __( 'No file uploaded. Please choose a JSON export file.', 'wpyog-team' ) );
					break;
				case 'invalid':
					$messages[] = array( 'type' => 'error', 'text' => __( 'Invalid file. Please upload a JSON file exported from WPYog Team.', 'wpyog-team' ) );
					break;
				case 'nonce':
					$messages[] = array( 'type' => 'error', 'text' => __( 'Security check failed. Please try again.', 'wpyog-team' ) );
					break;
			}
		}

		include plugin_dir_path( dirname( __FILE__ ) ) . 'templates/export-import.php';
	}

	// =========================================================================
	// EXPORT
	// =========================================================================

	public function handle_export() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'wpyog-team' ) );
		}
		check_admin_referer( 'wpyog_export_action', 'wpyog_export_nonce' );

		$data = array(
			'plugin'      => 'wpyog-team',
			'version'     => '1.0.17',
			'exported_at' => current_time( 'mysql' ),
			'site_url'    => get_site_url(),
			'members'     => $this->export_members(),
			'teams'       => $this->export_teams(),
		);

		$filename = 'wpyog-team-export-' . gmdate( 'Y-m-d-His' ) . '.json';

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		exit;
	}

	private function export_members() {
		$members = array();

		$query = new WP_Query( array(
			'post_type'        => 'wpyog_member',
			'posts_per_page'   => -1,
			'post_status'      => array( 'publish', 'draft' ),
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => false,
			'no_found_rows'    => true,
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();

				$photo_url = '';
				if ( has_post_thumbnail( $id ) ) {
					$src = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
					$photo_url = $src ? $src[0] : '';
				}

				$members[] = array(
					'id'          => $id,
					'title'       => get_the_title(),
					'content'     => get_the_content(),
					'status'      => get_post_status(),
					'photo_url'   => $photo_url,
					'designation' => get_post_meta( $id, 'wpyog_designation', true ) ?: '',
					'subtitle'    => get_post_meta( $id, 'wpyog_subtitle',    true ) ?: '',
					'short_desc'  => get_post_meta( $id, 'wpyog_short_desc',  true ) ?: '',
					'contact'     => get_post_meta( $id, 'wpyog_contact',     true ) ?: array(),
					'social'      => get_post_meta( $id, 'wpyog_social',      true ) ?: array(),
				);
			}
			wp_reset_postdata();
		}

		return $members;
	}

	private function export_teams() {
		$teams = array();

		$query = new WP_Query( array(
			'post_type'        => 'wpyog_team',
			'posts_per_page'   => -1,
			'post_status'      => array( 'publish', 'draft' ),
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => false,
			'no_found_rows'    => true,
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();

				$teams[] = array(
					'id'         => $id,
					'title'      => get_the_title(),
					'status'     => get_post_status(),
					'memberlist' => get_post_meta( $id, 'memberlist',  true ) ?: array(),
					'layout'     => get_post_meta( $id, 'layout',      true ) ?: 'popup',
					'columns'    => get_post_meta( $id, 'columns',     true ) ?: 3,
					'circle_image' => get_post_meta( $id, 'circle_image', true ) === '1' ? 1 : 0,
					'single_pagination' => get_post_meta( $id, 'single_pagination', true ) === '0' ? 0 : 1,
					'margin'     => get_post_meta( $id, 'pt_margin',   true ) ?: array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ),
					'padding'    => get_post_meta( $id, 'pt_padding',  true ) ?: array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ),
				);
			}
			wp_reset_postdata();
		}

		return $teams;
	}

	// =========================================================================
	// IMPORT
	// =========================================================================

	public function handle_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'wpyog-team' ) );
		}

		$redirect_base = admin_url( 'edit.php?post_type=wpyog_member&page=wpyog-export-import' );

		if (
			! isset( $_POST['wpyog_import_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpyog_import_nonce'] ) ), 'wpyog_import_action' )
		) {
			wp_safe_redirect( add_query_arg( 'wpyog_import', 'nonce', $redirect_base ) );
			exit;
		}

		if ( empty( $_FILES['wpyog_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( 'wpyog_import', 'no_file', $redirect_base ) );
			exit;
		}

		$tmp_path     = sanitize_text_field( wp_unslash( $_FILES['wpyog_import_file']['tmp_name'] ) );
		$file_content = file_get_contents( $tmp_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading an uploaded temp file, no core alternative for local JSON reads.
		$data         = json_decode( $file_content, true );

		if ( ! is_array( $data ) || ( $data['plugin'] ?? '' ) !== 'wpyog-team' ) {
			wp_safe_redirect( add_query_arg( 'wpyog_import', 'invalid', $redirect_base ) );
			exit;
		}

		$id_map        = array();
		$members_count = 0;
		$teams_count   = 0;

		if ( ! empty( $data['members'] ) && is_array( $data['members'] ) ) {
			foreach ( $data['members'] as $member ) {
				$new_id = $this->import_member( $member );
				if ( $new_id ) {
					$id_map[ absint( $member['id'] ) ] = $new_id;
					$members_count++;
				}
			}
		}

		if ( ! empty( $data['teams'] ) && is_array( $data['teams'] ) ) {
			foreach ( $data['teams'] as $team ) {
				$new_id = $this->import_team( $team, $id_map );
				if ( $new_id ) {
					$teams_count++;
				}
			}
		}

		wp_safe_redirect( add_query_arg( array(
			'wpyog_import' => 'success',
			'members'      => $members_count,
			'teams'        => $teams_count,
		), $redirect_base ) );
		exit;
	}

	private function import_member( array $member ) {
		$post_data = array(
			'post_title'   => sanitize_text_field( $member['title']   ?? '' ),
			'post_content' => wp_kses_post( $member['content']        ?? '' ),
			'post_status'  => sanitize_key( $member['status']         ?? 'publish' ),
			'post_type'    => 'wpyog_member',
		);

		$new_id = wp_insert_post( $post_data, true );
		if ( is_wp_error( $new_id ) || ! $new_id ) {
			return false;
		}

		$text_meta = array(
			'designation' => 'wpyog_designation',
			'subtitle'    => 'wpyog_subtitle',
		);
		foreach ( $text_meta as $key => $meta_key ) {
			$val = sanitize_text_field( $member[ $key ] ?? '' );
			if ( $val ) {
				update_post_meta( $new_id, $meta_key, $val );
			}
		}

		$short_desc = sanitize_textarea_field( $member['short_desc'] ?? '' );
		if ( $short_desc ) {
			update_post_meta( $new_id, 'wpyog_short_desc', $short_desc );
		}

		if ( ! empty( $member['contact'] ) && is_array( $member['contact'] ) ) {
			$contacts = array();
			foreach ( $member['contact'] as $c ) {
				$contacts[] = array(
					'label' => sanitize_text_field( $c['label'] ?? '' ),
					'value' => sanitize_text_field( $c['value'] ?? '' ),
				);
			}
			if ( $contacts ) {
				update_post_meta( $new_id, 'wpyog_contact', $contacts );
			}
		}

		if ( ! empty( $member['social'] ) && is_array( $member['social'] ) ) {
			$socials = array();
			foreach ( $member['social'] as $s ) {
				$ic = sanitize_text_field( $s['icon'] ?? '' );
				$lk = esc_url_raw( $s['link'] ?? '' );
				if ( $ic ) {
					$socials[] = array( 'icon' => $ic, 'link' => $lk );
				}
			}
			if ( $socials ) {
				update_post_meta( $new_id, 'wpyog_social', $socials );
			}
		}

		if ( ! empty( $member['photo_url'] ) ) {
			$attach_id = $this->sideload_image(
				esc_url_raw( $member['photo_url'] ),
				$new_id,
				sanitize_text_field( $member['title'] ?? '' )
			);
			if ( $attach_id && ! is_wp_error( $attach_id ) ) {
				set_post_thumbnail( $new_id, $attach_id );
			}
		}

		return $new_id;
	}

	private function import_team( array $team, array $id_map ) {
		$old_memberlist = is_array( $team['memberlist'] ?? null ) ? $team['memberlist'] : array();
		$new_memberlist = array();
		foreach ( $old_memberlist as $old_id ) {
			$old_id = absint( $old_id );
			if ( isset( $id_map[ $old_id ] ) ) {
				$new_memberlist[] = $id_map[ $old_id ];
			}
		}

		$post_data = array(
			'post_title'  => sanitize_text_field( $team['title']  ?? '' ),
			'post_status' => sanitize_key( $team['status']        ?? 'publish' ),
			'post_type'   => 'wpyog_team',
		);

		$new_id = wp_insert_post( $post_data, true );
		if ( is_wp_error( $new_id ) || ! $new_id ) {
			return false;
		}

		update_post_meta( $new_id, 'memberlist', $new_memberlist );
		update_post_meta( $new_id, 'layout',     sanitize_key( $team['layout']  ?? 'popup' ) );
		update_post_meta( $new_id, 'columns',    absint( $team['columns']        ?? 3 ) );
		update_post_meta( $new_id, 'circle_image', ! empty( $team['circle_image'] ) ? '1' : '0' );
		update_post_meta( $new_id, 'single_pagination', isset( $team['single_pagination'] ) && empty( $team['single_pagination'] ) ? '0' : '1' );

		$sides_default = array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 );

		$margin  = is_array( $team['margin']  ?? null ) ? $team['margin']  : $sides_default;
		$padding = is_array( $team['padding'] ?? null ) ? $team['padding'] : $sides_default;

		foreach ( array_keys( $sides_default ) as $side ) {
			$margin[ $side ]  = absint( $margin[ $side ]  ?? 0 );
			$padding[ $side ] = absint( $padding[ $side ] ?? 0 );
		}

		update_post_meta( $new_id, 'pt_margin',  $margin );
		update_post_meta( $new_id, 'pt_padding', $padding );

		return $new_id;
	}

	private function sideload_image( $url, $post_id, $desc = '' ) {
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		return media_sideload_image( $url, $post_id, sanitize_text_field( $desc ), 'id' );
	}
}
