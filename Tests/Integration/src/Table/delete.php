<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBWorker\Worker\WorkerIntegration as Worker;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\src\Table\TestCase;

/**
 * Tests for Table->delete().
 *
 * @covers Table::delete
 * @group  Table
 */
class Test_Delete extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnTrue() {
		$this->createTemporaryTable();

		$table  = new Table( new CustomTable( new Worker() ) );
		$result = $table->delete(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function testShouldReturnFalse() {
		$error = "Deletion of the DB table {$this->table_name} failed.";

		$table  = new Table( new CustomTable( new Worker() ) );
		$result = $table->delete(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 1, $this->get_logs() );
		$this->assertContains( $error, $this->get_logs() );
	}

	public function testShouldFailWithoutLogging() {
		remove_filter( 'screenfeed_autowpdb_can_log', [ $this, 'return_true' ] );

		$table  = new Table( new CustomTable( new Worker() ) );
		$result = $table->delete(
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	private function createTemporaryTable() {
		global $wpdb;

		$schema          = 'file_id bigint(20) unsigned NOT NULL default 0';
		$charset_collate = $wpdb->get_charset_collate();

		$wpdb->query( "CREATE TEMPORARY TABLE `{$this->table_name}` ($schema) $charset_collate" );
	}
}
