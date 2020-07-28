<?php
/**
 * Utilities to work with the database.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Reunites tools to work with the database.
 *
 * @since 0.1
 * @uses  $GLOBALS['wpdb']
 * @uses  dbDelta()
 */
class DBUtilities {

	/**
	 * Change an array of values into a comma separated list, ready to be used in a `IN ()` clause.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $values An array of values.
	 * @return string               A comma separated list of values.
	 */
	public static function prepare_values_list( array $values ): string {
		$values = esc_sql( (array) $values );
		$values = array_map( [ __CLASS__, 'quote_string' ], $values );
		return implode( ',', $values );
	}

	/**
	 * Wrap a value in quotes, unless it is a numeric value.
	 *
	 * @since 0.1
	 *
	 * @param  mixed $value A value.
	 * @return mixed
	 */
	public static function quote_string( $value ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType, NeutronStandard.Functions.TypeHint.NoReturnType
		return is_numeric( $value ) || ! is_string( $value ) ? $value : "'" . addcslashes( $value, "'" ) . "'";
	}

	/**
	 * Create/Upgrade a table in the database.
	 *
	 * @since 0.1
	 *
	 * @param  string       $table_name   The (prefixed) table name.
	 * @param  string       $schema_query Query representing the table schema.
	 * @param  array<mixed> $args {
	 *     Optional arguments.
	 *
	 *     @var callable $logger Callback to use to log errors. The error message is passed to the callback as 1st argument. Default is 'error_log'.
	 * }
	 * @return bool                       True on success. False otherwise.
	 */
	public static function create_table( string $table_name, string $schema_query, array $args = [] ): bool {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$wpdb->hide_errors();

		$logger          = isset( $args['logger'] ) ? $args['logger'] : 'error_log';
		$charset_collate = $wpdb->get_charset_collate();

		dbDelta( "CREATE TABLE $table_name ($schema_query) $charset_collate;" );

		if ( ! empty( $wpdb->last_error ) ) {
			// The query returned an error.
			empty( $logger ) || call_user_func( $logger, sprintf( 'Error while creating the DB table %s: %s', $table_name, $wpdb->last_error ) );
			return false;
		}

		if ( ! self::table_exists( $table_name ) ) {
			// The table does not exists (wtf).
			empty( $logger ) || call_user_func( $logger, sprintf( 'Creation of the DB table %s failed.', $table_name ) );
			return false;
		}

		return true;
	}

	/**
	 * Tell if the given table exists.
	 *
	 * @since 0.1
	 *
	 * @param  string $table_name Full name of the table (with DB prefix).
	 * @return bool
	 */
	public static function table_exists( string $table_name ): bool {
		global $wpdb;

		$result = $wpdb->get_var(
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$wpdb->esc_like( $table_name )
			)
		);

		return $result === $table_name;
	}
}
