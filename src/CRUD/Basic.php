<?php
/**
 * Class that contains basic CRUD methods.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\CRUD;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to interact with the DB table.
 *
 * @since 0.1
 * @uses  $GLOBALS['wpdb']
 */
class Basic extends AbstractCRUD {

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
	public function insert( array $data ): int {
		global $wpdb;

		// Get the data ready for the query.
		$data = $this->prepare_data_for_query( $data );

		// Add default values to missing fields.
		$data = array_merge( $this->serialize_columns( $this->get_table_definition()->get_column_defaults() ), $data );

		// Remove the auto-increment columns.
		$data = array_diff_key( $data, $this->get_auto_increment_columns() );

		$wpdb->insert(
			$this->get_table_definition()->get_table_name(),
			$data,
			$this->get_placeholders( $data )
		);

		return (int) $wpdb->insert_id;
	}

	/**
	 * Replace a row in the table if it exists or insert a new row in the table if the row did not already exist.
	 * Missing values will fall back to default values.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $data Data to replace (in column => value pairs).
	 * @return int                The row ID.
	 */
	public function replace( array $data ): int {
		global $wpdb;

		// Get the data ready for the query.
		$data = $this->prepare_data_for_query( $data );

		// Add default values to missing fields.
		$data = array_merge( $this->serialize_columns( $this->get_table_definition()->get_column_defaults() ), $data );

		$wpdb->replace(
			$this->get_table_definition()->get_table_name(),
			$data,
			$this->get_placeholders( $data )
		);

		return (int) $wpdb->insert_id;
	}

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
	 * @return array<mixed>|null          An array or an object, depending on $output_type.
	 *                                    If no matching rows are found, or if there is a database error, the return value will be an empty array. If $select is empty, or you pass an invalid $output_type, NULL will be returned.
	 */
	public function get( array $select, array $where, string $output_type = OBJECT ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		global $wpdb;

		$select = $this->prepare_select_for_query( $select );

		if ( null === $select ) {
			return null;
		}

		$table = $this->get_table_definition()->get_table_name();
		$where = $this->prepare_data_for_query( $where );

		if ( ! empty( $where ) ) {
			$formats    = $this->get_placeholders( $where );
			$conditions = [];
			$values     = [];

			foreach ( $where as $field => $value ) {
				if ( is_null( $value ) ) {
					$conditions[] = "`$field` IS NULL";
					continue;
				}

				$conditions[] = "`$field` = " . $formats[ $field ];
				$values[]     = $value;
			}

			$conditions = implode( ' AND ', $conditions );

			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT $select FROM `$table` WHERE $conditions", $values ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				$output_type
			);
		} else {
			$results = $wpdb->get_results(
				"SELECT $select FROM `$table`", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$output_type
			);
		}

		if ( empty( $results ) ) {
			return $results;
		}

		foreach ( $results as &$result ) {
			$result = $this->cast_row( $result );
		}

		return $results;
	}

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
	public function update( array $data, array $where ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		global $wpdb;

		if ( empty( $data ) ) {
			return false;
		}

		// Get the data ready for the query.
		$data = $this->prepare_data_for_query( $data );

		// Remove the auto-increment columns from $data.
		$data = array_diff_key( $data, $this->get_auto_increment_columns() );

		// Get the where clauses ready for the query.
		$where = $this->prepare_data_for_query( $where );

		return $wpdb->update(
			$this->get_table_definition()->get_table_name(),
			$data,
			$where,
			$this->get_placeholders( $data ),
			$this->get_placeholders( $where )
		);
	}

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
	public function delete( array $where ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		global $wpdb;

		if ( empty( $where ) ) {
			return false;
		}

		// Get the data ready for the query.
		$where = $this->prepare_data_for_query( $where );

		return $wpdb->delete(
			$this->get_table_definition()->get_table_name(),
			$where,
			$this->get_placeholders( $where )
		);
	}
}
