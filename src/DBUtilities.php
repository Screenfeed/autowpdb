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
 * @uses  ABSPATH
 * @uses  dbDelta()
 * @uses  esc_sql()
 * @uses  remove_accents()
 * @uses  sanitize_key()
 */
class DBUtilities {

	/**
	 * Create/Upgrade a table in the database.
	 *
	 * @since 0.1
	 *
	 * @param  string       $table_name   The (prefixed) table name. Use `sanitize_table_name()` before passing it to this method.
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

		dbDelta( "CREATE TABLE `$table_name` ($schema_query) $charset_collate;" );

		if ( ! empty( $wpdb->last_error ) ) {
			// The query returned an error.
			empty( $logger ) || call_user_func( $logger, sprintf( 'Error while creating the DB table %s: %s', $table_name, $wpdb->last_error ) );
			return false;
		}

		if ( ! self::table_exists( $table_name ) ) {
			// The table does not exist (wtf).
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
	 * @param  string $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return bool
	 */
	public static function table_exists( string $table_name ): bool {
		global $wpdb;

		$table_name = $wpdb->esc_like( $table_name );
		$query      = "SHOW TABLES LIKE `$table_name`";
		$result     = $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return ( $result === $table_name );
	}

	/**
	 * Delete the given table (DROP).
	 *
	 * @since  0.2
	 * @source inspired from https://github.com/berlindb/core/blob/734f799e04a9ce86724f2d906b1a6e0fc56fdeb4/table.php#L404-L427.
	 *
	 * @param  string $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return bool               True on success. False otherwise.
	 */
	public static function delete_table( string $table_name ): bool {
		global $wpdb;

		$query  = "DROP TABLE `$table_name`";
		$result = $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( true !== $result ) {
			return false;
		}

		return ! static::table_exists( $table_name );
	}

	/**
	 * Reinit the given table (TRUNCATE):
	 * - Delete all entries,
	 * - Reinit auto-increment column.
	 *
	 * @since  0.2
	 * @source Inspired from https://github.com/berlindb/core/blob/734f799e04a9ce86724f2d906b1a6e0fc56fdeb4/table.php#L429-L452.
	 *
	 * @param  string $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return bool               True on success. False otherwise.
	 */
	public static function reinit_table( string $table_name ): bool {
		global $wpdb;

		$query  = "TRUNCATE TABLE `$table_name`";
		$result = $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return true === $result;
	}

	/**
	 * Delete all rows from the given table (DELETE FROM):
	 * - Delete all entries,
	 * - Do NOT reinit auto-increment column,
	 * - Return the number of deleted entries,
	 * - Less performant than reinit.
	 *
	 * @since  0.2
	 * @source Inspired from https://github.com/berlindb/core/blob/734f799e04a9ce86724f2d906b1a6e0fc56fdeb4/table.php#L454-L477.
	 *
	 * @param  string $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return int                Number of deleted rows.
	 */
	public static function empty_table( string $table_name ): int {
		global $wpdb;

		$query = "DELETE FROM `$table_name`";

		return (int) $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Clone the given table (without its contents).
	 *
	 * @since  0.2
	 * @source Inspired from https://github.com/berlindb/core/blob/master/table.php#L479-L515.
	 *
	 * @param  string $table_name     Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @param  string $new_table_name Full name of the new table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return bool                   True on success. False otherwise.
	 */
	public static function clone_table( string $table_name, string $new_table_name ): bool {
		global $wpdb;

		$query  = "CREATE TABLE `$new_table_name` LIKE `$table_name`";
		$result = $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return true === $result;
	}

	/**
	 * Copy the contents of the given table to a new table.
	 *
	 * @since  0.2
	 * @source Inspired from https://github.com/berlindb/core/blob/734f799e04a9ce86724f2d906b1a6e0fc56fdeb4/table.php#L517-L553.
	 *
	 * @param  string $table_name     Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @param  string $new_table_name Full name of the new table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return int                    Number of inserted rows.
	 */
	public static function copy_table( string $table_name, string $new_table_name ): int {
		global $wpdb;

		$query = "INSERT INTO `$new_table_name` SELECT * FROM `$table_name`";

		return (int) $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Count the number of rows in the given table.
	 *
	 * @since  0.2
	 * @source Inspired from https://github.com/berlindb/core/blob/734f799e04a9ce86724f2d906b1a6e0fc56fdeb4/table.php#L555-L578.
	 *
	 * @param  string $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @param  string $column     Name of the column to use in `COUNT()`. Optional, default is `*`.
	 * @return int                Number of rows.
	 */
	public static function count_table_rows( string $table_name, string $column = '*' ): int {
		global $wpdb;

		$prefix = '';
		$column = trim( $column );

		if ( preg_match( '@^DISTINCT\s+(?<column>[^\s]+)$@i', $column, $matches ) ) {
			$prefix = 'DISTINCT ';
			$column = $matches['column'];
		}
		if ( '*' !== $column ) {
			$column = trim( $column, '`\'"' );
			$column = sprintf( '%s`%s`', $prefix, esc_sql( $column ) );
		}

		$query = "SELECT COUNT($column) FROM `$table_name`";

		return (int) $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Sanitize a table name string.
	 * Used to make sure that a table name value meets MySQL expectations.
	 *
	 * Applies the following formatting to a string:
	 * - Trim whitespace,
	 * - No accents,
	 * - No special characters,
	 * - No hyphens,
	 * - No double underscores,
	 * - No trailing underscores.
	 *
	 * @since  0.2
	 * @source Inspired from https://github.com/berlindb/core/blob/4d3a93e6036302957523c4f435ea1a67fc632180/base.php#L193-L244.
	 *
	 * @param  string $table_name The name of the database table.
	 * @return string|null        Sanitized database table name. Null on error.
	 */
	public static function sanitize_table_name( string $table_name ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		if ( empty( $table_name ) ) {
			return null;
		}

		$table_name = trim( $table_name );

		// Only non-accented table names (avoid truncation).
		$table_name = remove_accents( $table_name );

		// Only lowercase characters, hyphens, and dashes (avoid index corruption).
		$table_name = sanitize_key( $table_name );

		// Replace hyphens with single underscores.
		$table_name = str_replace( '-', '_', $table_name );

		// Single underscores only.
		$table_name = preg_replace( '@_{2,}@', '_', $table_name );

		if ( empty( $table_name ) ) {
			return null;
		}

		// Remove trailing underscores.
		$table_name = trim( $table_name, '_' );

		if ( empty( $table_name ) ) {
			return null;
		}

		return $table_name;
	}

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
}
