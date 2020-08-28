<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->set_table_not_ready().
 *
 * @covers TableUpgrader::set_table_not_ready
 * @group  TableUpgrader
 */
class Test_SetTableNotReady extends TestCase {

	public function testShouldSetTableReady() {
		$this->init();

		$this->invokeMethod( 'set_table_ready', $this->upgrader );

		$this->assertTableIsReady();

		$this->invokeMethod( 'set_table_not_ready', $this->upgrader );

		$this->assertTableIsNotReady();
	}
}
