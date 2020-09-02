<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;
use stdClass;

/**
 * Tests for AbstractTableDefinition::cast_col().
 *
 * @covers AbstractTableDefinition::cast_col
 * @group  AbstractTableDefinition
 */
class Test_CastCol extends TestCase {

	public function testShouldReturnNull() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'cast_col', [ [], 'whatever' ] );
		$this->assertNull( $result );

		$result = $this->invokeMethod( $table, 'cast_col', [ new stdClass(), 'whatever' ] );
		$this->assertNull( $result );

		$result = $this->invokeMethod( $table, 'cast_col', [ 'not iterable', 'whatever' ] );
		$this->assertNull( $result );
	}

	public function testShouldCastValues() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->any() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%d' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$values   = [ 12, '15', 14.4, 'uh' ];
		$expected = [ 12, 15, 14, 0 ];
		$result   = $this->invokeMethod( $table, 'cast_col', [ $values, 'foo' ] );

		$this->assertSame( $expected, $result );

		$values   = (object) $values;
		$expected = (object) $expected;
		$result   = $this->invokeMethod( $table, 'cast_col', [ $values, 'foo' ] );

		$this->assertEqualsCanonicalizing( $expected, $result );
	}
}
