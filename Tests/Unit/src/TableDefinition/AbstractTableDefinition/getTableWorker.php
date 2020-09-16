<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\DBWorker\WorkerInterface;
use Screenfeed\AutoWPDB\TableDefinition\AbstractTableDefinition;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for AbstractTableDefinition->get_table_worker().
 *
 * @covers AbstractTableDefinition::get_table_worker
 * @group  AbstractTableDefinition
 */
class Test_GetTableWorker extends TestCase {

	public function testShouldReturnTableWorker() {
		$table  = $this->getMockForAbstractClass( AbstractTableDefinition::class );
		$result = $table->get_table_worker();

		$this->assertInstanceOf( WorkerInterface::class, $result );

		$worker = new Worker();
		$table  = $this->getMockForAbstractClass( AbstractTableDefinition::class, [ $worker ] );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );
	}
}
