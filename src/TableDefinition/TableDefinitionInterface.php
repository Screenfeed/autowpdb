<?php
/**
 * Interface to define a table.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB\TableDefinition;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Interface that defines the DB table.
 *
 * @since 0.1
 */
interface TableDefinitionInterface {

	/**
	 * Get the table version.
	 *
	 * @since 0.1
	 *
	 * @return int
	 */
	public function get_table_version(): int;

	/**
	 * Get the table "short name", aka the unprefixed table name.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_table_short_name(): string;

	/**
	 * Get the full name of the table, with the DB prefix.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_table_name(): string;

	/**
	 * Tell if the table is the same for each site of a Multisite.
	 *
	 * @since 0.1
	 *
	 * @return bool True if the table is common to all sites. False if each site has its own table.
	 */
	public function is_table_global(): bool;

	/**
	 * Get the name of the primary column.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_primary_key(): string;

	/**
	 * Get the column placeholders.
	 * Column names must be lowercase.
	 * This is also used to know how to cast the values.
	 * Example:
	 *     [
	 *         'file_id'   => '%d',
	 *         'file_date' => '%s',
	 *         'path'      => '%s',
	 *         'mime_type' => '%s',
	 *         'modified'  => '%d',
	 *         'percent'   => '%f',
	 *         'data'      => '%s',
	 *     ]
	 *
	 * @since 0.1
	 *
	 * @return array<string>
	 */
	public function get_column_placeholders(): array;

	/**
	 * Default column values.
	 * Column names must be lowercase. Columns must be in the same order than the ones from get_column_placeholders().
	 * This is also used to know how to cast the values in case of an array or an object.
	 * Example:
	 *     [
	 *         'file_id'   => 0,
	 *         'file_date' => '0000-00-00 00:00:00',
	 *         'path'      => '',
	 *         'mime_type' => '',
	 *         'modified'  => 0,
	 *         'percent'   => 0.0,
	 *         'data'      => [],
	 *     ]
	 *
	 * @since 0.1
	 *
	 * @return array<mixed>
	 */
	public function get_column_defaults(): array;

	/**
	 * Get the query to create the table fields.
	 * Column names must be lowercase.
	 * Don't forget to put 2 space characters after "PRIMARY KEY".
	 * Example:
	 *     "
	 *     file_id bigint(20) unsigned NOT NULL auto_increment,
	 *     file_date datetime NOT NULL default '0000-00-00 00:00:00',
	 *     path varchar(191) NOT NULL default '',
	 *     mime_type varchar(100) NOT NULL default '',
	 *     modified tinyint(1) unsigned NOT NULL default 0,
	 *     percent float(4,2) unsigned default null,
	 *     data longtext default NULL,
	 *     PRIMARY KEY  (file_id),
	 *     UNIQUE KEY path (path),
	 *     KEY modified (modified)"
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_table_schema(): string;
}
