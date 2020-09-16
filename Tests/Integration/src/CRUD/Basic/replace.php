<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\Basic;

/**
 * Tests for Basic::replace().
 *
 * @covers Basic::replace
 * @group  Basic
 */
class Test_Replace extends TestCase {
	protected $drop_table = true;

	public function testShouldReplace() {
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

		$to_replace = $this->get_last_row();
		$file_id    = (int) $to_replace['file_id'];

		$this->add_row(
			[
				'file_date' => '2020-09-09 17:17:00',
				'path'      => '/foo/barbaz.png',
				'mime_type' => 'image/png',
				'width'     => 100,
				'height'    => 100,
			]
		);

		// Replace date, width, and height.
		$insert_id = $this->table_crud->replace(
			[
				'file_id'   => $file_id,
				'file_date' => '2020-09-09 17:20:00',
				'path'      => '/foo/barbaz.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 1000,
				'height'    => 600,
			]
		);

		$row      = $this->get_row( $file_id );
		$expected = [
			'file_id'   => "$file_id",
			'file_date' => '2020-09-09 17:20:00',
			'path'      => '/foo/barbaz.jpg',
			'mime_type' => 'image/jpeg',
			'modified'  => '0',
			'width'     => '1000',
			'height'    => '600',
			'file_size' => '0',
			'status'    => null,
			'error'     => null,
			'data'      => null,
		];

		$this->assertIsArray( $row );
		$this->assertEqualsCanonicalizing( $expected, $row );
		$this->assertSame( $file_id, $insert_id );
	}

	public function testShouldInsert() {
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

		$row       = $this->get_last_row();
		$file_id_1 = (int) $row['file_id'];

		$this->add_row(
			[
				'file_date' => '2020-09-09 17:16:00',
				'path'      => '/foo/barbaz.jpg',
				'mime_type' => 'image/jpeg',
				'width'     => 800,
				'height'    => 200,
			]
		);

		$row       = $this->get_last_row();
		$file_id_2 = (int) $row['file_id'];

		// Change file extension (path is UNIQUE).
		$insert_id = $this->table_crud->replace(
			[
				'file_date' => '2020-09-09 17:20:00',
				'path'      => '/foo/barbaz.png',
				'mime_type' => 'image/png',
				'width'     => 1000,
				'height'    => 600,
			]
		);

		$row      = $this->get_last_row();
		$file_id  = (int) $row['file_id'];
		$expected = [
			'file_id'   => "$file_id",
			'file_date' => '2020-09-09 17:20:00',
			'path'      => '/foo/barbaz.png',
			'mime_type' => 'image/png',
			'modified'  => '0',
			'width'     => '1000',
			'height'    => '600',
			'file_size' => '0',
			'status'    => null,
			'error'     => null,
			'data'      => null,
		];

		$this->assertIsArray( $row );
		$this->assertEqualsCanonicalizing( $expected, $row );
		$this->assertNotContains( $file_id, [ $file_id_1, $file_id_2 ] );
	}
}
