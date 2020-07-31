<?php
/**
 * Abstract class to use to interact with the DB table.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\CRUD;

use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use stdClass;
use Traversable;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class that contains some tools to help interacting with the DB table.
 *
 * @since 0.1
 * @uses  $GLOBALS['wpdb']
 * @uses  esc_sql()
 * @uses  maybe_unserialize()
 * @uses  maybe_serialize()
 */
abstract class AbstractCRUD implements CRUDInterface {

	/**
	 * The table to interact with.
	 *
	 * @var   TableDefinitionInterface
	 * @since 0.1
	 */
	protected $table_definition;

	/**
	 * Stores the list of columns that must be (un)serialized.
	 * Example:
	 *     [
	 *         'foo'  => [],
	 *         'data' => [],
	 *     ]
	 *
	 * @var   array<array|object>|null
	 * @since 0.1
	 */
	private $to_serialize;

	/**
	 * Stores the list of columns that use the auto_increment attribute.
	 * Example:
	 *     [
	 *         'file_id' => '',
	 *     ]
	 *
	 * @var   array<string>|null
	 * @since 0.1
	 */
	private $auto_increment_columns;

	/**
	 * Get things started.
	 *
	 * @since 0.1
	 *
	 * @param TableDefinitionInterface $table_definition A TableDefinitionInterface object.
	 */
	public function __construct( TableDefinitionInterface $table_definition ) {
		global $wpdb;

		$this->table_definition = $table_definition;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** GETTERS ================================================================================= */
	/** ----------------------------------------------------------------------------------------- */

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

	/** ----------------------------------------------------------------------------------------- */
	/** TOOLS =================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Prepare a list of column names to be used in a query as SELECT fields.
	 * This will lowercase the column names, remove invalid fields, escape, and create a comma separated list.
	 *
	 * @since 0.1
	 *
	 * @param  array<string> $select A list of column names. Use [ '*' ] to get all columns.
	 * @return string|null           A comma separated list of fields. Null if the list is empty.
	 */
	protected function prepare_select_for_query( array $select ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		if ( empty( $select ) ) {
			return null;
		}

		$select = array_values( $select );

		if ( [ '*' ] === $select ) {
			return '*';
		}

		$column_names = array_keys( $this->table_definition->get_column_placeholders() );
		$select       = array_map( 'strtolower', $select );
		$select       = array_intersect( $select, $column_names );

		if ( empty( $select ) ) {
			return null;
		}

		return implode( ',', esc_sql( $select ) );
	}

	/**
	 * Prepare data array to be ready for a query.
	 * This will lowercase the array keys, remove invalid keys, and serialize values that need to be.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $data Data to prepare (in column => value pairs). Missing values are not updated.
	 * @return array<mixed>
	 */
	protected function prepare_data_for_query( array $data ): array {
		if ( empty( $data ) ) {
			return [];
		}

		// Force fields to lower case.
		$data = array_change_key_case( $data );

		// Keep only valid columns.
		$data = array_intersect_key( $data, $this->table_definition->get_column_placeholders() );

		// Maybe serialize some values.
		return $this->serialize_columns( $data );
	}

	/**
	 * Get the placeholders related to the given columns.
	 * This will:
	 * - return placeholders only for the given columns,
	 * - order the placeholders based on the given columns.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $columns A named array of data (in column -> value pairs).
	 * @return array<string>
	 */
	protected function get_placeholders( array $columns ): array {
		$formats = $this->table_definition->get_column_placeholders();
		$formats = array_intersect_key( $formats, $columns );

		return array_merge( $columns, $formats );
	}

	/**
	 * Get the placeholder corresponding to the given column name.
	 * Fall back to '%s'.
	 *
	 * @since 0.1
	 *
	 * @param  string $column The column name.
	 * @return string
	 */
	protected function get_placeholder( string $column ): string {
		$columns = $this->table_definition->get_column_placeholders();
		return isset( $columns[ $column ] ) ? $columns[ $column ] : '%s';
	}

	/**
	 * Get the default value for the given column name.
	 *
	 * @since 0.1
	 *
	 * @param  string $column The column name.
	 * @return mixed|null     The default value. Null if the column does not exist.
	 */
	protected function get_default_value( string $column ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		$columns = $this->table_definition->get_column_defaults();
		return isset( $columns[ $column ] ) ? $columns[ $column ] : null;
	}

	/**
	 * Cast a value and maybe unserialize it.
	 * The placeholder is used to decide how to cast the value.
	 *
	 * @since 0.1
	 *
	 * @param  mixed  $value The value to cast.
	 * @param  string $column   The corresponding column name.
	 * @return mixed
	 */
	protected function cast( $value, string $column ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType, NeutronStandard.Functions.TypeHint.NoReturnType
		$placeholder = $this->get_placeholder( $column );

		if ( '%d' === $placeholder ) {
			return (int) $value;
		}

		if ( '%f' === $placeholder ) {
			return (float) $value;
		}

		if ( $this->is_column_serializable( $column ) ) {
			if ( ! empty( $value ) ) {
				return maybe_unserialize( $value );
			}
			return $this->get_default_value( $column );
		}

		return $value;
	}

	/**
	 * Cast a column.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed>|Traversable<mixed>|stdClass|null $values Array of values to cast.
	 * @param  string                                        $column The corresponding column name.
	 * @return array<mixed>|Traversable<mixed>|stdClass|null
	 */
	protected function cast_col( $values, string $column ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType, NeutronStandard.Functions.TypeHint.NoReturnType
		if ( empty( $values ) || ! $this->is_iterable( $values ) ) {
			return null;
		}

		$is_empty = true;

		foreach ( $values as &$value ) { // @phpstan-ignore-line
			$value    = $this->cast( $value, $column );
			$is_empty = false;
		}

		return $is_empty ? null : $values;
	}

	/**
	 * Cast a row.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed>|Traversable<mixed>|stdClass|null $row_fields A row from the DB.
	 * @return array<mixed>|Traversable<mixed>|stdClass|null
	 */
	protected function cast_row( $row_fields ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType, NeutronStandard.Functions.TypeHint.NoReturnType
		if ( empty( $row_fields ) || ! $this->is_iterable( $row_fields ) ) {
			return null;
		}

		$is_empty = true;

		foreach ( $row_fields as $field => &$value ) { // @phpstan-ignore-line
			$value    = $this->cast( $value, $field );
			$is_empty = false;
		}

		return $is_empty ? null : $row_fields;
	}

	/**
	 * Serialize columns that need to be.
	 *
	 * @since 0.1
	 *
	 * @param  array<mixed> $data An array of values.
	 * @return array<string>      Empty arrays are not serialized: null is returned instead.
	 */
	protected function serialize_columns( array $data ): array {
		if ( ! isset( $this->to_serialize ) ) {
			$this->to_serialize = array_filter(
				$this->table_definition->get_column_defaults(),
				function ( $value ): bool { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
					return is_array( $value ) || is_object( $value );
				}
			);
		}

		if ( empty( $this->to_serialize ) ) {
			return $data;
		}

		$serialized_data = array_intersect_key( $data, $this->to_serialize );

		if ( empty( $serialized_data ) ) {
			return $data;
		}

		$serialized_data = array_map(
			function( $value ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType, NeutronStandard.Functions.TypeHint.NoReturnType
				// Try not to store empty serialized arrays.
				$casted = (array) $value;
				return empty( $value ) ? null : maybe_serialize( $value );
			},
			$serialized_data
		);

		return array_merge( $data, $serialized_data );
	}

	/**
	 * Tell if the column value must be (un)serialized.
	 *
	 * @since 0.1
	 *
	 * @param  string $column The column name.
	 * @return bool
	 */
	protected function is_column_serializable( string $column ): bool {
		$value = $this->get_default_value( $column );
		return is_array( $value ) || is_object( $value );
	}

	/**
	 * Get the list of columns that use the auto_increment attribute.
	 *
	 * @since 0.1
	 *
	 * @return array<string> Column names are returned as array keys.
	 */
	protected function get_auto_increment_columns(): array {
		if ( isset( $this->auto_increment_columns ) ) {
			return $this->auto_increment_columns;
		}

		$schema = $this->table_definition->get_table_schema();

		if ( preg_match_all( '@^\s*(?<col_name>[^\s]+)\s.+\sauto_increment,?$@mi', $schema, $matches ) ) {
			$this->auto_increment_columns = array_fill_keys( $matches['col_name'], '' );
		} else {
			$this->auto_increment_columns = [];
		}

		return $this->auto_increment_columns;
	}

	/**
	 * Verify that the content of a variable is an array, a stdClass object, or an object implementing the Traversable interface.
	 *
	 * @since 0.1
	 *
	 * @param  mixed $var Any data.
	 * @return bool
	 */
	protected function is_iterable( $var ): bool { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		return ( is_iterable( $var ) || $var instanceof stdClass );
	}
}
