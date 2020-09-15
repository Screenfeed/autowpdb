<?php
/**
 * Interface to work with the database.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\DBWorker;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Reunites tools to work with the database.
 *
 * @since 0.3
 */
interface WorkerInterface {

	/**
	 * Create/Upgrade a table in the database.
	 *
	 * @since 0.1
	 *
	 * @param  string       $table_name   The (prefixed) table name. Use `sanitize_table_name()` before passing it to this method.
	 * @param  string       $schema_query Query representing the table schema.
	 * @param  array<mixed> $args         {
	 *     Optional arguments.
	 *
	 *     @var callable|false|null $logger Callback to use to log errors. The error message is passed to the callback as 1st argument. False to disable log. Null will default to 'error_log'.
	 * }
	 * @return bool                       True on success. False otherwise.
	 */
	public function create_table( string $table_name, string $schema_query, array $args = [] ): bool;

	/**
	 * Tell if the given table exists.
	 *
	 * @since 0.1
	 *
	 * @param  string $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @return bool
	 */
	public function table_exists( string $table_name ): bool;

	/**
	 * Delete the given table (DROP).
	 *
	 * @since  0.2
	 * @source inspired from https://github.com/berlindb/core/blob/734f799e04a9ce86724f2d906b1a6e0fc56fdeb4/table.php#L404-L427.
	 *
	 * @param  string       $table_name Full name of the table (with DB prefix). Use `sanitize_table_name()` before passing it to this method.
	 * @param  array<mixed> $args       {
	 *     Optional arguments.
	 *
	 *     @var callable|false|null $logger Callback to use to log errors. The error message is passed to the callback as 1st argument. False to disable log. Null will default to 'error_log'.
	 * }
	 * @return bool                     True on success. False otherwise.
	 */
	public function delete_table( string $table_name, array $args = [] ): bool;

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
	public function reinit_table( string $table_name ): bool;

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
	public function empty_table( string $table_name ): int;

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
	public function clone_table( string $table_name, string $new_table_name ): bool;

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
	public function copy_table( string $table_name, string $new_table_name ): int;

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
	public function count_table_rows( string $table_name, string $column = '*' ): int;

	/**
	 * Get the DB's last error.
	 * This is merely a wrapper to get $wpdb->last_error.
	 *
	 * @since 0.3
	 *
	 * @return string The error message. An empty string if there is no error.
	 */
	public function get_last_error(): string;

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
	public function sanitize_table_name( string $table_name );

	/**
	 * Change an array of values into a comma separated list, ready to be used in a `IN ()` clause.
	 * WARNING: works only with numeric and string values. Numeric values won't be quoted ('23' will become 23), so they will not be listed as strings.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $values An array of values.
	 * @return string               A comma separated list of values.
	 */
	public function prepare_values_list( array $values ): string;

	/**
	 * Wrap a value in (simple) quotes, unless it is a numeric value.
	 * WARNING: string values must have simple quotes already escaped.
	 *
	 * @since 0.1
	 *
	 * @param  mixed $value A value.
	 * @return mixed
	 */
	public function quote_string( $value ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType, NeutronStandard.Functions.TypeHint.NoReturnType
}
