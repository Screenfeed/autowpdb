<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::serialize_columns().
 *
 * @covers AbstractTableDefinition::serialize_columns
 * @group  AbstractTableDefinition
 */
class Test_SerializeColumns extends TestCase {

	public function testShouldNotSerializeWhenNoSerializableColumns() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => '', 'bar' => 0 ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$columns = [ 'foo' => 'foofoo', 'bar' => 14 ];
		$result  = $this->invokeMethod( $table, 'serialize_columns', [ $columns ] );

		$this->assertSame( $columns, $result );
	}

	public function testShouldNotSerializeWhenNoSerializableColumnsInArg() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => '', 'bar' => 0, 'baz' => [] ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$columns = [ 'foo' => 'foofoo', 'bar' => 14 ];
		$result  = $this->invokeMethod( $table, 'serialize_columns', [ $columns ] );

		$this->assertSame( $columns, $result );
	}

	public function testShouldNotSerializeEmptyValues() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => '', 'bar' => 0, 'baz' => [] ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$expected = [ 'foo' => 'foofoo', 'bar' => 14, 'baz' => null ];
		$columns  = [ 'foo' => 'foofoo', 'bar' => 14, 'baz' => [] ];
		$result   = $this->invokeMethod( $table, 'serialize_columns', [ $columns ] );

		$this->assertSame( $expected, $result );

		$columns  = [ 'foo' => 'foofoo', 'bar' => 14, 'baz' => false ];
		$result   = $this->invokeMethod( $table, 'serialize_columns', [ $columns ] );

		$this->assertSame( $expected, $result );

		$columns  = [ 'foo' => 'foofoo', 'bar' => 14, 'baz' => 0 ];
		$result   = $this->invokeMethod( $table, 'serialize_columns', [ $columns ] );

		$this->assertSame( $expected, $result );
	}

	public function testShouldSerialize() {
		Functions\expect( 'maybe_serialize' )
			->once()
			->with( Mockery::type( 'array' ) )
			->andReturnUsing(
				function ( $data ) {
					if ( is_array( $data ) || is_object( $data ) ) {
						return serialize( $data );
					}
					return $data;
				}
			);

		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => '', 'bar' => 0, 'baz' => [] ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$expected = [ 'foo' => 'foofoo', 'bar' => 14, 'baz' => 'a:1:{i:0;s:6:"bazbaz";}' ];
		$columns  = [ 'foo' => 'foofoo', 'bar' => 14, 'baz' => [ 'bazbaz' ] ];
		$result   = $this->invokeMethod( $table, 'serialize_columns', [ $columns ] );
	}
}
