<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->maybe_upgrade_table().
 *
 * @covers TableUpgrader::maybe_upgrade_table
 * @group  TableUpgrader
 */
class Test_MaybeUpgradeTable extends TestCase {
	protected $drop_table = true;

	public function testShouldSetReadyWhenUpToDate() {
		$this->init();
		$this->reset();
		$this->insertVersionInDb();
		$this->upgrader->maybe_upgrade_table();
		$this->assertTableIsReady();
		$this->assertDbVersionIs();
		$this->reset();
	}

	public function testShouldSetNotReadyWhenNotUpToDateAndDowngradeNotAllowed() {
		$this->init();
		$this->reset();
		add_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', [ $this, 'return_false' ] );
		$this->upgrader->maybe_upgrade_table();
		$this->assertTableIsNotReady();
		$this->assertDbVersionIs( 0 );
		$this->reset();
	}

	public function testShouldLaunchUpgrade() {
		$this->init();
		$this->reset();
		add_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', [ $this, 'return_true' ] );
		$this->upgrader->maybe_upgrade_table();
		$this->assertTableIsReady();
		$this->assertDbVersionIs();
		$this->assertSame( $this->table_def->get_table_version(), $this->upgrader->get_db_version() );
		$this->reset();
	}

	private function reset() {
		$this->deleteDbVersion();
		$this->resetTableReady();

		remove_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', [ $this, 'return_false' ] );
		remove_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', [ $this, 'return_true' ] );
	}
}
