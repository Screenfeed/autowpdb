<?php
namespace Screenfeed\AutoWPDB\Tests\Fixtures\src\Table;

use Screenfeed\AutoWPDB\TableDefinition\AbstractTableDefinition;

/**
 * Class that defines our custom table.
 *
 * @since 0.3
 */
class CustomTable extends AbstractTableDefinition {
	protected $schema;
	protected $short_name;
	protected $table_is_global;

	protected $default_schema = "
	file_id bigint(20) unsigned NOT NULL auto_increment,
	file_date datetime NOT NULL default '0000-00-00 00:00:00',
	path varchar(191) NOT NULL default '',
	mime_type varchar(100) NOT NULL default '',
	modified tinyint(1) unsigned NOT NULL default 0,
	width smallint(2) unsigned NOT NULL default 0,
	height smallint(2) unsigned NOT NULL default 0,
	file_size int(4) unsigned NOT NULL default 0,
	status varchar(20) default NULL,
	error varchar(255) default NULL,
	data longtext default NULL,
	PRIMARY KEY  (file_id),
	UNIQUE KEY path (path),
	KEY status (status),
	KEY modified (modified)";
	protected $default_short_name = 'foobar';
	protected $default_table_is_global = true;

	public function get_table_version(): int {
		return 102;
	}

	public function get_table_short_name(): string {
		if ( ! isset( $this->short_name ) ) {
			$this->short_name = $this->default_short_name;
		}
		return $this->short_name;
	}

	public function is_table_global(): bool {
		if ( ! isset( $this->table_is_global ) ) {
			$this->table_is_global = $this->default_table_is_global;
		}
		return $this->table_is_global;
	}

	public function get_primary_key(): string {
		return 'file_id';
	}

	public function get_column_placeholders(): array {
		return [
			'file_id'   => '%d',
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
	}

	public function get_column_defaults(): array {
		return [
			'file_id'   => 0,
			'file_date' => '0000-00-00 00:00:00',
			'path'      => '',
			'mime_type' => '',
			'modified'  => 0,
			'width'     => 0,
			'height'    => 0,
			'file_size' => 0,
			'status'    => null,
			'error'     => null,
			'data'      => [],
		];
	}

	public function get_table_schema(): string {
		if ( ! isset( $this->schema ) ) {
			$this->schema = $this->default_schema;
		}
		return $this->schema;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** SETTERS ================================================================================= */
	/** ----------------------------------------------------------------------------------------- */

	public function set_table_short_name( $short_name ) {
		$this->short_name = $short_name;
	}

	public function set_table_schema( $schema ) {
		$this->schema = $schema;
	}

	public function set_table_is_global( $table_is_global ) {
		$this->table_is_global = $table_is_global;
	}
}
