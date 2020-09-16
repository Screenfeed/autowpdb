<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->table_is_ready().
 *
 * @covers TableUpgrader::table_is_ready
 * @group  TableUpgrader
 */
class Test_TableIsReady extends TestCase {

	public function testShouldReturnTrue() {
		$this->init();

		$this->invokeMethod( $this->upgrader, 'set_table_ready' );

		$this->assertTrue( $this->upgrader->table_is_ready() );

		$this->resetTableReady();
	}

	public function testShouldReturnFalse() {
		$this->init();

		$this->assertFalse( $this->upgrader->table_is_ready() );

		$this->resetTableReady();
	}
}
