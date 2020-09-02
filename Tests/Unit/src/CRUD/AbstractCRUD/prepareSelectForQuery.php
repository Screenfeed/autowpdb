<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::prepare_select_for_query().
 *
 * @covers AbstractTableDefinition::prepare_select_for_query
 * @group  AbstractTableDefinition
 */
class Test_PrepareSelectForQuery extends TestCase {

	public function testShouldReturnNullWhenEmptyArgs() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'prepare_select_for_query', [ [] ] );

		$this->assertNull( $result );
	}

	public function testShouldReturnStarWhenArgsIsStar() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'prepare_select_for_query', [ [ 'foo' => '*' ] ] );

		$this->assertSame( '*', $result );
	}

	public function testShouldReturnNullWhenOnlyInvalidColumnsAreProvided() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%s', 'bar' => '%d' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'prepare_select_for_query', [ [ 'baz' ] ] );

		$this->assertNull( $result );
	}

	public function testShouldReturnImplodedList() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%s', 'bar' => '%d' ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'prepare_select_for_query', [ [ " \n\t\"'` BAR \n\t\"'` ", 'FOO', 'baz' ] ] );

		$this->assertSame( '`bar`,`foo`', $result );
	}
}
