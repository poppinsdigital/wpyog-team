<?php
/**
 * Export / Import admin page template
 *
 * Variables available from render_page():
 *   $messages  array of { type: 'success'|'error', text: string }
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wpyog-export-import-wrap">

	<h1><?php esc_html_e( 'WPYog Team — Export / Import', 'wpyog-team' ); ?></h1>

	<?php if ( ! empty( $messages ) ) : ?>
		<?php foreach ( $messages as $msg ) : ?>
			<div class="notice notice-<?php echo esc_attr( $msg['type'] ); ?> is-dismissible">
				<p><?php echo esc_html( $msg['text'] ); ?></p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="wpyog-ei-grid">

		<!-- ===================== EXPORT ===================== -->
		<div class="wpyog-ei-card">
			<div class="wpyog-ei-card-header">
				<span class="dashicons dashicons-download"></span>
				<h2><?php esc_html_e( 'Export', 'wpyog-team' ); ?></h2>
			</div>
			<div class="wpyog-ei-card-body">
				<p><?php esc_html_e( 'Download all team members and teams as a JSON file. Use this to back up your data or migrate it to another site.', 'wpyog-team' ); ?></p>
				<ul class="wpyog-ei-list">
					<li><?php esc_html_e( 'All published and draft team members', 'wpyog-team' ); ?></li>
					<li><?php esc_html_e( 'All fields: designation, subtitle, bio, contacts, social links', 'wpyog-team' ); ?></li>
					<li><?php esc_html_e( 'Photo URLs (re-downloaded on import)', 'wpyog-team' ); ?></li>
					<li><?php esc_html_e( 'All teams with members, layout, columns, spacing', 'wpyog-team' ); ?></li>
				</ul>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="wpyog_export" />
					<?php wp_nonce_field( 'wpyog_export_action', 'wpyog_export_nonce' ); ?>
					<button type="submit" class="button button-primary wpyog-ei-btn">
						<span class="dashicons dashicons-download"></span>
						<?php esc_html_e( 'Download Export File', 'wpyog-team' ); ?>
					</button>
				</form>
			</div>
		</div>

		<!-- ===================== IMPORT ===================== -->
		<div class="wpyog-ei-card">
			<div class="wpyog-ei-card-header">
				<span class="dashicons dashicons-upload"></span>
				<h2><?php esc_html_e( 'Import', 'wpyog-team' ); ?></h2>
			</div>
			<div class="wpyog-ei-card-body">
				<p><?php esc_html_e( 'Upload a JSON export file to create members and teams on this site. A new copy of each item is always created — existing content is never overwritten.', 'wpyog-team' ); ?></p>
				<div class="wpyog-ei-notice-info">
					<span class="dashicons dashicons-info-outline"></span>
					<?php esc_html_e( 'Photos are downloaded from the source site. Make sure both sites are accessible during import.', 'wpyog-team' ); ?>
				</div>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
					<input type="hidden" name="action" value="wpyog_import" />
					<?php wp_nonce_field( 'wpyog_import_action', 'wpyog_import_nonce' ); ?>
					<div class="wpyog-ei-file-row">
						<label for="wpyog_import_file" class="screen-reader-text">
							<?php esc_html_e( 'Choose JSON file', 'wpyog-team' ); ?>
						</label>
						<input type="file"
							name="wpyog_import_file"
							id="wpyog_import_file"
							accept=".json,application/json"
							class="wpyog-ei-file-input"
							required />
					</div>
					<button type="submit" class="button button-primary wpyog-ei-btn">
						<span class="dashicons dashicons-upload"></span>
						<?php esc_html_e( 'Import File', 'wpyog-team' ); ?>
					</button>
				</form>
			</div>
		</div>

	</div><!-- .wpyog-ei-grid -->

</div><!-- .wrap -->

<style>
.wpyog-export-import-wrap h1 { margin-bottom: 20px; }
.wpyog-ei-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
	gap: 20px;
	max-width: 900px;
}
.wpyog-ei-card {
	background: #fff;
	border: 1px solid #c3c4c7;
	border-radius: 4px;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.wpyog-ei-card-header {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 14px 20px;
	border-bottom: 1px solid #f0f0f1;
	background: #f6f7f7;
	border-radius: 4px 4px 0 0;
}
.wpyog-ei-card-header h2 {
	margin: 0;
	font-size: 14px;
	font-weight: 600;
}
.wpyog-ei-card-header .dashicons {
	color: #2271b1;
	font-size: 18px;
	width: 18px;
	height: 18px;
}
.wpyog-ei-card-body { padding: 20px; }
.wpyog-ei-card-body p { margin-top: 0; color: #50575e; }
.wpyog-ei-list {
	margin: 0 0 16px 0;
	padding-left: 20px;
	color: #50575e;
}
.wpyog-ei-list li { margin-bottom: 4px; list-style: disc; }
.wpyog-ei-notice-info {
	display: flex;
	align-items: flex-start;
	gap: 6px;
	background: #f0f6fc;
	border-left: 4px solid #2271b1;
	padding: 10px 12px;
	margin-bottom: 16px;
	font-size: 13px;
	color: #1d2327;
	border-radius: 0 2px 2px 0;
}
.wpyog-ei-notice-info .dashicons { color: #2271b1; flex-shrink: 0; margin-top: 1px; }
.wpyog-ei-file-row { margin-bottom: 12px; }
.wpyog-ei-file-input { display: block; width: 100%; }
.wpyog-ei-btn {
	display: inline-flex;
	align-items: center;
	gap: 6px;
}
.wpyog-ei-btn .dashicons { font-size: 16px; width: 16px; height: 16px; }
</style>
