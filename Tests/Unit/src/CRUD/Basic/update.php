<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\CRUD\Basic;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic\TestCase;
use wpdb;

/**
 * Tests for Basic::update().
 *
 * @covers Basic::update
 * @group  Basic
 */
class Test_Update extends TestCase {

	public function testShouldReturnFalseWhenNoData() {
		$table  = new Basic( new CustomTable() );
		$result = $table->update( [], [ 'foo' => 'bar' ] );

		$this->assertFalse( $result );
	}

	public function testShouldReturnNumberOfDeletedRows() {
		global $wpdb;

		$data               = [
			'file_id'   => 12,
			'file_size' => 200,
			'data'      => [
				'bar' => 'baz',
			],
			'unknown'   => 'foobar',
		];
		$prepared_data      = [
			'file_size' => 200,
			'data'      => 'a:1:{s:3:"bar";s:3:"baz";}',
		];
		$data_placeholders  = [
			'file_size' => '%d',
			'data'      => '%s',
		];
		$where              = [
			'mime_type' => 'image/jpeg',
			'file_size' => 0,
			'data'      => [
				'foo' => 'bar',
			],
			'unknown'   => 'foobar',
		];
		$prepared_where     = [
			'mime_type' => 'image/jpeg',
			'file_size' => 0,
			'data'      => 'a:1:{s:3:"foo";s:3:"bar";}',
		];
		$where_placeholders = [
			'mime_type' => '%s',
			'file_size' => '%d',
			'data'      => '%s',
		];

		Functions\expect( 'maybe_serialize' )
			->twice()
			->with( Mockery::type( 'array' ) )
			->andReturnUsing(
				function ( $data ) {
					if ( is_array( $data ) || is_object( $data ) ) {
						return serialize( $data );
					}
					return $data;
				}
			);

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'update' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'update' )
			->with( $this->table_name, $prepared_data, $prepared_where, $data_placeholders, $where_placeholders )
			->willReturn( 2 );

		$definition = $this->getMockBuilder( CustomTable::class )
			->setMethods( [ 'get_table_name' ] )
			->getMock();
		$definition
			->expects( $this->any() )
			->method( 'get_table_name' )
			->willReturn( $this->table_name );
		$table      = new Basic( $definition );

		$result = $table->update( $data, $where );

		$this->assertSame( 2, $result );
	}
}
