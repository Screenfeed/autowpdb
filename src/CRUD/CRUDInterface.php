<?php
/**
 * Interface to use to interact with a class.
 * This includes only basic methods and some tools.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\CRUD;

use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Interface that interacts with the DB table.
 *
 * @since 0.1
 */
interface CRUDInterface {

	/** ----------------------------------------------------------------------------------------- */
	/** GETTERS ================================================================================= */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Get the table.
	 *
	 * @since 0.1
	 *
	 * @return TableDefinitionInterface
	 */
	public function get_table(): TableDefinitionInterface;

	/** ----------------------------------------------------------------------------------------- */
	/** CREATE ================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Insert a row into the table.
	 * Missing values will fall back to default values.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $data Data to insert (in column => value pairs).
	 * @return int                The row ID.
	 */
	public function insert( array $data ): int;

	/**
	 * Replace a row in the table if it exists or insert a new row in the table if the row did not already exist.
	 * Missing values will fall back to default values.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $data Data to replace (in column => value pairs).
	 * @return int                The row ID.
	 */
	public function replace( array $data ): int;

	/** ----------------------------------------------------------------------------------------- */
	/** READ ==================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Get rows.
	 *
	 * @since 0.1
	 *
	 * @param  array<string> $select      A list of column names. Use [ '*' ] to get all columns.
	 * @param  array<mixed>  $where       A named array of WHERE clauses (in column -> value pairs). Multiple clauses will be joined with ANDs.
	 * @param  string        $output_type Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *                                    With one of the first three, return an array of rows indexed from 0 by SQL result row number.
	 *                                    Each row is an associative array (column => value, ...), a numerically indexed array (0 => value, ...), or an object. (->column = value), respectively.
	 *                                    With OBJECT_K, return an associative array of row objects keyed by the value of each row's first column's value.
	 *                                    Duplicate keys are discarded.
	 * @return mixed                      An array or an object, depending on $output_type.
	 *                                    If no matching rows are found, or if there is a database error, the return value will be an empty array or object. If $select is empty, or you pass an invalid $output_type, NULL will be returned.
	 */
	public function get( array $select, array $where, string $output_type = OBJECT ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType

	/** ----------------------------------------------------------------------------------------- */
	/** UPDATE ================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Update rows.
	 * Missing values are not updated nor emptied.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $data  Data to update (in column => value pairs).
	 *                             This means that if you are using GET or POST data you may need to use stripslashes() to avoid slashes ending up in the database.
	 * @param  array<mixed> $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs.
	 * @return int|false           The number of rows updated. False on error.
	 */
	public function update( array $data, array $where ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType

	/** ----------------------------------------------------------------------------------------- */
	/** DELETE ================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Delete rows.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $where A named array of WHERE clauses (in column -> value pairs). Multiple clauses will be joined with ANDs.
	 * @return int|false           The number of rows updated. False on error.
	 */
	public function delete( array $where ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
}
