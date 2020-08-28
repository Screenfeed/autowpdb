<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::set_table_ready().
 *
 * @covers TableUpgrader::set_table_ready
 * @group  TableUpgrader
 */
class Test_SetTableReady extends TestCase {

	public function testShouldSetNetworkTableReady() {
		global $wpdb;

		$upgrader = $this->createMocks( true );
		$this->invokeMethod( 'set_table_ready', $upgrader );

		$this->assertTrue( $this->getPropertyValue( 'table_ready', $upgrader ) );
		$this->assertObjectHasAttribute( 'table_short_name', $wpdb );
		$this->assertSame( $wpdb->table_short_name, 'table_name' );
		$this->assertContains( 'table_short_name', $wpdb->global_tables );
		$this->assertNotContains( 'table_short_name', $wpdb->tables );
	}

	public function testShouldSetSiteTableReady() {
		global $wpdb;

		$upgrader = $this->createMocks( false );
		$this->invokeMethod( 'set_table_ready', $upgrader );

		$this->assertTrue( $this->getPropertyValue( 'table_ready', $upgrader ) );
		$this->assertObjectHasAttribute( 'table_short_name', $wpdb );
		$this->assertSame( $wpdb->table_short_name, 'table_name' );
		$this->assertNotContains( 'table_short_name', $wpdb->global_tables );
		$this->assertContains( 'table_short_name', $wpdb->tables );
	}

	public function createMocks( $is_table_global ) {
		global $wpdb;

		$wpdb = (object) [
			'global_tables' => [],
			'tables'        => [],
		];

		$table_def = $this->createMock( CustomTable::class );
		$table_def
			->expects( $this->once() )
			->method( 'get_table_short_name' )
			->with()
			->willReturn( 'table_short_name' );
		$table_def
			->expects( $this->once() )
			->method( 'get_table_name' )
			->with()
			->willReturn( 'table_name' );
		$table_def
			->expects( $this->once() )
			->method( 'is_table_global' )
			->with()
			->willReturn( $is_table_global );

		$table = $this->createMock( Table::class );
		$table
			->expects( $this->once() )
			->method( 'get_table_definition' )
			->with()
			->willReturn( $table_def );

		return new TableUpgrader( $table );
	}
}
