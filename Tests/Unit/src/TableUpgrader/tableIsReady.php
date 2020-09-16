<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::table_is_ready().
 *
 * @covers TableUpgrader::table_is_ready
 * @group  TableUpgrader
 */
class Test_TableIsReady extends TestCase {

	public function testShouldReturnTrue() {
		$table    = $this->createMock( Table::class );
		$upgrader = new TableUpgrader( $table );

		$this->setPropertyValue( $upgrader, 'table_ready', true );

		$this->assertTrue( $upgrader->table_is_ready() );
	}

	public function testShouldReturnFalse() {
		$table    = $this->createMock( Table::class );
		$upgrader = new TableUpgrader( $table );

		$this->setPropertyValue( $upgrader, 'table_ready', false );

		$this->assertFalse( $upgrader->table_is_ready() );
	}
}
