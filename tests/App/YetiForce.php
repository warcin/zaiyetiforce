<?php
/**
 * YetiForce test file.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Tests\App;

/**
 * YetiForce test class.
 */
class YetiForce extends \Tests\Base
{
	/**
	 * Testing watchdog getAll method.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testWatchdogGetAll()
	{
		$this->assertCount(\count(\App\YetiForce\Watchdog::$variables), \App\YetiForce\Watchdog::getAll());
	}
}
