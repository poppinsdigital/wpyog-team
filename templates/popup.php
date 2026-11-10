<?php
/**
 * Popup Layout Template
 *
 * Cards grid — click to open a modal popup with full member details.
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- this file is only ever reached via include()/require() from inside a class method; its top-level variables and hook usage are function-local, not real WordPress globals.

$cols    = isset( $options['columns'] ) ? absint( $options['columns'] ) : 3;
$circle  = ! empty( $options['circle_image'] );
$margin  = isset( $options['margin'] )  ? $options['margin']  : array();
$padding = isset( $options['padding'] ) ? $options['padding'] : array();

// Build inline style for the wrapper — always output so non-zero values are applied
$m_top    = isset( $margin['top'] )    ? absint( $margin['top'] )    : 0;
$m_right  = isset( $margin['right'] )  ? absint( $margin['right'] )  : 0;
$m_bottom = isset( $margin['bottom'] ) ? absint( $margin['bottom'] ) : 0;
$m_left   = isset( $margin['left'] )   ? absint( $margin['left'] )   : 0;

$p_top    = isset( $padding['top'] )    ? absint( $padding['top'] )    : 0;
$p_right  = isset( $padding['right'] )  ? absint( $padding['right'] )  : 0;
$p_bottom = isset( $padding['bottom'] ) ? absint( $padding['bottom'] ) : 0;
$p_left   = isset( $padding['left'] )   ? absint( $padding['left'] )   : 0;

$inline_style = sprintf(
	'margin:%1$spx %2$spx %3$spx %4$spx; padding:%5$spx %6$spx %7$spx %8$spx;',
	$m_top, $m_right, $m_bottom, $m_left,
	$p_top, $p_right, $p_bottom, $p_left
);

// Collect all members data for the modal
$all_members = array();
if ( $team_query->have_posts() ) {
	while ( $team_query->have_posts() ) {
		$team_query->the_post();
		$member_id   = get_the_ID();
		$member_data = $this->get_options( 'wpyog_member', $member_id );
		$all_members[] = array(
			'id'          => $member_id,
			'name'        => get_the_title(),
			'photo'       => $this->get_member_thumbnail_url( $member_id, 'wpyog_team' ),
			'photo_thumb' => $this->get_member_thumbnail_url( $member_id, 'thumbnail' ),
			'designation' => $member_data['designation'],
			'subtitle'    => $member_data['subtitle'],
			'short_desc'  => $member_data['short_desc'],
			'bio'         => apply_filters( 'the_content', get_the_content() ),
			'contact'     => $member_data['contact'],
			'social'      => $member_data['social'],
		);
	}
	wp_reset_postdata();
}
?>

<div class="pt-team-wrap pt-popup-layout pt-cols-<?php echo esc_attr( $cols ); ?><?php echo $circle ? ' pt-circle-img' : ''; ?>"
	id="pt-team-<?php echo esc_attr( $id ); ?>"
	style="<?php echo esc_attr( $inline_style ); ?>"
	data-team="<?php echo esc_attr( $id ); ?>">

	<div class="pt-grid">

		<?php foreach ( $all_members as $index => $member ) : ?>

		<div class="pt-card"
			data-index="<?php echo esc_attr( $index ); ?>"
			data-member="<?php echo esc_attr( $member['id'] ); ?>">

			<div class="pt-card-photo">
				<img src="<?php echo esc_url( $member['photo'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy" />
			</div>

			<div class="pt-card-info">
				<h3 class="pt-member-name"><?php echo esc_html( $member['name'] ); ?></h3>
				<?php if ( $member['designation'] ) : ?>
					<p class="pt-member-designation"><?php echo esc_html( $member['designation'] ); ?></p>
				<?php endif; ?>
				<?php if ( $member['subtitle'] ) : ?>
					<p class="pt-member-subtitle"><?php echo esc_html( $member['subtitle'] ); ?></p>
				<?php endif; ?>
				<?php if ( $member['short_desc'] ) : ?>
					<p class="pt-member-short-desc"><?php echo esc_html( $member['short_desc'] ); ?></p>
				<?php endif; ?>
			</div>

		</div><!-- .pt-card -->

		<?php endforeach; ?>

	</div><!-- .pt-grid -->

	<!-- Modal overlay — close by clicking outside or pressing ESC (no X button) -->
	<div class="pt-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Team member profile', 'wpyog-team' ); ?>">

		<!-- Close — top-right of overlay backdrop -->
		<button class="pt-modal-close pt-close-overlay" aria-label="<?php esc_attr_e( 'Close', 'wpyog-team' ); ?>">&#215;</button>

		<!-- Desktop side arrows (hidden on mobile via CSS) -->
		<button class="pt-modal-prev pt-nav-side" aria-label="<?php esc_attr_e( 'Previous member', 'wpyog-team' ); ?>">&#8249;</button>

		<div class="pt-modal">

			<!-- Mobile nav bar — dark strip at top with prev/next/close (hidden on desktop) -->
			<div class="pt-modal-nav-bar" aria-hidden="true">
				<div class="pt-nav-arrows">
					<button class="pt-modal-prev pt-nav-mobile" aria-label="<?php esc_attr_e( 'Previous member', 'wpyog-team' ); ?>">&#8249;</button>
					<button class="pt-modal-next pt-nav-mobile" aria-label="<?php esc_attr_e( 'Next member', 'wpyog-team' ); ?>">&#8250;</button>
				</div>
				<button class="pt-modal-close pt-close-mobile" aria-label="<?php esc_attr_e( 'Close', 'wpyog-team' ); ?>">&#215;</button>
			</div>

			<div class="pt-modal-inner">

				<div class="pt-modal-photo">
					<img src="" alt="" class="pt-modal-img" />
				</div>

				<div class="pt-modal-content">

					<p class="pt-modal-designation"></p>
					<h2 class="pt-modal-name"></h2>
					<p class="pt-modal-subtitle"></p>

					<div class="pt-modal-bio"></div>

					<div class="pt-modal-contacts"></div>

					<div class="pt-modal-social"></div>

				</div><!-- .pt-modal-content -->
			</div><!-- .pt-modal-inner -->
		</div><!-- .pt-modal -->

		<!-- Desktop side arrow (hidden on mobile via CSS) -->
		<button class="pt-modal-next pt-nav-side" aria-label="<?php esc_attr_e( 'Next member', 'wpyog-team' ); ?>">&#8250;</button>

	</div><!-- .pt-modal-overlay -->

</div><!-- .pt-team-wrap -->

<?php
// Pass all member data to JS
$members_json = array();
foreach ( $all_members as $m ) {
	$contacts_html = $this->build_contacts_html( $m['contact'] );
	$social_html   = $this->build_social_html( $m['social'] );

	$members_json[] = array(
		'id'          => $m['id'],
		'name'        => $m['name'],
		'photo'       => $m['photo'],
		'designation' => $m['designation'],
		'subtitle'    => $m['subtitle'],
		'bio'         => $m['bio'],
		'contacts'    => $contacts_html,
		'social'      => $social_html,
	);
}

// Output member data inline — directly in shortcode HTML so it is guaranteed
// to be present in the DOM regardless of block theme script-printing order.
// wp_add_inline_script() is unreliable on block themes (WordPress 6.6+) because
// the script handle may already have been printed before the shortcode block renders.
printf(
	'<script id="wpyog-team-data-%1$s">window.ptTeamData=window.ptTeamData||{};window.ptTeamData[%2$s]=%3$s;</script>',
	esc_attr( $id ),
	wp_json_encode( (string) $id ),
	wp_json_encode( $members_json )
);
