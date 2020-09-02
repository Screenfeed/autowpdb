<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::prepare_data_for_query().
 *
 * @covers AbstractTableDefinition::prepare_data_for_query
 * @group  AbstractTableDefinition
 */
class Test_PrepareDataForQuery extends TestCase {

	public function testShouldReturnEmptyArrayWhenEmptyArgs() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'prepare_data_for_query', [ [] ] );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	public function testShouldReturnEmptyArrayWhenOnlyInvalidColumnsAreProvided() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%s', 'bar' => '%d' ] );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => '', 'bar' => 0 ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'prepare_data_for_query', [ [ 'baz' => 'babaz' ] ] );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	public function testShouldReturnPreparedList() {
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
			->method( 'get_column_placeholders' )
			->willReturn( [ 'foo' => '%s', 'bar' => '%d' ] );
		$definition
			->expects( $this->once() )
			->method( 'get_column_defaults' )
			->willReturn( [ 'foo' => [], 'bar' => 0 ] );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result   = $this->invokeMethod( $table, 'prepare_data_for_query', [ [ 'BAR' => 7, 'FOO' => [ 'foofoo' ], 'baz' => 'babaz' ] ] );
		$expected = [
			'bar' => 7,
			'foo' => 'a:1:{i:0;s:6:"foofoo";}',
		];

		$this->assertSame( $expected, $result );
	}
}
