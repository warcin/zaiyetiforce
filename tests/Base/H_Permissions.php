<?php

/**
 * Permissions test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Tests\Base;

class H_Permissions extends \Tests\Base
{
	/**
	 * Testing record permissions.
	 */
	public function testIsPermitted()
	{
		$this->assertTrue(\App\Privilege::isPermitted('Accounts', 'DetailView', \Tests\Base\C_RecordActions::createAccountRecord()->getId()));
	}

	/**
	 * Testing refreshing permission files.
	 */
	public function testRecalculateSharingRules()
	{
		\App\UserPrivilegesFile::recalculateAll();
		$this->assertTrue(true);
	}

	/**
	 * Testing refreshing module meta files.
	 */
	public function testCreateModuleMetaFile()
	{
		\App\Module::createModuleMetaFile();
		$this->assertTrue(true);
	}
}
