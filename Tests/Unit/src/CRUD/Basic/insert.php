<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\CRUD\Basic;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic\TestCase;
use wpdb;

/**
 * Tests for Basic::insert().
 *
 * @covers Basic::insert
 * @group  Basic
 */
class Test_Insert extends TestCase {

	public function testShouldReturnInsertId() {
		global $wpdb;

		$data           = [
			'file_id'   => 12,
			'PATH'      => '/foo/bar',
			'mime_type' => 'image/jpeg',
			'width'     => 600,
			'height'    => 400,
			'file_size' => 0,
			'data'      => [
				'foo' => 'bar',
			],
			'unknown'   => 'foobar',
		];
		$data_to_insert = [
			'file_date' => '0000-00-00 00:00:00',
			'path'      => '/foo/bar',
			'mime_type' => 'image/jpeg',
			'modified'  => 0,
			'width'     => 600,
			'height'    => 400,
			'file_size' => 0,
			'status'    => null,
			'error'     => null,
			'data'      => 'a:1:{s:3:"foo";s:3:"bar";}',
		];
		$placeholders   = [
			'file_date' => '%s',
			'path'      => '%s',
			'mime_type' => '%s',
			'modified'  => '%d',
			'width'     => '%d',
			'height'    => '%d',
			'file_size' => '%d',
			'status'    => '%s',
			'error'     => '%s',
			'data'      => '%s',
		];

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

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'insert' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'insert' )
			->with( $this->table_name, $data_to_insert, $placeholders )
			->willReturn( true );
		$wpdb->insert_id = '4';

		$definition = $this->getMockBuilder( CustomTable::class )
			->setMethods( [ 'get_table_name' ] )
			->getMock();
		$definition
			->expects( $this->any() )
			->method( 'get_table_name' )
			->willReturn( $this->table_name );
		$table      = new Basic( $definition );

		$result = $table->insert( $data );

		$this->assertSame( 4, $result );
	}
}
