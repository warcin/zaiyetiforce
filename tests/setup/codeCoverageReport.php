<?php

declare(strict_types=1);
/**
 * Generate code coverage report.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
$path = getcwd();
chdir(__DIR__ . '/../../');

if (!file_exists('vendor')) {
	return;
}

require_once 'include/ConfigUtils.php';

$codeCoverage = Tests\Coverage::getInstance();
$codeCoverage->generateReport();

chdir($path);
