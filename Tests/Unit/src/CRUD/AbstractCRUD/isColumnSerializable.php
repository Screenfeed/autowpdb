<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::is_column_serializable().
 *
 * @covers AbstractTableDefinition::is_column_serializable
 * @group  AbstractTableDefinition
 */
class Test_IsColumnSerializable extends TestCase {

	public function testShouldReturnFalse() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->any() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => '', 'bar' => 0 ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'is_column_serializable', [ 'foo' ] );

		$this->assertFalse( $result );

		$result = $this->invokeMethod( $table, 'is_column_serializable', [ 'bar' ] );

		$this->assertFalse( $result );
	}

	public function testShouldReturnTrue() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->any() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => [], 'bar' => (object) [] ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'is_column_serializable', [ 'foo' ] );

		$this->assertTrue( $result );

		$result = $this->invokeMethod( $table, 'is_column_serializable', [ 'bar' ] );

		$this->assertTrue( $result );
	}
}
