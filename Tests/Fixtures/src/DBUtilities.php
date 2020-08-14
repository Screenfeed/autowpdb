<?php
namespace Screenfeed\AutoWPDB\Tests\Fixtures\src;

use Screenfeed\AutoWPDB\DBUtilities as BaseDBUtilities;
use Screenfeed\AutoWPDB\Tests\Unit\StaticMethodMocker;

/**
 * Allows to mock BaseDBUtilities.
 */
class DBUtilities extends BaseDBUtilities {
	use StaticMethodMocker;

	public static function create_table( string $table_name, string $schema_query, array $args = [] ): bool {
		return static::maybe_mock_static( __FUNCTION__, $table_name, $schema_query, $args );
	}

	public static function table_exists( string $table_name ): bool {
		return static::maybe_mock_static( __FUNCTION__, $table_name );
	}

	public static function delete_table( string $table_name, array $args = [] ): bool {
		return static::maybe_mock_static( __FUNCTION__, $table_name, $args );
	}

	public static function reinit_table( string $table_name ): bool {
		return static::maybe_mock_static( __FUNCTION__, $table_name );
	}

	public static function empty_table( string $table_name ): int {
		return static::maybe_mock_static( __FUNCTION__, $table_name );
	}

	public static function clone_table( string $table_name, string $new_table_name ): bool {
		return static::maybe_mock_static( __FUNCTION__, $table_name, $new_table_name );
	}

	public static function copy_table( string $table_name, string $new_table_name ): int {
		return static::maybe_mock_static( __FUNCTION__, $table_name, $new_table_name );
	}

	public static function count_table_rows( string $table_name, string $column = '*' ): int {
		return static::maybe_mock_static( __FUNCTION__, $table_name, $column );
	}

	public static function sanitize_table_name( string $table_name ) {
		return static::maybe_mock_static( __FUNCTION__, $table_name );
	}

	public static function prepare_values_list( array $values ): string {
		return static::maybe_mock_static( __FUNCTION__, $values );
	}

	public static function quote_string( $value ) {
		return static::maybe_mock_static( __FUNCTION__, $value );
	}

	protected static function can_log( $logger ): bool {
		return static::maybe_mock_static( __FUNCTION__, $logger );
	}
}
