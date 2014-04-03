<?php

namespace SMW\Tests\Regression;

use SMW\Test\MwRegressionTestCase;
use SMW\Test\MaintenanceRunner;

use Title;

/**
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 * @group RegressionTest
 * @group Database
 * @group medium
 *
 * @licence GNU GPL v2+
 * @since 1.9.2
 *
 * @author mwjames
 */
class RebuildDataMaintenanceRegressionTest extends MwRegressionTestCase {

	protected $maintenanceRunner = null;

	public function getSourceFile() {
		return __DIR__ . '/data/' . 'GenericLoremIpsumTest-Mw-1-19-7.xml';
	}

	public function acquirePoolOfTitles() {
		return array(
			'Category:Lorem ipsum',
			'Lorem ipsum',
			'Elit Aliquam urna interdum',
			'Platea enim hendrerit',
			'Property:Has Url',
			'Property:Has annotation uri',
			'Property:Has boolean',
			'Property:Has date',
			'Property:Has email',
			'Property:Has number',
			'Property:Has page',
			'Property:Has quantity',
			'Property:Has temperature',
			'Property:Has text'
		);
	}

	public function assertDataImport() {

		// Equivalent of running php rebuildData.php --< myOptions >
		$this->maintenanceRunner = new MaintenanceRunner( 'SMW\Maintenance\RebuildData' );
		$this->maintenanceRunner->setQuiet();

		$this->assertTrue( $this->assertRunWithoutOptions() );
		$this->assertTrue( $this->assertRunWithFullDeleteOption() );
		$this->assertTrue( $this->assertRunWithIdRangeOption() );
		$this->assertTrue( $this->assertRunWithCategoryOption() );
	}

	protected function assertRunWithoutOptions() {
		return $this->maintenanceRunner->run();
	}

	protected function assertRunWithFullDeleteOption() {
		return $this->maintenanceRunner->setOptions( array( 'f' => true ) )->run();
	}

	protected function assertRunWithIdRangeOption() {
		return $this->maintenanceRunner->setOptions( array( 's' => 1, 'e' => 10 ) )->run();
	}

	protected function assertRunWithCategoryOption() {
		return $this->maintenanceRunner->setOptions( array( 'c' => true ) )->run();
	}

}
