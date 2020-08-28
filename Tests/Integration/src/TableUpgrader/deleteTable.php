<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->delete_table().
 *
 * @covers TableUpgrader::delete_table
 * @group  TableUpgrader
 */
class Test_DeleteTable extends TestCase {
	protected $drop_table = true;

	public function testShouldNotSetTableNotReady() {
		$this->init(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		// Set the table ready without creating it.
		$this->invokeMethod( 'set_table_ready', $this->upgrader );
		$this->invokeMethod( 'update_db_version', $this->upgrader );

		$this->upgrader->delete_table();

		// The table status should still be "ready".
		$this->assertTableIsReady();
		$this->assertDbVersionIs();

		// We should have an error message.
		$error = "Deletion of the DB table {$this->table_name} failed.";
		$this->assertCount( 1, $this->get_logs() );

		foreach ( $this->get_logs() as $log ) {
			$this->assertStringStartsWith( $error, $log );
		}

		$this->reset();
	}

	public function testShouldSetTableNotReady() {
		$this->init();

		$this->upgrader->upgrade_table();

		$this->upgrader->delete_table();

		$this->assertTableIsNotReady();
		$this->assertDbVersionIs( 0 );

		$this->reset();
	}

	private function reset() {
		$this->deleteDbVersion();
		$this->resetTableReady();
	}
}
