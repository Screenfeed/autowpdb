<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\DBWorker\WorkerInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBWorker\Worker\WorkerIntegration as Worker;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for AbstractTableDefinition->get_table_worker().
 *
 * @covers AbstractTableDefinition::get_table_worker
 * @group  AbstractTableDefinition
 */
class Test_GetTableWorker extends TestCase {

	public function testShouldReturnTableWorker() {
		$table  = new CustomTable();
		$result = $table->get_table_worker();

		$this->assertInstanceOf( WorkerInterface::class, $result );

		$worker = new Worker();
		$table  = new CustomTable( $worker );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );
	}
}
