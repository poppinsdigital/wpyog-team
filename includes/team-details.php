<?php
/**
 * Team Details Metabox
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$margin  = isset( $options['margin'] )  ? $options['margin']  : array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 );
$padding = isset( $options['padding'] ) ? $options['padding'] : array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 );

$layouts = array(
	'popup' => array( 'label' => 'Popup', 'icon' => 'dashicons-external' ),
);

// Build a lookup of all published members (id => title)
$all_members_map = array();
if ( $members_query->have_posts() ) {
	while ( $members_query->have_posts() ) {
		$members_query->the_post();
		$all_members_map[ get_the_ID() ] = get_the_title();
	}
	wp_reset_postdata();
	$members_query->rewind_posts();
}
?>

<div class="pt-metabox-wrap pt-team-wrap">

	<!-- Members -->
	<div class="pt-section">
		<div class="pt-section-header">
			<h4><?php esc_html_e( 'Members', 'wpyog-team' ); ?></h4>
			<span class="pt-section-desc"><?php esc_html_e( 'Select members, drag to reorder.', 'wpyog-team' ); ?></span>
		</div>

		<?php if ( ! empty( $all_members_map ) ) : ?>

		<!-- 
			FALLBACK: A standard <select multiple> that is ALWAYS submitted with the form.
			JS will keep this in sync with the fancy UI list below.
			If JS fails, this still saves correctly.
		-->
		<select id="pt-memberlist-hidden"
			name="memberlist[]"
			multiple
			size="1"
			style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;overflow:hidden;"
			aria-hidden="true">
			<?php foreach ( $all_members_map as $mid => $mtitle ) : ?>
				<option value="<?php echo esc_attr( $mid ); ?>"
					<?php selected( in_array( $mid, (array) $options['memberlist'], true ) ); ?>>
					<?php echo esc_html( $mtitle ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<div class="pt-member-picker">
			<select id="pt-member-add-select" data-placeholder="<?php esc_attr_e( 'Search and add a member…', 'wpyog-team' ); ?>">
				<option value=""></option>
				<?php foreach ( $all_members_map as $mid => $mtitle ) :
					$disabled = in_array( $mid, (array) $options['memberlist'], true ) ? 'disabled' : '';
					$thumb    = $this->get_member_thumbnail_url( $mid, 'thumbnail' );
				?>
					<option value="<?php echo esc_attr( $mid ); ?>"
						data-img="<?php echo esc_attr( $thumb ); ?>"
						<?php echo esc_attr( $disabled ); ?>>
						<?php echo esc_html( $mtitle ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div><!-- .pt-member-picker -->

		<?php else : ?>
			<p>
				<?php esc_html_e( 'No team members found.', 'wpyog-team' ); ?>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wpyog_member' ) ); ?>">
					<?php esc_html_e( 'Add a member', 'wpyog-team' ); ?>
				</a>
			</p>
		<?php endif; ?>

		<ul class="pt-members-selected" id="pt-members-list">
			<?php if ( ! empty( $options['memberlist'] ) ) :
				/*
				 * Iterate the saved array directly (in saved/drag order).
				 * DO NOT use WP_Query with orderby=>post__in — MySQL's FIELD() ordering
				 * can be unreliable depending on server config, causing the backend list
				 * to appear in a different order than what was dragged and saved.
				 */
				foreach ( $options['memberlist'] as $mid ) :
					$mid       = absint( $mid );
					$m_post    = get_post( $mid );
					if ( ! $m_post || 'publish' !== $m_post->post_status ) {
						continue; // skip deleted/draft members
					}
					$thumb = $this->get_member_thumbnail_url( $mid, 'thumbnail' );
			?>
				<li data-id="<?php echo esc_attr( $mid ); ?>">
					<span class="dashicons dashicons-move pt-drag-handle"></span>
					<img width="32" height="32" src="<?php echo esc_url( $thumb ); ?>" alt="">
					<span class="pt-member-name"><?php echo esc_html( $m_post->post_title ); ?></span>
					<span class="pt-remove-member dashicons dashicons-no-alt" data-id="<?php echo esc_attr( $mid ); ?>" title="Remove"></span>
				</li>
			<?php endforeach; ?>
			<?php else : ?>
				<li class="pt-no-members-msg"><?php esc_html_e( 'No members selected. Use the search above to add members.', 'wpyog-team' ); ?></li>
			<?php endif; ?>
		</ul><!-- #pt-members-list -->

	</div><!-- .pt-section -->

	<!-- Layout -->
	<div class="pt-section">
		<div class="pt-section-header">
			<h4><?php esc_html_e( 'Select Layout', 'wpyog-team' ); ?></h4>
		</div>
		<div class="pt-layout-grid">
			<?php foreach ( $layouts as $key => $info ) : ?>
				<label class="pt-layout-option <?php echo $options['layout'] === $key ? 'is-selected' : ''; ?>">
					<input type="radio" name="layout" value="<?php echo esc_attr( $key ); ?>"
						<?php checked( $options['layout'], $key ); ?> />
					<span class="dashicons <?php echo esc_attr( $info['icon'] ); ?>"></span>
					<span class="pt-layout-label"><?php echo esc_html( $info['label'] ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- Columns -->
	<div class="pt-section pt-section-row">
		<div class="pt-field">
			<label for="columns"><?php esc_html_e( 'Set Column', 'wpyog-team' ); ?></label>
			<select name="columns" id="columns" class="pt-select">
				<?php foreach ( array( 2 => '2 Column', 3 => '3 Column', 4 => '4 Column', 5 => '5 Column' ) as $val => $label ) : ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $options['columns'], $val ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<!-- Margin & Padding -->
	<div class="pt-section pt-spacing-section">
		<div class="pt-spacing-group">
			<h4><?php esc_html_e( 'Margin (px)', 'wpyog-team' ); ?></h4>
			<div class="pt-spacing-inputs">
				<?php foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) : ?>
					<div class="pt-spacing-field">
						<input type="number" name="margin_<?php echo esc_attr( $side ); ?>" min="0"
							value="<?php echo esc_attr( isset( $margin[ $side ] ) ? $margin[ $side ] : 0 ); ?>" />
						<label><?php echo esc_html( ucfirst( $side ) ); ?></label>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="pt-spacing-group">
			<h4><?php esc_html_e( 'Padding (px)', 'wpyog-team' ); ?></h4>
			<div class="pt-spacing-inputs">
				<?php foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) : ?>
					<div class="pt-spacing-field">
						<input type="number" name="padding_<?php echo esc_attr( $side ); ?>" min="0"
							value="<?php echo esc_attr( isset( $padding[ $side ] ) ? $padding[ $side ] : 0 ); ?>" />
						<label><?php echo esc_html( ucfirst( $side ) ); ?></label>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>


</div><!-- .pt-team-wrap -->
