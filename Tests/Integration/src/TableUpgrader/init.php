<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->init().
 *
 * @covers TableUpgrader::init
 * @group  TableUpgrader
 */
class Test_Init extends TestCase {

	public function testShouldSetTableReadyAndAddCustomHook() {
		$hook_name = 'foobar';
		$hook_prio = 5;

		$this->init(
			[
				'upgrade_hook'      => $hook_name,
				'upgrade_hook_prio' => $hook_prio,
			]
		);
		$this->insertVersionInDb();

		$this->upgrader->init();

		$this->assertTableIsReady();
		$this->assertDbVersionIs();
		$this->assertSame( $hook_prio, has_action( $hook_name, [ $this->upgrader, 'maybe_upgrade_table' ] ) );
		$this->reset();
	}

	public function testShouldSetTableReadyAndAddDefaultHook() {
		$this->init();
		$this->insertVersionInDb();

		$this->upgrader->init();

		$this->assertTableIsReady();
		$this->assertDbVersionIs();
		$this->assertSame( 8, has_action( 'admin_menu', [ $this->upgrader, 'maybe_upgrade_table' ] ) );
		$this->reset();
	}

	public function testShouldNotSetTableReadyNorAddHook() {
		$this->init(
			[
				'upgrade_hook' => false,
			]
		);
		$this->deleteDbVersion();

		$this->upgrader->init();

		$this->assertTableIsNotReady();
		$this->assertDbVersionIs( 0 );
		$this->assertFalse( has_action( 'admin_menu', [ $this->upgrader, 'maybe_upgrade_table' ] ) );
		$this->reset();
	}

	private function reset() {
		$this->deleteDbVersion();
		$this->resetTableReady();
	}
}
