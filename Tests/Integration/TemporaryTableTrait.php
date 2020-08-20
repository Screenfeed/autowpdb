<?php

namespace Screenfeed\AutoWPDB\Tests\Integration;

trait TemporaryTableTrait {
	protected $table_name        = 'foobar';
	protected $target_table_name = 'targettable';
	protected $drop_table        = false;
	protected $drop_target_table = false;

	protected function init_temporary_tables() {
		global $wpdb;

		$this->table_name        = $wpdb->prefix . $this->table_name;
		$this->target_table_name = $wpdb->prefix . $this->target_table_name;
	}

	protected function maybe_drop_temporary_tables() {
		global $wpdb;

		if ( $this->drop_table ) {
			$query  = "DROP TEMPORARY TABLE IF EXISTS `{$this->table_name}`";
			$result = $wpdb->query( $query );
		}

		if ( $this->drop_target_table ) {
			$query  = "DROP TEMPORARY TABLE IF EXISTS `{$this->target_table_name}`";
			$result = $wpdb->query( $query );
		}
	}

	protected function create_table( $table_name = '' ) {
		global $wpdb;

		if ( empty( $table_name ) ) {
			$table_name = $this->table_name;
		}

		$charset_collate = $wpdb->get_charset_collate();
		$schema          = "
			id bigint(20) unsigned NOT NULL auto_increment,
			data longtext default NULL,
			PRIMARY KEY  (id)";

		$wpdb->query( "CREATE TEMPORARY TABLE `$table_name` ($schema) $charset_collate" );
	}

	protected function add_row( $data, $table_name = '' ) {
		global $wpdb;

		if ( empty( $table_name ) ) {
			$table_name = $this->table_name;
		}

		$wpdb->insert(
			$table_name,
			[ 'data' => $data ],
			[ 'data' => '%s' ]
		);

		return (int) $wpdb->insert_id;
	}

	protected function get_rows( $table_name = '' ) {
		global $wpdb;

		if ( empty( $table_name ) ) {
			$table_name = $this->table_name;
		}

		return $wpdb->get_results(
			"SELECT * FROM $table_name ORDER BY `id` ASC",
			ARRAY_A
		);
	}

	protected function get_last_row( $table_name = '' ) {
		global $wpdb;

		if ( empty( $table_name ) ) {
			$table_name = $this->table_name;
		}

		return $wpdb->get_row(
			"SELECT * FROM {$table_name} ORDER BY `id` DESC LIMIT 1;",
			ARRAY_A
		);
	}
}
