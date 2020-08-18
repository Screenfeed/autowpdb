<?php
namespace Screenfeed\AutoWPDB\Tests\Fixtures\src;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Allows to perform some Integration tests for DBUtilities::create_table() and DBUtilities::delete_table().
 * During the integration tests, WP only allows to create temporary tables, which won't be listed in 'SHOW TABLES'.
 */
class DBUtilitiesIntegration extends DBUtilities {

	public static function table_exists( string $table_name ): bool {
		global $wpdb;

		$query = "SELECT * FROM `$table_name` LIMIT 1";
		$wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return "Table '{$wpdb->dbname}.$table_name' doesn't exist" !== $wpdb->last_error;
	}
}
