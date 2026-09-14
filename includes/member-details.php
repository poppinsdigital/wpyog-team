<?php
/**
 * Member Details Metabox
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- this file is only ever reached via include()/require() from inside a class method; its top-level variables and hook usage are function-local, not real WordPress globals.

$designation = get_post_meta( $post->ID, 'wpyog_designation', true );
$subtitle    = get_post_meta( $post->ID, 'wpyog_subtitle', true );
$short_desc  = get_post_meta( $post->ID, 'wpyog_short_desc', true );
$contacts    = get_post_meta( $post->ID, 'wpyog_contact', true );
$socials     = get_post_meta( $post->ID, 'wpyog_social', true );
if ( ! is_array( $contacts ) ) $contacts = array();
if ( ! is_array( $socials ) )  $socials  = array();
?>

<div class="pt-metabox-wrap">

	<!-- Basic Info -->
	<div class="pt-section">
		<div class="pt-field">
			<label for="wpyog_designation"><?php esc_html_e( 'Designation', 'wpyog-team' ); ?></label>
			<input class="widefat" type="text" name="wpyog_designation" id="wpyog_designation"
				value="<?php echo esc_attr( $designation ); ?>"
				placeholder="<?php esc_attr_e( 'e.g. Senior Developer', 'wpyog-team' ); ?>" />
		</div>
		<div class="pt-field">
			<label for="wpyog_subtitle"><?php esc_html_e( 'Sub Title', 'wpyog-team' ); ?></label>
			<input class="widefat" type="text" name="wpyog_subtitle" id="wpyog_subtitle"
				value="<?php echo esc_attr( $subtitle ); ?>"
				placeholder="<?php esc_attr_e( 'e.g. Full Stack Developer', 'wpyog-team' ); ?>" />
		</div>
		<div class="pt-field">
			<label for="wpyog_short_desc"><?php esc_html_e( 'Short Description', 'wpyog-team' ); ?> <span class="pt-hint">(<?php esc_html_e( '140 characters or less', 'wpyog-team' ); ?>)</span></label>
			<textarea class="widefat" name="wpyog_short_desc" id="wpyog_short_desc" rows="3" maxlength="140"
				placeholder="<?php esc_attr_e( 'A short tagline or summary…', 'wpyog-team' ); ?>"><?php echo esc_textarea( $short_desc ); ?></textarea>
		</div>
	</div>

	<!-- Contact Details -->
	<div class="pt-section">
		<div class="pt-section-header">
			<h4><?php esc_html_e( 'Contact Details', 'wpyog-team' ); ?></h4>
			<span class="pt-section-desc"><?php esc_html_e( 'Add phone, email, or other contact info shown in the popup.', 'wpyog-team' ); ?></span>
		</div>
		<table class="pt-repeater-table" id="pt-contact-table">
			<thead>
				<tr>
					<th class="pt-drag-col"></th>
					<th><?php esc_html_e( 'Label', 'wpyog-team' ); ?></th>
					<th><?php esc_html_e( 'Value / Link', 'wpyog-team' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $contacts ) ) : ?>
					<?php foreach ( $contacts as $row ) : ?>
					<tr class="pt-repeater-row">
						<td class="pt-drag-col"><span class="dashicons dashicons-move"></span></td>
						<td>
							<input type="text" class="widefat" name="wpyog_contact_label[]"
								placeholder="<?php esc_attr_e( 'e.g. Tel', 'wpyog-team' ); ?>"
								value="<?php echo esc_attr( $row['label'] ); ?>" />
						</td>
						<td>
							<input type="text" class="widefat" name="wpyog_contact_value[]"
								placeholder="<?php esc_attr_e( 'e.g. +1 234 567 890', 'wpyog-team' ); ?>"
								value="<?php echo esc_attr( $row['value'] ); ?>" />
						</td>
						<td><a class="button pt-remove-row" href="#"><?php esc_html_e( 'Remove', 'wpyog-team' ); ?></a></td>
					</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="pt-repeater-row">
						<td class="pt-drag-col"><span class="dashicons dashicons-move"></span></td>
						<td><input type="text" class="widefat" name="wpyog_contact_label[]" placeholder="<?php esc_attr_e( 'e.g. Tel', 'wpyog-team' ); ?>" value="" /></td>
						<td><input type="text" class="widefat" name="wpyog_contact_value[]" placeholder="<?php esc_attr_e( 'e.g. +1 234 567 890', 'wpyog-team' ); ?>" value="" /></td>
						<td><a class="button pt-remove-row" href="#"><?php esc_html_e( 'Remove', 'wpyog-team' ); ?></a></td>
					</tr>
				<?php endif; ?>
				<!-- Template row (hidden) -->
				<tr class="pt-repeater-row pt-template-row screen-reader-text">
					<td class="pt-drag-col"><span class="dashicons dashicons-move"></span></td>
					<td><input type="text" class="widefat" name="wpyog_contact_label[]" placeholder="<?php esc_attr_e( 'e.g. Tel', 'wpyog-team' ); ?>" value="" /></td>
					<td><input type="text" class="widefat" name="wpyog_contact_value[]" placeholder="<?php esc_attr_e( 'e.g. +1 234 567 890', 'wpyog-team' ); ?>" value="" /></td>
					<td><a class="button pt-remove-row" href="#"><?php esc_html_e( 'Remove', 'wpyog-team' ); ?></a></td>
				</tr>
			</tbody>
		</table>
		<p><a class="button pt-add-row" href="#" data-table="pt-contact-table"><?php esc_html_e( 'Add row', 'wpyog-team' ); ?></a></p>
	</div>

	<!-- Social Icons -->
	<div class="pt-section">
		<div class="pt-section-header">
			<h4><?php esc_html_e( 'Social Media Icons', 'wpyog-team' ); ?></h4>
			<span class="pt-section-desc"><?php esc_html_e( 'Add social media links displayed in the popup.', 'wpyog-team' ); ?></span>
		</div>
		<table class="pt-repeater-table" id="pt-social-table">
			<thead>
				<tr>
					<th class="pt-drag-col"></th>
					<th><?php esc_html_e( 'Icon', 'wpyog-team' ); ?></th>
					<th><?php esc_html_e( 'Link / Email / Phone', 'wpyog-team' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $socials ) ) : ?>
					<?php foreach ( $socials as $row ) : ?>
					<tr class="pt-repeater-row">
						<td class="pt-drag-col"><span class="dashicons dashicons-move"></span></td>
						<td>
							<?php $this->render_select( 'wpyog_social_icon[]', $social_icons, $row['icon'], __( 'Select icon', 'wpyog-team' ), 'pt-icon-select' ); ?>
						</td>
						<td>
							<input type="text" class="widefat" name="wpyog_social_link[]"
								placeholder="<?php esc_attr_e( 'https:// or email or phone', 'wpyog-team' ); ?>"
								value="<?php echo esc_attr( $row['link'] ); ?>" />
						</td>
						<td><a class="button pt-remove-row" href="#"><?php esc_html_e( 'Remove', 'wpyog-team' ); ?></a></td>
					</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="pt-repeater-row">
						<td class="pt-drag-col"><span class="dashicons dashicons-move"></span></td>
						<td><?php $this->render_select( 'wpyog_social_icon[]', $social_icons, '', __( 'Select icon', 'wpyog-team' ), 'pt-icon-select' ); ?></td>
						<td><input type="text" class="widefat" name="wpyog_social_link[]" placeholder="<?php esc_attr_e( 'https:// or email or phone', 'wpyog-team' ); ?>" value="" /></td>
						<td><a class="button pt-remove-row" href="#"><?php esc_html_e( 'Remove', 'wpyog-team' ); ?></a></td>
					</tr>
				<?php endif; ?>
				<!-- Template row (hidden) -->
				<tr class="pt-repeater-row pt-template-row screen-reader-text">
					<td class="pt-drag-col"><span class="dashicons dashicons-move"></span></td>
					<td><?php $this->render_select( 'wpyog_social_icon[]', $social_icons, '', __( 'Select icon', 'wpyog-team' ), 'pt-icon-select' ); ?></td>
					<td><input type="text" class="widefat" name="wpyog_social_link[]" placeholder="<?php esc_attr_e( 'https:// or email or phone', 'wpyog-team' ); ?>" value="" /></td>
					<td><a class="button pt-remove-row" href="#"><?php esc_html_e( 'Remove', 'wpyog-team' ); ?></a></td>
				</tr>
			</tbody>
		</table>
		<p><a class="button pt-add-row" href="#" data-table="pt-social-table"><?php esc_html_e( 'Add row', 'wpyog-team' ); ?></a></p>
	</div>

</div><!-- .pt-metabox-wrap -->
