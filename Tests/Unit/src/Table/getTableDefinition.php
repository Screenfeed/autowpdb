<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->get_table_definition().
 *
 * @covers Table::get_table_definition
 * @group  Table
 */
class Test_GetTableDefinition extends TestCase {

	public function testShouldReturnTableDefinitionInstance() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );

		$table  = new Table( $table_definition );
		$result = $table->get_table_definition();

		$this->assertSame( $table_definition, $result );
	}
}
