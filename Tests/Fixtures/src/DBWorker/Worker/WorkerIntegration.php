<?php
namespace Screenfeed\AutoWPDB\Tests\Fixtures\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Allows to perform some Integration tests for Worker->create_table() and Worker->delete_table().
 * During the integration tests, WP only allows to create temporary tables, which won't be listed in 'SHOW TABLES'.
 *
 * @source https://wordpress.stackexchange.com/questions/220275/wordpress-unit-testing-cannot-create-tables
 */
class WorkerIntegration extends Worker {

	public function table_exists( string $table_name ): bool {
		global $wpdb;

		$query = "SELECT * FROM `$table_name` LIMIT 1";
		$wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return "Table '{$wpdb->dbname}.$table_name' doesn't exist" !== $wpdb->last_error;
	}
}
