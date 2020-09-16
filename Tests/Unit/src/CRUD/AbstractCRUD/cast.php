<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;
use stdClass;

/**
 * Tests for AbstractTableDefinition::cast().
 *
 * @covers AbstractTableDefinition::cast
 * @group  AbstractTableDefinition
 */
class Test_Cast extends TestCase {
	private static $placeholders = [
		'foo' => '%s',
		'bar' => '%d',
		'baz' => '%f',
		'lor' => '%s',
		'ips' => '%s',
	];
	private static $default_values = [
		'foo' => 'foofoo',
		'bar' => 0,
		'baz' => 0.0,
		'lor' => [],
		'ips' => [],
	];

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$default_values['ips'] = (object) [];
	}

	public function testShouldCastValue() {
		Functions\expect( 'maybe_unserialize' )
			->times( 4 )
			->andReturnUsing(
				function ( $data ) {
					if ( is_string( $data ) ) {
						return @unserialize( trim( $data ) );
					}
					return $data;
				}
			);

		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->any() )
			->method( 'get_column_placeholders' )
			->willReturn( static::$placeholders );
		$definition
			->expects( $this->any() )
			->method( 'get_column_defaults' )
			->willReturn( static::$default_values );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		// Int.
		$result = $this->invokeMethod( $table, 'cast', [ 12, 'bar' ] );

		$this->assertSame( 12, $result );

		$result = $this->invokeMethod( $table, 'cast', [ '12', 'bar' ] );

		$this->assertSame( 12, $result );

		// Float.
		$result = $this->invokeMethod( $table, 'cast', [ 12.2, 'baz' ] );

		$this->assertSame( 12.2, $result );

		$result = $this->invokeMethod( $table, 'cast', [ '12.2', 'baz' ] );

		$this->assertSame( 12.2, $result );

		// Array.
		$result = $this->invokeMethod( $table, 'cast', [ [ 'ha', 'ho' ], 'lor' ] );

		$this->assertSame( [ 'ha', 'ho' ], $result );

		$result = $this->invokeMethod( $table, 'cast', [ 'a:1:{i:0;s:6:"foofoo";}', 'lor' ] );

		$this->assertSame( [ 'foofoo' ], $result );

		$result = $this->invokeMethod( $table, 'cast', [ 0, 'lor' ] );

		$this->assertSame( [], $result );

		// Object.
		$value  = (object) [ 'ha', 'ho' ];
		$result = $this->invokeMethod( $table, 'cast', [ $value, 'ips' ] );

		$this->assertSame( $value, $result );

		$expected = (object) [ 'foo' => 'foofoo' ];
		$value    = serialize( $expected );
		$result   = $this->invokeMethod( $table, 'cast', [ $value, 'ips' ] );

		$this->assertIsObject( $result );
		$this->assertInstanceOf( stdClass::class, $result );
		$this->assertEqualsCanonicalizing( $expected, $result );

		$result = $this->invokeMethod( $table, 'cast', [ 0, 'ips' ] );

		$this->assertIsObject( $result );
		$this->assertInstanceOf( stdClass::class, $result );
		$this->assertEqualsCanonicalizing( (object) [], $result );

		// String.
		$result = $this->invokeMethod( $table, 'cast', [ 'value', 'foo' ] );

		$this->assertSame( 'value', $result );

		$result = $this->invokeMethod( $table, 'cast', [ '14', 'foo' ] );

		$this->assertSame( '14', $result );

		$result = $this->invokeMethod( $table, 'cast', [ 14, 'foo' ] );

		$this->assertSame( '14', $result );

		$result = $this->invokeMethod( $table, 'cast', [ -14.4, 'foo' ] );

		$this->assertSame( '-14.4', $result );
	}
}
