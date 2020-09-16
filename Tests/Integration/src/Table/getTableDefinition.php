<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for Table->get_table_definition().
 *
 * @covers Table::get_table_definition
 * @group  Table
 */
class Test_GetTableDefinition extends TestCase {

	public function testShouldReturnTableDefinitionInstance() {
		$table_definition = new CustomTable();

		$table  = new Table( $table_definition );
		$result = $table->get_table_definition();

		$this->assertSame( $table_definition, $result );
	}
}
