<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->delete_db_version().
 *
 * @covers TableUpgrader::delete_db_version
 * @group  TableUpgrader
 */
class Test_DeleteDbVersion extends TestCase {

	public function testShouldDeleteVersion() {
		$this->init();

		$this->insertVersionInDb( 106 );

		$this->assertSame( 106, $this->upgrader->get_db_version() );

		$this->invokeMethod( 'delete_db_version', $this->upgrader );

		$this->assertSame( 0, $this->upgrader->get_db_version() );
	}
}
