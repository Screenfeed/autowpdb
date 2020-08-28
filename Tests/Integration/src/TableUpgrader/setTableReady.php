<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->set_table_ready().
 *
 * @covers TableUpgrader::set_table_ready
 * @group  TableUpgrader
 */
class Test_SetTableReady extends TestCase {

	public function testShouldSetTableReady() {
		$this->init();

		$this->assertTableIsNotReady();

		$this->invokeMethod( $this->upgrader, 'set_table_ready' );

		$this->assertTableIsReady();

		$this->resetTableReady();
	}
}
