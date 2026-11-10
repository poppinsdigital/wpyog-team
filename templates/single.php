<?php
/**
 * Single Page Layout Template
 *
 * Cards grid - each card links to the member's own page.
 *
 * @package wpyog-team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- this file is only ever reached via include()/require() from inside a class method; its top-level variables and hook usage are function-local, not real WordPress globals.

$cols         = isset( $options['columns'] ) ? absint( $options['columns'] ) : 3;
$circle       = ! empty( $options['circle_image'] );
$inline_style = $this->get_wrap_style( $options );
?>

<div class="pt-team-wrap pt-single-layout pt-cols-<?php echo esc_attr( $cols ); ?><?php echo $circle ? ' pt-circle-img' : ''; ?>"
	id="pt-team-<?php echo esc_attr( $id ); ?>"
	style="<?php echo esc_attr( $inline_style ); ?>"
	data-team="<?php echo esc_attr( $id ); ?>">

	<div class="pt-grid">

		<?php
		if ( $team_query->have_posts() ) :
			while ( $team_query->have_posts() ) :
				$team_query->the_post();
				$member_id   = get_the_ID();
				$member_data = $this->get_options( 'wpyog_member', $member_id );
				$member_name = get_the_title();
				$member_url  = get_permalink( $member_id );
				$photo       = $this->get_member_thumbnail_url( $member_id, 'wpyog_team' );
				?>

		<div class="pt-card" data-member="<?php echo esc_attr( $member_id ); ?>">
			<a class="pt-card-link" href="<?php echo esc_url( $member_url ); ?>">

				<div class="pt-card-photo">
					<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $member_name ); ?>" loading="lazy" />
				</div>

				<div class="pt-card-info">
					<h3 class="pt-member-name"><?php echo esc_html( $member_name ); ?></h3>
					<?php if ( $member_data['designation'] ) : ?>
						<p class="pt-member-designation"><?php echo esc_html( $member_data['designation'] ); ?></p>
					<?php endif; ?>
					<?php if ( $member_data['subtitle'] ) : ?>
						<p class="pt-member-subtitle"><?php echo esc_html( $member_data['subtitle'] ); ?></p>
					<?php endif; ?>
					<?php if ( $member_data['short_desc'] ) : ?>
						<p class="pt-member-short-desc"><?php echo esc_html( $member_data['short_desc'] ); ?></p>
					<?php endif; ?>
				</div>

			</a>
		</div><!-- .pt-card -->

				<?php
			endwhile;
			wp_reset_postdata();
		endif;
		?>

	</div><!-- .pt-grid -->

</div><!-- .pt-team-wrap -->
