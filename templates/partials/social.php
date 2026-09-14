<?php
/**
 * Social Icons Partial
 *
 * @package wpyog-team
 *
 * Available vars: $member_data (array with 'social' key)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- this file is only ever reached via include()/require() from inside a class method; its top-level variables and hook usage are function-local, not real WordPress globals.

if ( empty( $member_data['social'] ) ) {
	return;
}

echo '<div class="pt-social-icons">';
foreach ( $member_data['social'] as $s ) {
	$icon = esc_attr( $s['icon'] );
	$link = $s['link'];
	if ( filter_var( $link, FILTER_VALIDATE_EMAIL ) ) {
		$href   = 'mailto:' . esc_attr( $link );
		$target = '';
	} elseif ( $this->validate_phone( $link ) ) {
		$href   = 'tel:' . esc_attr( $link );
		$target = '';
	} else {
		$href   = esc_url( $link );
		$target = ' target="_blank" rel="noopener noreferrer"';
	}
	echo '<a href="' . $href . '"' . $target . '><i class="pt-icon-' . $icon . '" aria-hidden="true"></i></a>'; // phpcs:ignore
}
echo '</div>';
