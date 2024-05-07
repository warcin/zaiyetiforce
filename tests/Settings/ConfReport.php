<?php
/**
 * ConfReport test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 */

namespace Tests\Settings;

class ConfReport extends \Tests\Base
{
	/**
	 * All configuration values.
	 *
	 * @var array
	 */
	public static $confReportAll;

	/**
	 * @codeCoverageIgnore
	 * Setting of tests.
	 */
	public static function setUpBeforeClass(): void
	{
		self::$confReportAll = \App\Utils\ConfReport::getAll();
	}

	/**
	 * Testing security configuration report.
	 */
	public function testSecurityConf()
	{
		$this->assertIsArray(
			self::$confReportAll['security'] ?? null,
			'Security configuration (normal mode, show all) report should be not empty'
		);
	}

	/**
	 * Testing database configuration report.
	 */
	public function testDbConf()
	{
		$this->assertNotEmpty(\App\Db::getInstance()->getInfo(), 'Database configuration report should be not empty');
		$this->assertIsArray(
			\App\Db::getInstance()->getInfo(),
			'Database configuration report should be array even if empty'
		);
	}

	/**
	 * Testing system informations report.
	 */
	public function testSystemInfo()
	{
		$this->assertNotEmpty(\App\Utils\ConfReport::getConfig(), 'System information report should be not empty');
	}

	/**
	 * Testing system stability configuration report.
	 */
	public function testStabilityConf()
	{
		$this->assertIsArray(
			self::$confReportAll['stability'] ?? null,
			'Security configuration (normal mode, show all) report should be not empty'
		);
	}
}
