<?php
/**
 * Class to work with a table.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB;

use Screenfeed\AutoWPDB\DBUtilities;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to work with a table.
 *
 * @since 0.2
 * @uses  DBUtilities
 */
class Table {

	/**
	 * A TableDefinitionInterface object.
	 *
	 * @var   TableDefinitionInterface
	 * @since 0.2
	 */
	protected $table_definition;

	/**
	 * Get things started.
	 *
	 * @since 0.2
	 *
	 * @param TableDefinitionInterface $table_definition A TableDefinitionInterface object.
	 */
	public function __construct( TableDefinitionInterface $table_definition ) {
		$this->table_definition = $table_definition;
	}

	/**
	 * Get the TableDefinitionInterface object.
	 *
	 * @since 0.2
	 *
	 * @return TableDefinitionInterface
	 */
	public function get_table_definition(): TableDefinitionInterface {
		return $this->table_definition;
	}

	/**
	 * Create/Upgrade the table in the database.
	 *
	 * @since 0.2
	 *
	 * @param  array<mixed> $args {
	 *     Optional arguments.
	 *
	 *     @var callable $logger Callback to use to log errors. The error message is passed to the callback as 1st argument. Default is 'error_log'.
	 * }
	 * @return bool True on success. False otherwise.
	 */
	public function create( array $args = [] ): bool {
		return DBUtilities::create_table( $this->table_definition->get_table_name(), $this->table_definition->get_table_schema(), $args );
	}

	/**
	 * Tell if the table exists.
	 *
	 * @since 0.2
	 *
	 * @return bool
	 */
	public function exists(): bool {
		return DBUtilities::table_exists( $this->table_definition->get_table_name() );
	}

	/**
	 * Delete the table (DROP).
	 *
	 * @since  0.2
	 *
	 * @param  array<mixed> $args {
	 *     Optional arguments.
	 *
	 *     @var callable $logger Callback to use to log errors. The error message is passed to the callback as 1st argument. Default is 'error_log'.
	 * }
	 * @return bool True on success. False otherwise.
	 */
	public function delete( array $args = [] ): bool {
		return DBUtilities::delete_table( $this->table_definition->get_table_name(), $args );
	}

	/**
	 * Reinit the table (TRUNCATE):
	 * - Delete all entries,
	 * - Reinit auto-increment column.
	 *
	 * @since  0.2
	 *
	 * @return bool True on success. False otherwise.
	 */
	public function reinit(): bool {
		return DBUtilities::reinit_table( $this->table_definition->get_table_name() );
	}

	/**
	 * Delete all rows from the table (DELETE FROM):
	 * - Delete all entries,
	 * - Do NOT reinit auto-increment column,
	 * - Return the number of deleted entries,
	 * - Less performant than reinit.
	 *
	 * @since  0.2
	 *
	 * @return int Number of deleted rows.
	 */
	public function empty(): int {
		return DBUtilities::empty_table( $this->table_definition->get_table_name() );
	}

	/**
	 * Clone the table (without its contents).
	 *
	 * @since  0.2
	 *
	 * @param  string $new_table_name Full name of the new table (with DB prefix).
	 * @return bool                   True on success. False otherwise.
	 */
	public function clone_to( string $new_table_name ): bool {
		$new_table_name = DBUtilities::sanitize_table_name( $new_table_name );

		if ( empty( $new_table_name ) ) {
			return false;
		}

		return DBUtilities::clone_table( $this->table_definition->get_table_name(), $new_table_name );
	}

	/**
	 * Copy the contents of the table to a new table.
	 *
	 * @since  0.2
	 *
	 * @param  string $new_table_name Full name of the new table (with DB prefix).
	 * @return int                    Number of inserted rows.
	 */
	public function copy_to( string $new_table_name ): int {
		$new_table_name = DBUtilities::sanitize_table_name( $new_table_name );

		if ( empty( $new_table_name ) ) {
			return 0;
		}

		return DBUtilities::copy_table( $this->table_definition->get_table_name(), $new_table_name );
	}

	/**
	 * Count the number of rows in the table.
	 *
	 * @since  0.2
	 *
	 * @param  string $column Name of the column to use in `COUNT()`. Optional, default is `*`.
	 * @return int            Number of rows.
	 */
	public function count( string $column = '*' ): int {
		return DBUtilities::count_table_rows( $this->table_definition->get_table_name(), $column );
	}
}
