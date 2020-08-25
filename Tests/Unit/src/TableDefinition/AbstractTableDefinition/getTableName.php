<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\TableDefinition\AbstractTableDefinition;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for AbstractTableDefinition::get_table_name().
 *
 * @covers AbstractTableDefinition::get_table_name
 * @group  AbstractTableDefinition
 */
class Test_GetTableName extends TestCase {
	protected $table_short_name = 'foobar';
	private $network_prefix     = 'network_prefix_';
	private $site_prefix        = 'site_prefix_';

	public function testShouldReturnFullTableName() {
		global $wpdb;

		$table = $this->createMocks( false );

		$result = $table->get_table_name();

		$this->assertSame( $this->site_prefix . $this->table_short_name, $result );

		$table = $this->createMocks( true );

		$result = $table->get_table_name();

		$this->assertSame( $this->network_prefix . $this->table_short_name, $result );
	}

	public function createMocks( $is_table_global ) {
		global $wpdb;

		$wpdb = (object) [
			'base_prefix' => $this->network_prefix,
			'prefix'      => $this->site_prefix,
		];

		DBUtilitiesUnit::set_mocks( [
			'sanitize_table_name' => function( $table_name ) {
				return $table_name;
			}
		] );

		$table = $this->getMockForAbstractClass( AbstractTableDefinition::class, [ DBUtilitiesUnit::class ] );
		$table
			->expects( $this->once() )
			->method( 'is_table_global' )
			->with()
			->willReturn( $is_table_global );
		$table
			->expects( $this->once() )
			->method( 'get_table_short_name' )
			->with()
			->willReturn( $this->table_short_name );

		return $table;
	}
}
