<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\DBUtilities;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesIntegration;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for Table->get_table_worker().
 *
 * @covers Table::get_table_worker
 * @group  Table
 */
class Test_GetTableWorker extends TestCase {

	public function testShouldReturnTableWorker() {
		$table_definition = new CustomTable();

		$worker = DBUtilities::class;
		$table  = new Table( $table_definition );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );

		$worker = DBUtilitiesIntegration::class;
		$table  = new Table( $table_definition, $worker );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );

		$table  = new Table( $table_definition, '\Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesIntegration' );
		$result = $table->get_table_worker();

		$this->assertSame( $worker, $result );
	}
}
