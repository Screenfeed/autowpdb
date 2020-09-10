<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\Basic;

/**
 * Tests for Basic::update().
 *
 * @covers Basic::update
 * @group  Basic
 */
class Test_Update extends TestCase {
	protected $drop_table = true;

	public function testShouldUpdate() {
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
		$this->add_row(
			[
				'file_date' => '2020-09-09 17:16:00',
				'path'      => '/foo/barbaz.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 800,
				'height'    => 200,
			]
		);
		$this->add_row(
			[
				'file_date' => '2020-09-09 17:17:00',
				'path'      => '/foo/barbaz.png',
				'mime_type' => 'image/png',
				'width'     => 100,
				'height'    => 100,
			]
		);

		// Update jpeg rows: date, modified, and data.
		$nbr_rows = $this->table_crud->update(
			[
				'file_id'   => 12,
				'file_date' => '2020-09-09 17:20:00',
				'DATA'      => [ 'foo' => 'bar' ],
				'modified'  => 1,
				'unknown'   => 'foobar',
			],
			[
				'mime_type' => 'image/jpeg',
			]
		);

		$rows    = $this->get_rows();
		$updated = 0;

		$this->assertIsArray( $rows );
		$this->assertCount( 3, $rows );
		$this->assertSame( 2, $nbr_rows );

		foreach ( $rows as $row ) {
			$this->assertIsArray( $row );
			$this->assertArrayHasKey( 'path', $row );
			$this->assertArrayNotHasKey( 'unknown', $row );
			$this->assertNotSame( '12', $row['file_id'] );

			if ( '/foo/bar.jpg' === $row['path'] || '/foo/barbaz.jpg' === $row['path'] ) {
				$this->assertSame( '2020-09-09 17:20:00', $row['file_date'] );
				$this->assertSame( '1', $row['modified'] );
				$this->assertSame( 'a:1:{s:3:"foo";s:3:"bar";}', $row['data'] );
				++$updated;
			} else {
				$this->assertNotSame( '2020-09-09 17:20:00', $row['file_date'] );
				$this->assertNotSame( '1', $row['modified'] );
				$this->assertNotSame( 'a:1:{s:3:"foo";s:3:"bar";}', $row['data'] );
			}
		}

		$this->assertSame( 2, $updated );
	}

	public function testShouldNotUpdate() {
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
		$this->add_row(
			[
				'file_date' => '2020-09-09 17:16:00',
				'path'      => '/foo/barbaz.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 800,
				'height'    => 200,
			]
		);
		$this->add_row(
			[
				'file_date' => '2020-09-09 17:17:00',
				'path'      => '/foo/barbaz.png',
				'mime_type' => 'image/png',
				'width'     => 100,
				'height'    => 100,
			]
		);

		// Update jpeg rows: date, modified, and data.
		$nbr_rows = $this->table_crud->update(
			[
				'modified' => 1,
			],
			[
				'mime_type' => 'image/gif',
			]
		);

		$rows = $this->get_rows();

		$this->assertIsArray( $rows );
		$this->assertCount( 3, $rows );
		$this->assertSame( 0, $nbr_rows );

		foreach ( $rows as $row ) {
			$this->assertIsArray( $row );
			$this->assertArrayHasKey( 'modified', $row );
			$this->assertNotSame( '1', $row['modified'] );
		}
	}
}
