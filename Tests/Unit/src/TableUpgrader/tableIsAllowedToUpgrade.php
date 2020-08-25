<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::table_is_allowed_to_upgrade().
 *
 * @covers TableUpgrader::table_is_allowed_to_upgrade
 * @group  TableUpgrader
 */
class Test_TableIsAllowedToUpgrade extends TestCase {

	public function testShouldReturnTrue() {
		$upgrader = $this->createMocks( 0, true, false );

		$this->assertTrue( $upgrader->table_is_allowed_to_upgrade() );

		$upgrader = $this->createMocks( 105, false, false );

		$this->assertTrue( $upgrader->table_is_allowed_to_upgrade() );

		$upgrader = $this->createMocks( 105, true, true );

		$this->assertTrue( $upgrader->table_is_allowed_to_upgrade() );
	}

	public function testShouldReturnFalse() {
		$upgrader = $this->createMocks( 105, true, false );

		$this->assertFalse( $upgrader->table_is_allowed_to_upgrade() );
	}

	public function createMocks( $table_version, $is_table_global, $should_upgrade_global_tables ) {
		$table_def = $this->createMock( CustomTable::class );
		$table_def
			->expects( ! empty( $table_version ) ? $this->once() : $this->never() )
			->method( 'is_table_global' )
			->with()
			->willReturn( $is_table_global );

		$table = $this->createMock( Table::class );
		$table
			->expects( $this->once() )
			->method( 'get_table_definition' )
			->with()
			->willReturn( $table_def );

		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'get_db_version' ] )
			->setConstructorArgs( [ $table ] )
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'get_db_version' )
			->with()
			->willReturn( $table_version );

		$has_version_and_is_global = ! empty( $table_version ) && $is_table_global;

		Functions\expect( 'wp_should_upgrade_global_tables' )
			->times( $has_version_and_is_global ? 1 : 0 )
			->andReturn( $should_upgrade_global_tables );

		return $upgrader;
	}
}
