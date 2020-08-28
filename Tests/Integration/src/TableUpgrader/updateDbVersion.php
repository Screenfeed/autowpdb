<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->update_db_version().
 *
 * @covers TableUpgrader::update_db_version
 * @group  TableUpgrader
 */
class Test_UpdateDbVersion extends TestCase {

	public function testShouldUpdateVersion() {
		$this->init();

		$expected = $this->table_def->get_table_version();

		$this->deleteDbVersion();

		$this->invokeMethod( 'update_db_version', $this->upgrader );

		$this->assertSame( $expected, $this->upgrader->get_db_version() );

		$this->deleteDbVersion();
	}
}
