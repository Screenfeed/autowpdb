<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\CRUD\CustomCRUD;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for AbstractTableDefinition::get_table_definition().
 *
 * @covers AbstractTableDefinition::get_table_definition
 * @group  AbstractTableDefinition
 */
class Test_GetTableDefinition extends TestCase {

	public function testShouldReturnFullTableName() {
		$definition = new CustomTable();
		$table      = new CustomCRUD( $definition );

		$result = $table->get_table_definition();

		$this->assertSame( $definition, $result );
	}
}
