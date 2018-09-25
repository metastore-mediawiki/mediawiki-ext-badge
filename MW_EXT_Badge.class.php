<?php

namespace MediaWiki\Extension\MetaStore;

use Parser, PPFrame, OutputPage, Skin;

/**
 * Class MW_EXT_Badge
 */
class MW_EXT_Badge {

	/**
	 * Get badge.
	 *
	 * @param $badge
	 *
	 * @return array
	 */
	private static function getBadge( $badge ) {
		$get = MW_EXT_Kernel::getJSON( __DIR__ . '/storage/badge.json' );
		$out = $get['badge'][ $badge ] ?? [] ?: [];

		return $out;
	}

	/**
	 * Get badge type.
	 *
	 * @param $badge
	 * @param $type
	 *
	 * @return mixed|string
	 */
	private static function getBadgeType( $badge, $type ) {
		$badge = self::getBadge( $badge ) ? self::getBadge( $badge ) : '';
		$out   = $badge[ $type ] ?? '' ?: '';

		return $out;
	}

	/**
	 * Get badge ID.
	 *
	 * @param $badge
	 * @param $type
	 *
	 * @return string
	 */
	private static function getBadgeID( $badge, $type ) {
		$type = self::getBadgeType( $badge, $type ) ? self::getBadgeType( $badge, $type ) : '';
		$out  = $type['id'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Get badge icon.
	 *
	 * @param $badge
	 * @param $type
	 *
	 * @return string
	 */
	private static function getBadgeIcon( $badge, $type ) {
		$type = self::getBadgeType( $badge, $type ) ? self::getBadgeType( $badge, $type ) : '';
		$out  = $type['icon'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Get badge category.
	 *
	 * @param $badge
	 * @param $type
	 *
	 * @return string
	 */
	private static function getBadgeCategory( $badge, $type ) {
		$type = self::getBadgeType( $badge, $type ) ? self::getBadgeType( $badge, $type ) : '';
		$out  = $type['category'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Get badge content.
	 *
	 * @param $badge
	 * @param $type
	 *
	 * @return string
	 */
	private static function getBadgeContent( $badge, $type ) {
		$type = self::getBadgeType( $badge, $type ) ? self::getBadgeType( $badge, $type ) : '';
		$out  = $type['content'] ?? '' ?: '';

		return $out;
	}

	/**
	 * Register tag function.
	 *
	 * @param Parser $parser
	 *
	 * @return bool
	 * @throws \MWException
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setFunctionHook( 'badge', [ __CLASS__, 'onRenderTag' ] );

		return true;
	}

	/**
	 * Render tag function.
	 *
	 * @param Parser $parser
	 * @param string $badge
	 * @param string $type
	 *
	 * @return bool|string
	 */
	public static function onRenderTag( Parser $parser, $badge = '', $type = '' ) {
		// Argument: badge.
		$getBadge = MW_EXT_Kernel::outClear( $badge ?? '' ?: '' );
		$outBadge = MW_EXT_Kernel::outNormalize( $getBadge );

		// Argument: type.
		$getType = MW_EXT_Kernel::outClear( $type ?? '' ?: '' );
		$outType = MW_EXT_Kernel::outNormalize( $getType );

		// Check arguments, set error category.
		if ( ! self::getBadge( $outBadge ) || ! self::getBadgeType( $outBadge, $outType ) ) {
			$parser->addTrackingCategory( 'mw-ext-badge-error-category' );

			return false;
		}

		// Get ID.
		$getID = self::getBadgeID( $outBadge, $outType );
		$outID = $getID;

		// Get icon.
		$getIcon = self::getBadgeIcon( $outBadge, $outType );
		$outIcon = $getIcon;

		// Get category.
		$getCategory = self::getBadgeCategory( $outBadge, $outType );
		$outCategory = $getCategory;

		// Get content.
		$getContent = self::getBadgeContent( $outBadge, $outType );
		$outContent = $getContent;

		// Add badge category.
		$parser->addTrackingCategory( 'mw-ext-badge-' . $outCategory );

		// Out HTML.
		$outHTML = '<div class="mw-ext-badge mw-ext-badge-' . $outBadge . ' mw-ext-badge-' . $outType . '>';
		$outHTML .= '<div class="mw-ext-badge-body>';
		$outHTML .= '<div class="mw-ext-badge-icon"><i class="' . $outIcon . '"></i></div>';
		$outHTML .= '<div class="mw-ext-badge-content">' . MW_EXT_Kernel::getMessageText( 'badge', $outContent ) . '</div>';
		$outHTML .= '</div>';
		$outHTML .= '</div>';

		// Out parser.
		$outParser = $outHTML;

		return $outParser;
	}

	/**
	 * Load resource function.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 *
	 * @return bool
	 */
	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$out->addModuleStyles( array( 'ext.mw.badge.styles' ) );

		return true;
	}
}
