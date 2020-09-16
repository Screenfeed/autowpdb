<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableUpgrader;

/**
 * Tests for TableUpgrader->table_is_up_to_date().
 *
 * @covers TableUpgrader::table_is_up_to_date
 * @group  TableUpgrader
 */
class Test_TableIsUpToDate extends TestCase {

	public function testShouldReturnTrue() {
		// Downgrade not allowed, versions are identical.
		$this->init(
			[
				'handle_downgrade' => false,
			]
		);

		$version = $this->table_def->get_table_version();

		$this->insertVersionInDb( $version );

		$this->assertTrue( $this->upgrader->table_is_up_to_date() );

		// Downgrade not allowed, installed version is newer (downgrade).
		$this->insertVersionInDb( $version + 2 );

		$this->assertTrue( $this->upgrader->table_is_up_to_date() );

		// Downgrade is allowed, versions are identical.
		$this->init(
			[
				'handle_downgrade' => true,
			]
		);

		$this->insertVersionInDb( $version );

		$this->assertTrue( $this->upgrader->table_is_up_to_date() );

		$this->deleteDbVersion();
	}

	public function testShouldReturnFalse() {
		// No version in DB.
		$this->init();

		$version = $this->table_def->get_table_version();

		$this->deleteDbVersion();

		$this->assertFalse( $this->upgrader->table_is_up_to_date() );

		// Downgrade not allowed, installed version is older (upgrade).
		$this->init(
			[
				'handle_downgrade' => false,
			]
		);

		$this->insertVersionInDb( $version - 1 );

		$this->assertFalse( $this->upgrader->table_is_up_to_date() );

		// Downgrade is allowed, installed version is older (upgrade).
		$this->init(
			[
				'handle_downgrade' => true,
			]
		);

		$this->insertVersionInDb( $version - 1 );

		$this->assertFalse( $this->upgrader->table_is_up_to_date() );

		// Downgrade is allowed, installed version is newer (downgrade).
		$this->init(
			[
				'handle_downgrade' => true,
			]
		);

		$this->insertVersionInDb( $version + 4 );

		$this->assertFalse( $this->upgrader->table_is_up_to_date() );

		$this->deleteDbVersion();
	}
}
