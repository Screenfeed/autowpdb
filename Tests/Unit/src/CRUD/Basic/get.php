<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic;

use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\CRUD\Basic;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic\TestCase;
use wpdb;

/**
 * Tests for Basic::get().
 *
 * @covers Basic::get
 * @group  Basic
 */
class Test_Get extends TestCase {
	private $prepare;
	private $values;
	private $prepared;
	private $output_type;
	private $raw_rows;
	private $rows;

	public function testShouldReturnNullWhenInvalidSelect() {
		$definition = new CustomTable();
		$table      = new Basic( $definition );

		$result = $table->get( [], [ 'file_id' => 2 ] );

		$this->assertNull( $result );

		$result = $table->get( [ ' ' ], [ 'file_id' => 2 ] );

		$this->assertNull( $result );

		$result = $table->get( [ 'unknown' ], [ 'file_id' => 2 ] );

		$this->assertNull( $result );
	}

	public function testShouldReturnNullWhenInvalidReturnType() {
		$this->prepared    = "SELECT `file_id`,`path`,`data` FROM `{$this->table_name}`";
		$this->output_type = 'unknown';
		$this->raw_rows    = null;

		$this->mockWpdb( false );

		Functions\expect( 'maybe_unserialize' )->never();

		$table  = $this->getTable();
		$result = $table->get( [ 'file_id', 'path', 'data' ], [], $this->output_type );

		$this->assertNull( $result );
	}

	public function testShouldReturnRowsWhenNoWhereClauses() {
		$this->prepared    = "SELECT `file_id`,`path`,`data` FROM `{$this->table_name}`";
		$this->output_type = OBJECT;

		$this->setDbResults();
		$this->mockWpdb( false );
		$this->mockUnserialize();

		$table  = $this->getTable();
		$result = $table->get( [ 'file_id', 'path', 'data' ], [] );

		$this->assertEqualsCanonicalizing( $this->rows, $result );
	}

	public function testShouldReturnRowsWhenWhereClauses() {
		$this->prepare     = "SELECT `file_id`,`path`,`data` FROM `{$this->table_name}` WHERE `mime_type` = %s AND `error` IS NULL";
		$this->values      = [ 'image/jpeg' ];
		$this->prepared    = "SELECT `file_id`,`path`,`data` FROM `{$this->table_name}` WHERE `mime_type` = 'image/jpeg' AND `error` IS NULL";
		$this->output_type = OBJECT;

		$this->setDbResults();
		$this->mockWpdb( true );
		$this->mockUnserialize();

		$table  = $this->getTable();
		$result = $table->get(
			[ 'file_id', 'path', 'data' ],
			[
				'mime_type' => 'image/jpeg',
				'error'     => null,
			]
		);

		$this->assertEqualsCanonicalizing( $this->rows, $result );
	}

	private function setDbResults() {
		$this->raw_rows = [
			(object) [
				'file_id'   => '2',
				'path'      => '/path/to/foobar.jpeg',
				'data'      => null,
			],
			(object) [
				'file_id'   => '12',
				'path'      => '/path/to/foobarbaz.jpeg',
				'data'      => 'a:1:{s:3:"foo";s:6:"foofoo";}',
			]
		];
		$this->rows     = [
			(object) [
				'file_id'   => 2,
				'path'      => '/path/to/foobar.jpeg',
				'data'      => [],
			],
			(object) [
				'file_id'   => 12,
				'path'      => '/path/to/foobarbaz.jpeg',
				'data'      => [ 'foo' => 'foofoo' ],
			]
		];
	}

	private function mockWpdb( $prepare ) {
		global $wpdb;

		$wpdb = $this->getMockBuilder( wpdb::class )
			->setMethods( [ 'prepare', 'get_results' ] )
			->getMock();

		if ( $prepare ) {
			$wpdb
				->expects( $this->once() )
				->method( 'prepare' )
				->with( $this->prepare, $this->values )
				->willReturn( $this->prepared );
		} else {
			$wpdb
				->expects( $this->never() )
				->method( 'prepare' );
		}

		$wpdb
			->expects( $this->once() )
			->method( 'get_results' )
			->with( $this->prepared, $this->output_type )
			->willReturn( $this->raw_rows );
	}

	private function mockUnserialize() {
		Functions\expect( 'maybe_unserialize' )
			->once()
			->andReturnUsing(
				function ( $data ) {
					if ( is_string( $data ) ) {
						return @unserialize( trim( $data ) );
					}
					return $data;
				}
			);
	}

	private function getTable() {
		$definition = $this->getMockBuilder( CustomTable::class )
			->setMethods( [ 'get_table_name' ] )
			->getMock();
		$definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( $this->table_name );

		return new Basic( $definition );
	}
}
