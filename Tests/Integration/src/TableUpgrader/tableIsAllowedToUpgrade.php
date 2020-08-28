<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

use Screenfeed\AutoWPDB\TableUpgrader;

/**
 * Tests for TableUpgrader->table_is_allowed_to_upgrade().
 *
 * @covers TableUpgrader::table_is_allowed_to_upgrade
 * @group  TableUpgrader
 */
class Test_TableIsAllowedToUpgrade extends TestCase {

	public function testShouldReturnTrue() {
		$this->init();

		// No version registered in the DB, table is global, and not allowed to upgrade.
		$this->deleteDbVersion();
		add_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_false' ] );

		$this->assertTrue( $this->upgrader->table_is_allowed_to_upgrade() );

		// Version registered in the DB, table is global, and allowed to upgrade.
		$this->insertVersionInDb();
		remove_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_false' ] );
		add_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_true' ] );

		$this->assertTrue( $this->upgrader->table_is_allowed_to_upgrade() );

		// Version registered in the DB, table is not global, and not allowed to upgrade.
		$this->init( [], [ 'table_is_global' => false ] );
		$this->insertVersionInDb();
		remove_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_true' ] );
		add_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_false' ] );

		$this->assertTrue( $this->upgrader->table_is_allowed_to_upgrade() );

		remove_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_false' ] );
	}

	public function testShouldReturnFalse() {
		// Version registered in the DB, table is global, and not allowed to upgrade.
		$this->init();
		$this->insertVersionInDb();
		add_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_false' ] );

		$this->assertFalse( $this->upgrader->table_is_allowed_to_upgrade() );

		remove_filter( 'wp_should_upgrade_global_tables', [ $this, 'return_false' ] );
	}

	public function testShouldReturnBoolean() {
		$this->init();

		$func = function() {
			return '0';
		};
		add_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', $func );

		$this->assertFalse( $this->upgrader->table_is_allowed_to_upgrade() );

		remove_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', $func );

		$func = function() {
			return 0;
		};
		add_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', $func );

		$this->assertFalse( $this->upgrader->table_is_allowed_to_upgrade() );

		remove_filter( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', $func );
	}
}
