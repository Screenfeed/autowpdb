<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::set_table_not_ready().
 *
 * @covers TableUpgrader::set_table_not_ready
 * @group  TableUpgrader
 */
class Test_SetTableNotReady extends TestCase {

	public function testShouldSetNetworkTableReady() {
		global $wpdb;

		$upgrader = $this->createMocks( true );
		$this->invokeMethod( $upgrader, 'set_table_not_ready' );

		$this->assertFalse( $this->getPropertyValue( $upgrader, 'table_ready' ) );
		$this->assertObjectNotHasAttribute( 'table_short_name', $wpdb );
		$this->assertNotContains( 'table_short_name', $wpdb->global_tables );
		$this->assertContains( 'table_short_name', $wpdb->tables );
	}

	public function testShouldSetSiteTableReady() {
		global $wpdb;

		$upgrader = $this->createMocks( false );
		$this->invokeMethod( $upgrader, 'set_table_not_ready' );

		$this->assertFalse( $this->getPropertyValue( $upgrader, 'table_ready' ) );
		$this->assertObjectNotHasAttribute( 'table_short_name', $wpdb );
		$this->assertContains( 'table_short_name', $wpdb->global_tables );
		$this->assertNotContains( 'table_short_name', $wpdb->tables );
	}

	public function createMocks( $is_table_global ) {
		global $wpdb;

		$wpdb = (object) [
			'table_short_name' => 'table_name',
			'global_tables'    => [ 'table_short_name' ],
			'tables'           => [ 'table_short_name' ],
		];

		$table_def = $this->createMock( CustomTable::class );
		$table_def
			->expects( $this->once() )
			->method( 'get_table_short_name' )
			->with()
			->willReturn( 'table_short_name' );
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
