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

		$this->invokeMethod( $this->upgrader, 'set_table_ready' );

		$this->assertTableIsReady();

		$this->invokeMethod( $this->upgrader, 'set_table_not_ready' );

		$this->assertTableIsNotReady();
	}
}
