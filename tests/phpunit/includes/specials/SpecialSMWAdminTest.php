<?php

namespace SMW\Test;

use SMWAdmin;
use FauxRequest;
use User;

/**
 * @covers \SMWAdmin
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 * @group SpecialPage
 * @group medium
 *
 * @licence GNU GPL v2+
 * @since 1.9.0.2
 *
 * @author mwjames
 */
class SpecialSMWAdminTest extends SpecialPageTestCase {

	public function getClass() {
		return '\SMWAdmin';
	}

	protected function getInstance() {
		return new SMWAdmin();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf( $this->getClass(), $this->getInstance() );
	}

	public function testExecuteWithMissingPermissionThrowsException() {

		$this->setExpectedException( 'PermissionsError' );
		$this->execute( '', null, new User );
	}

	public function testExecute() {

		$this->execute( '', null, new MockSuperUser );
		$this->assertInternalType( 'string', $this->getText() );
	}

	/**
	 * @depends testExecute
	 */
	public function testExecuteOnActionListSettings() {

		$this->execute( '', new FauxRequest( array( 'action' => 'listsettings' ) ), new MockSuperUser );
		$this->assertInternalType( 'string', $this->getText() );
	}

	/**
	 * @depends testExecute
	 */
	public function testExecuteOnIdLookup() {

		$fakeIdTableClass = new FakeClass;
		$fakeIdTableClass->getIdTable = function() { return 'fake_foo_table'; };

		$selectRow = new \stdClass;
		$selectRow->smw_title = 'Queey';

		$database = $this->getMockBuilder( 'SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$database->expects( $this->once() )
			->method( 'selectRow' )
			->with( $this->equalTo( 'fake_foo_table' ) )
			->will( $this->returnValue( $selectRow ) );

		$store = $this->getMockBuilder( 'SMWSQLStore3' )
			->disableOriginalConstructor()
			->getMock();

		$store->expects( $this->once() )
			->method( 'getDatabase' )
			->will( $this->returnValue( $database ) );

		$store->expects( $this->once() )
			->method( 'getObjectIds' )
			->will( $this->returnValue( $fakeIdTableClass ) );

		$this->setStore( $store );
		$this->execute( '', new FauxRequest(
			array(
				'action' => 'idlookup',
				'objectId' => '9999'
			) ), new MockSuperUser
		);

		$this->assertInternalType( 'string', $this->getText() );
	}

}
