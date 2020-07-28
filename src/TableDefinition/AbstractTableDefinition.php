<?php
/**
 * Abstract class to define a table.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\TableDefinition;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class that defines the DB table.
 *
 * @since 0.1
 * @uses  $GLOBALS['wpdb']
 */
abstract class AbstractTableDefinition implements TableDefinitionInterface {

	/**
	 * Get the table name.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_table_name(): string {
		global $wpdb;

		$prefix = $this->is_table_global() ? $wpdb->base_prefix : $wpdb->prefix;

		return $prefix . $this->get_table_short_name();
	}
}
