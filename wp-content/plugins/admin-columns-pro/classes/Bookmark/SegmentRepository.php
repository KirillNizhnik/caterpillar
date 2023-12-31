<?php

namespace ACP\Bookmark;

use AC\Type\ListScreenId;
use ACP\Bookmark\Entity\Segment;
use ACP\Bookmark\Type\SegmentId;
use DateTime;
use InvalidArgumentException;
use RuntimeException;

class SegmentRepository {

	public const FILTER_USER = 'user_id';
	public const FILTER_LIST_SCREEN = 'list_screen_id';
	public const FILTER_GLOBAL = 'global';
	public const ORDER_BY = 'orderby';
	public const ORDER = 'order';
	public const TABLE = 'ac_segments';

	public function find( SegmentId $id ): ?Segment {
		global $wpdb;

		$sql = "
			SELECT *
			FROM " . $wpdb->prefix . self::TABLE . "
			WHERE id = %s
		";

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $id->get_id() ) );

		if ( ! isset( $result->id ) ) {
			return null;
		}

		return $this->create_segment_from_row( $result );
	}

	/**
	 * @param array $args
	 *
	 * @return Segment[]
	 */
	public function find_all( array $args = [] ): array {
		global $wpdb;

		$args = array_merge( [
			self::FILTER_USER        => null,
			self::FILTER_LIST_SCREEN => null,
			self::FILTER_GLOBAL      => null, // Global available to all users
			self::ORDER_BY           => 'date_created', // e.g. `name`, `date_created`, `id`, `user_id`
			self::ORDER              => null,
		], $args );

		$sql = "
			SELECT * 
			FROM {$wpdb->prefix}" . self::TABLE;

		$and = [];

		if ( $args[ self::FILTER_LIST_SCREEN ] ) {
			$listScreenId = $args[ self::FILTER_LIST_SCREEN ];

			if ( ! $listScreenId instanceof ListScreenId ) {
				throw new InvalidArgumentException( 'Expected a ListScreenId for list screen id.' );
			}

			$and[] = $wpdb->prepare( "`list_screen_id` = %s", $listScreenId->get_id() );
		}

		if ( $args[ self::FILTER_USER ] ) {
			$and[] = $wpdb->prepare( "`user_id` = %d", $args[ self::FILTER_USER ] );
		}

		if ( is_bool( $args[ self::FILTER_GLOBAL ] ) ) {
			$and[] = $wpdb->prepare( "`global` = %d", $args[ self::FILTER_GLOBAL ] );
		}

		if ( $and ) {
			$sql .= "\nWHERE " . implode( "\nAND ", $and );
		}

		$order = $args[ self::ORDER ] === 'ASC'
			? $args[ self::ORDER ]
			: 'DESC';

		$sql .= sprintf( "\nORDER BY `%s` %s", $wpdb->_escape( $args[ self::ORDER_BY ] ), $order );

		$segments = [];

		foreach ( $wpdb->get_results( $sql ) as $row ) {
			$segments[ $row->id ] = $this->create_segment_from_row( $row );
		}

		return $segments;
	}

	private function create_segment_from_row( object $row ): Segment {
		return new Segment(
			new SegmentId( (int) $row->id ),
			new ListScreenId( $row->list_screen_id ),
			(int) $row->user_id,
			$row->name,
			unserialize( $row->url_parameters, [ 'allowed_classes' => false ] ),
			(bool) $row->global
		);
	}

	public function create(
		ListScreenId $list_screen_id,
		int $user_id,
		string $name,
		array $url_parameters,
		bool $global
	): Segment {
		global $wpdb;

		$inserted = $wpdb->insert(
			$wpdb->prefix . self::TABLE,
			[
				'list_screen_id' => $list_screen_id->get_id(),
				'user_id'        => $user_id,
				'name'           => $name,
				'url_parameters' => serialize( $url_parameters ),
				'global'         => $global,
				'date_created'   => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
			],
			[
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
			]
		);

		if ( $inserted !== 1 ) {
			throw new RuntimeException( 'Failed to save segment.' );
		}

		return new Segment(
			new SegmentId( $wpdb->insert_id ),
			$list_screen_id,
			$user_id,
			$name,
			$url_parameters,
			$global
		);
	}

	public function delete( SegmentId $id ): void {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . self::TABLE,
			[
				'id' => $id->get_id(),
			],
			[
				'%d',
			]
		);
	}

}