<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::get_placeholders().
 *
 * @covers AbstractTableDefinition::get_placeholders
 * @group  AbstractTableDefinition
 */
class Test_GetPlaceholders extends TestCase {

	public function testShouldReturnPlaceholders() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%d', 'bar' => '%s' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_placeholders', [ [ 'bar' => 'babar', 'baz' => 'babaz' ] ] );

		$this->assertIsArray( $result );
		$this->assertSame( [ 'bar' => '%s' ], $result );
	}
}
