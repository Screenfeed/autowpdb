<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::get_default_value().
 *
 * @covers AbstractTableDefinition::get_default_value
 * @group  AbstractTableDefinition
 */
class Test_GetDefaultValue extends TestCase {

	public function testShouldReturnNullWhenUnknownColumn() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => 1, 'bar' => '' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_default_value', [ 'baz' ] );

		$this->assertNull( $result );
	}

	public function testShouldReturnValue() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => 1, 'bar' => '' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_default_value', [ 'foo' ] );

		$this->assertSame( 1, $result );
	}
}
