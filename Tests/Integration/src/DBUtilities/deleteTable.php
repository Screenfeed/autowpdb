<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesIntegration as DBUtilities;

/**
 * Tests for DBUtilities::delete_table().
 *
 * @covers DBUtilities::delete_table
 * @group  DBUtilities
 */
class Test_DeleteTable extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnTrue() {
		$this->createTemporaryTable();

		$result = DBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->logs );
	}

	public function testShouldReturnFalse() {
		$error = "Deletion of the DB table {$this->table_name} failed.";

		$result = DBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 1, $this->logs );
		$this->assertContains( $error, $this->logs );
	}

	public function testShouldFailWithoutLogging() {
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );

		$result = DBUtilities::delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->logs );
	}

	private function createTemporaryTable() {
		global $wpdb;

		$schema          = 'file_id bigint(20) unsigned NOT NULL default 0';
		$charset_collate = $wpdb->get_charset_collate();

		$wpdb->query( "CREATE TEMPORARY TABLE `{$this->table_name}` ($schema) $charset_collate" );
	}
}
