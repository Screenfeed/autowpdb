<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::table_is_up_to_date().
 *
 * @covers TableUpgrader::table_is_up_to_date
 * @group  TableUpgrader
 */
class Test_TableIsUpToDate extends TestCase {

	public function testShouldReturnTrue() {
		// Downgrade not allowed, versions are identical.
		$upgrader = $this->createMocks(
			[
				'handle_downgrade' => false,
			],
			102
		);

		$this->assertTrue( $upgrader->table_is_up_to_date() );

		// Downgrade not allowed, installed version is newer (downgrade).
		$upgrader = $this->createMocks(
			[
				'handle_downgrade' => false,
			],
			104
		);

		$this->assertTrue( $upgrader->table_is_up_to_date() );

		// Downgrade is allowed, versions are identical.
		$upgrader = $this->createMocks(
			[
				'handle_downgrade' => true,
			],
			102
		);

		$this->assertTrue( $upgrader->table_is_up_to_date() );
	}

	public function testShouldReturnFalse() {
		// Downgrade not allowed, installed version is older (upgrade).
		$upgrader = $this->createMocks(
			[
				'handle_downgrade' => false,
			],
			101
		);

		$this->assertFalse( $upgrader->table_is_up_to_date() );

		// Downgrade is allowed, installed version is older (upgrade).
		$upgrader = $this->createMocks(
			[
				'handle_downgrade' => true,
			],
			101
		);

		$this->assertFalse( $upgrader->table_is_up_to_date() );

		// Downgrade is allowed, installed version is newer (downgrade).
		$upgrader = $this->createMocks(
			[
				'handle_downgrade' => true,
			],
			106
		);

		$this->assertFalse( $upgrader->table_is_up_to_date() );
	}

	public function createMocks( $args, $current_db_version ) {
		$table_def = $this->createMock( CustomTable::class );
		$table_def
			->expects( ! empty( $current_db_version ) ? $this->once() : $this->never() )
			->method( 'get_table_version' )
			->with()
			->willReturn( 102 );

		$table = $this->createMock( Table::class );
		$table
			->expects( ! empty( $current_db_version ) ? $this->once() : $this->never() )
			->method( 'get_table_definition' )
			->with()
			->willReturn( $table_def );

		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'get_db_version' ] )
			->setConstructorArgs( [ $table, $args ] )
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'get_db_version' )
			->with()
			->willReturn( $current_db_version );

		return $upgrader;
	}
}
