<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\CRUD\Basic;

/**
 * Tests for Basic::insert().
 *
 * @covers Basic::insert
 * @group  Basic
 */
class Test_Insert extends TestCase {
	protected $drop_table = true;

	public function testShouldReturnInsertId() {
		$this->create_table();

		$data = [
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

		$insert_id = $this->table_crud->insert( $data );
		$row       = $this->get_last_row();

		$expected = [
			'file_id'   => '1',
			'file_date' => '0000-00-00 00:00:00',
			'path'      => '/foo/bar',
			'mime_type' => 'image/jpeg',
			'modified'  => '0',
			'width'     => '600',
			'height'    => '400',
			'file_size' => '0',
			'status'    => null,
			'error'     => null,
			'data'      => 'a:1:{s:3:"foo";s:3:"bar";}',
		];

		$this->assertIsArray( $row );
		$this->assertEqualsCanonicalizing( $expected, $row );
		$this->assertSame( 1, $insert_id );
	}
}
