<?php

/**
 * Library test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Tests\Base;

class F_Library extends \Tests\Base
{
	/**
	 * Testing library versions.
	 */
	public function testLibraryVersion()
	{
		\Settings_ModuleManager_Library_Model::downloadAll();
		$libs = \Settings_ModuleManager_Library_Model::getAll();
		foreach ($libs as $name => $lib) {
			$appVersion = \App\Version::get($lib['name']);
			$this->assertFileExists($lib['dir'] . 'version.php', 'File does not exist: ' . $lib['dir'] . 'version.php');

			$libVersions = require $lib['dir'] . 'version.php';
			$libVersion = $libVersions['version'];
			$this->assertTrue($appVersion == $libVersion, "Wrong library version: $name, library version: $libVersion, config version: $appVersion");
		}
	}
}
