<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBWorker\Worker\WorkerIntegration as Worker;

/**
 * Tests for Worker->delete_table().
 *
 * @covers Worker::delete_table
 * @group  Worker
 */
class Test_DeleteTable extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnTrue() {
		$this->create_table();

		$result = ( new Worker() )->delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertTrue( $result );
		$this->assertCount( 0, $this->get_logs() );
	}

	public function testShouldReturnFalse() {
		$error = "Deletion of the DB table {$this->table_name} failed.";

		$result = ( new Worker() )->delete_table(
			$this->table_name,
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

		$result = ( new Worker() )->delete_table(
			$this->table_name,
			[
				'logger' => [ $this, 'log' ],
			]
		);

		$this->assertFalse( $result );
		$this->assertCount( 0, $this->get_logs() );
	}
}
