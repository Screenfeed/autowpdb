<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->get_db_version().
 *
 * @covers TableUpgrader::get_db_version
 * @group  TableUpgrader
 */
class Test_GetDbVersion extends TestCase {

	public function testShouldReturnVersion() {
		$this->init();

		$this->insertVersionInDb( 106 );

		$this->assertSame( 106, $this->upgrader->get_db_version() );

		$this->deleteDbVersion();
	}
}
