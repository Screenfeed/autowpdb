<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBUtilities;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->get_table_worker().
 *
 * @covers Table::get_table_worker
 * @group  Table
 */
class Test_GetTableWorker extends TestCase {

	public function testShouldReturnTableWorker() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );

		$worker = DBUtilities::class;
		$table  = new Table( $table_definition );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );

		$worker = DBUtilitiesUnit::class;
		$table  = new Table( $table_definition, $worker );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );
	}
}
