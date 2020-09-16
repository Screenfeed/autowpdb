<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\Basic;

/**
 * Tests for Basic::delete().
 *
 * @covers Basic::delete
 * @group  Basic
 */
class Test_Delete extends TestCase {
	protected $drop_table = true;

	public function testShouldNotDeleteRows() {
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

		$nbr_rows = $this->table_crud->delete( [] );

		$row = $this->get_last_row();

		$this->assertFalse( $nbr_rows );
		$this->assertIsArray( $row );

		$nbr_rows = $this->table_crud->delete(
			[
				'mime_type' => 'image/png',
			]
		);

		$row = $this->get_last_row();

		$this->assertSame( 0, $nbr_rows );
		$this->assertIsArray( $row );
	}

	public function testShouldDeleteRows() {
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

		$last_row = $this->get_last_row();
		$file_id  = (int) $last_row['file_id'];

		$nbr_rows = $this->table_crud->delete(
			[
				'mime_type' => 'image/jpeg',
			]
		);

		$rows     = $this->get_rows();
		$expected = [
			'file_id'   => "$file_id",
			'file_date' => '2020-09-09 17:17:00',
			'path'      => '/foo/barbaz.png',
			'mime_type' => 'image/png',
			'modified'  => '0',
			'width'     => '100',
			'height'    => '100',
			'file_size' => '0',
			'status'    => null,
			'error'     => null,
			'data'      => null,
		];

		$this->assertIsArray( $rows );
		$this->assertCount( 1, $rows );

		$row = reset( $rows );

		$this->assertEqualsCanonicalizing( $expected, $row );
		$this->assertSame( 2, $nbr_rows );
	}
}
