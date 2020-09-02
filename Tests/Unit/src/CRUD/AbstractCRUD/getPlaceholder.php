<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::get_placeholder().
 *
 * @covers AbstractTableDefinition::get_placeholder
 * @group  AbstractTableDefinition
 */
class Test_GetPlaceholder extends TestCase {

	public function testShouldReturnDefaultPlaceholderWhenUnknownColumn() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%d', 'bar' => '%s' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_placeholder', [ 'baz' ] );

		$this->assertSame( '%s', $result );
	}

	public function testShouldReturnPlaceholder() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%d', 'bar' => '%s' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_placeholder', [ 'foo' ] );

		$this->assertSame( '%d', $result );
	}
}
