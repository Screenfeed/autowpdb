<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::get_table_definition().
 *
 * @covers AbstractTableDefinition::get_table_definition
 * @group  AbstractTableDefinition
 */
class Test_GetTableDefinition extends TestCase {

	public function testShouldReturnTableDefinition() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $table->get_table_definition();

		$this->assertSame( $definition, $result );
	}
}
