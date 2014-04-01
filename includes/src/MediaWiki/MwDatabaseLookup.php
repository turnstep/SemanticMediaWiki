<?php

namespace SMW\MediaWiki;

use Title;
use UnexpectedValueException;

/**
 * @ingroup SMW
 *
 * @licence GNU GPL v2+
 * @since 1.9.2
 *
 * @author mwjames
 */
class MwDatabaseLookup {

	/** @var Database */
	protected $database = null;

	protected $namespace = null;

	/**
	 * @since 1.9.2
	 *
	 * @param Database $database
	 */
	public function __construct( Database $database ) {
		$this->database = $database;
	}

	/**
	 * @since 1.9.2
	 *
	 * @param int $namespace
	 *
	 * @return MwDatabaseLookup
	 */
	public function byNamespace( $namespace ) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * @since 1.9.2
	 *
	 * @return array[]|Title[]
	 * @throws UnexpectedValueException
	 */
	public function selectAllTitles() {

		if ( $this->namespace === null ) {
			throw new UnexpectedValueException( 'Expected a namespace because an unrestricted selection is not supported' );
		}

		if ( $this->namespace === NS_CATEGORY ) {
			$tableName = 'category';
			$fields = array( 'cat_title' );
			$conditions = '';
			$options = array( 'USE INDEX' => 'cat_title' );
		} else {
			$tableName = 'page';
			$fields = array( 'page_namespace', 'page_title' );
			$conditions = array( 'page_namespace' => $this->namespace );
			$options = array( 'USE INDEX' => 'PRIMARY' );
		}

		$res = $this->database->select(
			$tableName,
			$fields,
			$conditions,
			__METHOD__,
			$options
		);

		return $this->makeTitlesFromSelection( $res );
	}

	/**
	 * @since 1.9.2
	 *
	 * @param int $startId
	 * @param int $endId
	 *
	 * @return null|Title[]
	 * @throws UnexpectedValueException
	 */
	public function selectTitlesByRange( $startId = 0, $endId = 0 ) {

		if ( $this->namespace === NS_CATEGORY ) {
			throw new UnexpectedValueException( 'Range selection for the category namespace is not supported' );
		}

		$tableName = 'page';
		$fields = array( 'page_namespace', 'page_title', 'page_id' );
		$conditions = array( "page_id BETWEEN $startId AND $endId" ) + array( 'page_namespace' => $this->namespace );
		$options =array( 'ORDER BY' => 'page_id ASC', 'USE INDEX' => 'PRIMARY' );

		$res = $this->database->select(
			$tableName,
			$fields,
			$conditions,
			__METHOD__,
			$options
		);

		return $this->makeTitlesFromSelection( $res );
	}

	/**
	 * @since 1.9.2
	 *
	 * @return int
	 */
	public function selectMaxPageId() {

		if ( $this->namespace === NS_CATEGORY ) {
			throw new UnexpectedValueException( 'Max Id selection for the category namespace is not supported' );
		}

		return $this->database->selectField(
			'page',
			'MAX(page_id)',
			false,
			__METHOD__
		);
	}

	protected function makeTitlesFromSelection( $res ) {

		$pages = array();

		if ( $res === false ) {
			return $pages;
		}

		foreach ( $res as $row ) {

			if ( $this->namespace === NS_CATEGORY ) {
				$ns = NS_CATEGORY;
				$title = $row->cat_title;
			} else {
				$ns =  $row->page_namespace;
				$title = $row->page_title;
			}

			$pages[] = Title::makeTitle( $ns, $title );
		}

		return $pages;
	}

}