<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;
use stdClass;

/**
 * Tests for AbstractTableDefinition::cast_row().
 *
 * @covers AbstractTableDefinition::cast_row
 * @group  AbstractTableDefinition
 */
class Test_CastRow extends TestCase {

	public function testShouldReturnNull() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'cast_row', [ [], 'whatever' ] );
		$this->assertNull( $result );

		$result = $this->invokeMethod( $table, 'cast_row', [ new stdClass(), 'whatever' ] );
		$this->assertNull( $result );

		$result = $this->invokeMethod( $table, 'cast_row', [ 'not iterable', 'whatever' ] );
		$this->assertNull( $result );
	}

	public function testShouldCastValues() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->any() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%d', 'bar' => '%d', 'baz' => '%d' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$values   = [ 'foo' => 12, 'bar' => '15', 'baz' => 14.4 ];
		$expected = [ 'foo' => 12, 'bar' => 15, 'baz' => 14 ];
		$result   = $this->invokeMethod( $table, 'cast_row', [ $values ] );

		$this->assertSame( $expected, $result );

		$values   = (object) $values;
		$expected = (object) $expected;
		$result   = $this->invokeMethod( $table, 'cast_row', [ $values ] );

		$this->assertEqualsCanonicalizing( $expected, $result );
	}
}
