<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\Basic;

/**
 * Tests for Basic::get().
 *
 * @covers Basic::get
 * @group  Basic
 */
class Test_Get extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnNullWhenInvalidReturnType() {
		$this->create_table();
		$this->add_row(
			[
				'file_date' => '2020-09-09 17:15:00',
				'path'      => '/foo/bar.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 600,
				'height'    => 400,
			]
		);

		$result = $this->table_crud->get( [ 'file_id', 'path', 'data' ], [], 'unknown' );

		$this->assertNull( $result );
	}

	public function testShouldReturnRowsWhenNoWhereClauses() {
		$this->create_table();

		$defs = $this->table_definition->get_column_defaults();
		$rows = [
			[
				'file_date' => '2020-09-09 17:15:00',
				'path'      => '/foo/bar.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 600,
				'height'    => 400,
			],
			[
				'file_date' => '2020-09-09 17:16:00',
				'path'      => '/foo/barbaz.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 800,
				'height'    => 200,
			],
			[
				'file_date' => '2020-09-09 17:17:00',
				'path'      => '/foo/barbaz.png',
				'mime_type' => 'image/png',
				'width'     => 100,
				'height'    => 100,
			],
		];

		unset( $defs['file_id'] );

		foreach ( $rows as $i => $row ) {
			$this->add_row( $row );
			$rows[ $i ] = array_merge( $defs, $row );
		}

		$results = $this->table_crud->get( [ '*' ], [], ARRAY_A );

		$this->assertIsArray( $results );
		$this->assertCount( 3, $results );

		foreach ( $results as $i => $result ) {
			unset( $result['file_id'] );
			$this->assertEqualsCanonicalizing( $rows[ $i ], $result );
		}
	}

	public function testShouldReturnRowsWhenWhereClauses() {
		$this->create_table();

		$defs = $this->table_definition->get_column_defaults();
		$rows = [
			[
				'file_date' => '2020-09-09 17:15:00',
				'path'      => '/foo/bar.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 600,
				'height'    => 400,
			],
			[
				'file_date' => '2020-09-09 17:17:00',
				'path'      => '/foo/barbaz.png',
				'mime_type' => 'image/png',
				'width'     => 100,
				'height'    => 100,
			],
			[
				'file_date' => '2020-09-09 17:16:00',
				'path'      => '/foo/barbaz.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 800,
				'height'    => 200,
			],
		];

		unset( $defs['file_id'] );

		foreach ( $rows as $i => $row ) {
			$this->add_row( $row );

			if ( 'image/jpeg' === $row['mime_type'] ) {
				$rows[ $i ] = array_merge( $defs, $row );
			} else {
				unset( $rows[ $i ] );
			}
		}

		$rows = array_values( $rows );

		$results = $this->table_crud->get(
			[ '*' ],
			[
				'mime_type' => 'image/jpeg',
				'error'     => null,
			],
			ARRAY_A
		);

		$this->assertIsArray( $results );
		$this->assertCount( 2, $results );

		foreach ( $results as $i => $result ) {
			unset( $result['file_id'] );
			$this->assertEqualsCanonicalizing( $rows[ $i ], $result );
		}
	}
}
