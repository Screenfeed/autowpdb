<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use ArrayIterator;
use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;
use stdClass;
use Traversable;

/**
 * Tests for AbstractTableDefinition::is_iterable().
 *
 * @covers AbstractTableDefinition::is_iterable
 * @group  AbstractTableDefinition
 */
class Test_IsIterable extends TestCase {

	public function testShouldReturnFalse() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'is_iterable', [ 5 ] );

		$this->assertFalse( $result );

		$result = $this->invokeMethod( $table, 'is_iterable', [ 'bar' ] );

		$this->assertFalse( $result );
	}

	public function testShouldReturnTrue() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );
		$values     = [
			[],
			[ 'foo' ],
			new ArrayIterator( [ 1, 2, 3 ] ),
			new stdClass(),
			(object) [],
			$this->getMockBuilder( [ Traversable::class ] )->getMock(),
		];

		foreach ( $values as $value ) {
			$result = $this->invokeMethod( $table, 'is_iterable', [ $value ] );

			$this->assertTrue( $result );
		}
	}
}
