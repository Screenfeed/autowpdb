<?php
namespace Screenfeed\AutoWPDB\Tests\Fixtures\src\CRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;

class CustomCRUD extends AbstractCRUD {

	public function insert( array $data ): int {
		return 0;
	}

	public function replace( array $data ): int {
		return 0;
	}

	public function get( array $select, array $where, string $output_type = OBJECT ) {
		return null;
	}

	public function update( array $data, array $where ) {
		return false;
	}

	public function delete( array $where ) {
		return false;
	}
}
