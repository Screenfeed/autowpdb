<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::delete_table().
 *
 * @covers TableUpgrader::delete_table
 * @group  TableUpgrader
 */
class Test_DeleteTable extends TestCase {

	public function testShouldSetTableNotReady() {
		$upgrader = $this->createMocks( false );

		$upgrader->delete_table();
	}

	public function testShouldSetTableReady() {
		$upgrader = $this->createMocks( true );

		$upgrader->delete_table();
	}

	public function createMocks( $deleted ) {
		$table = $this->createMock( Table::class );
		$table
			->expects( $this->once() )
			->method( 'delete' )
			->with( [ 'logger' => 'custom_logger' ] )
			->willReturn( $deleted );

		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'set_table_not_ready', 'delete_db_version' ] )
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
			->expects( ! $deleted ? $this->never() : $this->once() )
			->method( 'set_table_not_ready' );
		$upgrader
			->expects( ! $deleted ? $this->never() : $this->once() )
			->method( 'delete_db_version' );

		return $upgrader;
	}
}
