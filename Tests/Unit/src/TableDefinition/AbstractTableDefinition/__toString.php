<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableDefinition\AbstractTableDefinition;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\TableDefinition\AbstractTableDefinition;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for AbstractTableDefinition::__toString().
 *
 * @covers AbstractTableDefinition::__toString
 * @group  AbstractTableDefinition
 */
class Test___toString extends TestCase {
	private $values = [
		'table_version'       => 1,
		'table_short_name'    => 'b',
		'table_name'          => 'c',
		'table_is_global'     => false,
		'primary_key'         => 'e',
		'column_placeholders' => [ '%s' ],
		'column_defaults'     => [ 'g' ],
		'table_schema'        => 'h',
	];

	public function testShouldReturnString() {
		$table = $this->createMocks();

		$result = $table->__toString();

		$this->assertSame( json_encode( $this->values ), $result );

		$table = $this->createMocks( true );

		$result = $table->__toString();

		$this->assertSame( '', $result );
	}

	public function createMocks( $error = false ) {
		Functions\when( 'wp_json_encode' )->alias(
			function( $value ) use ( $error ) {
				if ( $error ) {
					return false;
				}
				return json_encode( $value );
			}
		);

		$table = $this->getMockBuilder( AbstractTableDefinition::class )
			->disableOriginalConstructor()
			->setMethods( [ 'jsonSerialize' ] )
			->getMockForAbstractClass();
		$table
			->expects( $this->any() )
			->method( 'jsonSerialize' )
			->with()
			->willReturn( $this->values );

		return $table;
	}
}
