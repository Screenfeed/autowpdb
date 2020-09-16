<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\CRUD\Basic;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic\TestCase;
use wpdb;

/**
 * Tests for Basic::delete().
 *
 * @covers Basic::delete
 * @group  Basic
 */
class Test_Delete extends TestCase {

	public function testShouldReturnNumberOfDeletedRows() {
		global $wpdb;

		$where          = [
			'mime_type' => 'image/jpeg',
			'file_size' => 0,
			'data'      => [
				'foo' => 'bar',
			],
			'unknown'   => 'foobar',
		];
		$prepared_where = [
			'mime_type' => 'image/jpeg',
			'file_size' => 0,
			'data'      => 'a:1:{s:3:"foo";s:3:"bar";}',
		];
		$placeholders   = [
			'mime_type' => '%s',
			'file_size' => '%d',
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
			->setMethods( [ 'delete' ] )
			->getMock();
		$wpdb
			->expects( $this->once() )
			->method( 'delete' )
			->with( $this->table_name, $prepared_where, $placeholders )
			->willReturn( 2 );

		$definition = $this->getMockBuilder( CustomTable::class )
			->setMethods( [ 'get_table_name' ] )
			->getMock();
		$definition
			->expects( $this->any() )
			->method( 'get_table_name' )
			->willReturn( $this->table_name );
		$table      = new Basic( $definition );

		$result = $table->delete( $where );

		$this->assertSame( 2, $result );
	}
}
