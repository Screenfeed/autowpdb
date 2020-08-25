<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::get_db_version().
 *
 * @covers TableUpgrader::get_db_version
 * @group  TableUpgrader
 */
class Test_GetDbVersion extends TestCase {

	public function testShouldReturnNetworkVersion() {
		$upgrader = $this->createMocks( true, true );

		$this->assertSame( 104, $upgrader->get_db_version() );
	}

	public function testShouldReturnSiteVersion() {
		$upgrader = $this->createMocks( false, true );

		$this->assertSame( 107, $upgrader->get_db_version() );

		$upgrader = $this->createMocks( true, false );

		$this->assertSame( 107, $upgrader->get_db_version() );

		$upgrader = $this->createMocks( false, false );

		$this->assertSame( 107, $upgrader->get_db_version() );
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

		Functions\expect( 'get_site_option' )
			->times( $global_and_multisite ? 1 : 0 )
			->with( $option_name )
			->andReturn( '104' );

		Functions\expect( 'get_option' )
			->times( $global_and_multisite ? 0 : 1 )
			->with( $option_name )
			->andReturn( '107' );

		return $upgrader;
	}
}
