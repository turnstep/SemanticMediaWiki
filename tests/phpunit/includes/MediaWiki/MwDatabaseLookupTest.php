<?php

namespace SMW\Tests\MediaWiki;

use SMW\MediaWiki\MwDatabaseLookup;

use Title;

/**
 * @covers \SMW\MediaWiki\MwDatabaseLookup
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 *
 * @license GNU GPL v2+
 * @since 1.9.2
 *
 * @author mwjames
 */
class MwDatabaseLookupTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$database = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SMW\MediaWiki\MwDatabaseLookup',
			new MwDatabaseLookup( $database )
		);
	}

	public function testSelectAllOnCategoryNamespace() {

		$row = new \stdClass;
		$row->cat_title = 'Foo';

		$database = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$database->expects( $this->any() )
			->method( 'select' )
			->with( $this->stringContains( 'category' ),
				$this->anything(),
				$this->anything(),
				$this->anything(),
				$this->anything() )
			->will( $this->returnValue( array( $row ) ) );

		$instance = new MwDatabaseLookup( $database );

		$this->assertArrayOfTitles( $instance->byNamespace( NS_CATEGORY )->selectAllTitles() );
	}

	public function testSelectAllOnMainNamespace() {

		$row = new \stdClass;
		$row->page_namespace = NS_MAIN;
		$row->page_title = 'Bar';

		$database = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$database->expects( $this->any() )
			->method( 'select' )
			->with( $this->anything(),
				$this->anything(),
				$this->equalTo( array( 'page_namespace' => NS_MAIN ) ),
				$this->anything(),
				$this->anything() )
			->will( $this->returnValue( array( $row ) ) );

		$instance = new MwDatabaseLookup( $database );

		$this->assertArrayOfTitles( $instance->byNamespace( NS_MAIN )->selectAllTitles() );
	}

	public function testSelectAllOnMissingNamespaceThrowsException() {

		$this->setExpectedException( 'UnexpectedValueException' );

		$database = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new MwDatabaseLookup( $database );
		$instance->selectAllTitles();
	}

	protected function assertArrayOfTitles( $arrayOfTitles ) {

		$this->assertInternalType( 'array', $arrayOfTitles );

		foreach ( $arrayOfTitles as $title ) {
			$this->assertInstanceOf( 'Title', $title );
		}
	}

}