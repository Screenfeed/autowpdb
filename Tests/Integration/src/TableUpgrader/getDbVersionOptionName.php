<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->get_db_version_option_name().
 *
 * @covers TableUpgrader::get_db_version_option_name
 * @group  TableUpgrader
 */
class Test_GetDbVersionOptionName extends TestCase {

	public function testShouldReturnOptionName() {
		$this->init();

		$this->assertSame( 'foobar_db_version', $this->upgrader->get_db_version_option_name() );
	}
}
