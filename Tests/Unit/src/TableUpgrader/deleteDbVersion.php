<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::delete_db_version().
 *
 * @covers TableUpgrader::delete_db_version
 * @group  TableUpgrader
 */
class Test_DeleteDbVersion extends TestCase {

	public function testShouldDeleteNetworkVersion() {
		$upgrader = $this->createMocks( true, true );
		$this->invokeMethod( $upgrader, 'delete_db_version' );
	}

	public function testShouldDeleteSiteVersion() {
		$upgrader = $this->createMocks( false, true );
		$this->invokeMethod( $upgrader, 'delete_db_version' );

		$upgrader = $this->createMocks( true, false );
		$this->invokeMethod( $upgrader, 'delete_db_version' );

		$upgrader = $this->createMocks( false, false );
		$this->invokeMethod( $upgrader, 'delete_db_version' );
	}

	public function createMocks( $is_table_global, $is_multisite ) {
		$option_name = 'wptest_version';

		$table_def = $this->createMock( CustomTable::class );
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

		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'get_db_version_option_name' ] )
			->setConstructorArgs( [ $table ] )
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'get_db_version_option_name' )
			->with()
			->willReturn( $option_name );

		Functions\expect( 'is_multisite' )
			->times( $is_table_global ? 1 : 0 )
			->andReturn( $is_multisite );

		$global_and_multisite = $is_table_global && $is_multisite;

		Functions\expect( 'delete_site_option' )
			->times( $global_and_multisite ? 1 : 0 )
			->with( $option_name );

		Functions\expect( 'delete_option' )
			->times( $global_and_multisite ? 0 : 1 )
			->with( $option_name );

		return $upgrader;
	}
}
