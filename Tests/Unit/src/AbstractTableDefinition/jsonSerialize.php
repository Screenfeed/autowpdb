<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\TableDefinition\AbstractTableDefinition;

/**
 * Tests for AbstractTableDefinition::jsonSerialize().
 *
 * @covers AbstractTableDefinition::jsonSerialize
 * @group  AbstractTableDefinition
 */
class Test_JsonSerialize extends TestCase {
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
	private $methods = [
		'get_table_version'       => 1,
		'get_table_short_name'    => 'b',
		'get_table_name'          => 'c',
		'is_table_global'         => false,
		'get_primary_key'         => 'e',
		'get_column_placeholders' => [ '%s' ],
		'get_column_defaults'     => [ 'g' ],
		'get_table_schema'        => 'h',
	];

	public function testShouldReturnArray() {
		$table = $this->createMocks();

		$result = $table->jsonSerialize();

		$this->assertSame( $this->values, $result );
	}

	public function createMocks() {
		$table = $this->getMockBuilder( AbstractTableDefinition::class )
			->disableOriginalConstructor()
			->setMethods( array_keys( $this->methods ) )
			->getMockForAbstractClass();

		foreach ( $this->methods as $method => $value ) {
			$table
				->expects( $this->once() )
				->method( $method )
				->with()
				->willReturn( $value );
		}

		return $table;
	}
}
