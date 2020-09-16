<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::upgrade_table().
 *
 * @covers TableUpgrader::upgrade_table
 * @group  TableUpgrader
 */
class Test_UpgradeTable extends TestCase {

	public function testShouldSetTableNotReady() {
		$upgrader = $this->createMocks( false );

		$upgrader->upgrade_table();
	}

	public function testShouldSetTableReady() {
		$upgrader = $this->createMocks( true );

		$upgrader->upgrade_table();
	}

	public function createMocks( $upgraded ) {
		$table = $this->createMock( Table::class );
		$table
			->expects( $this->once() )
			->method( 'create' )
			->with( [ 'logger' => 'custom_logger' ] )
			->willReturn( $upgraded );

		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'set_table_not_ready', 'set_table_ready', 'update_db_version' ] )
			->setConstructorArgs(
				[
					$table,
					[
						'logger' => 'custom_logger',
						'foo'    => 'bar',
					]
				]
			)
			->getMock();

		$upgrader
			->expects( ! $upgraded ? $this->once() : $this->never() )
			->method( 'set_table_not_ready' );
		$upgrader
			->expects( ! $upgraded ? $this->never() : $this->once() )
			->method( 'set_table_ready' );
		$upgrader
			->expects( ! $upgraded ? $this->never() : $this->once() )
			->method( 'update_db_version' );

		return $upgrader;
	}
}
