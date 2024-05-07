<?php
/**
 * Zip test file.
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
 * Zip test class.
 */
class Zip extends \Tests\Base
{
	/**
	 * Testing instance from file with no file name provided.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testInstanceOpenNoFileName(): void
	{
		$this->expectException(\App\Exceptions\AppException::class);
		\App\Zip::openFile(false);
	}

	/**
	 * Testing instance from not existing file.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testInstanceOpenFileNotExists(): void
	{
		$this->expectException(\App\Exceptions\AppException::class);
		\App\Zip::openFile('tests/data/NxFile.zip')->close();
	}

	/**
	 * Testing instance from linux generated zip file.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testInstanceOpenLinuxFile(): void
	{
		$instanceOpen = \App\Zip::openFile('tests/data/TestLinux.zip');
		$this->assertInstanceOf('\App\Zip', $instanceOpen, 'Expected zip object instance');
	}

	/**
	 * Testing linux file unzip.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testUnzipLinuxFile(): void
	{
		$instanceOpen = \App\Zip::openFile('tests/data/TestLinux.zip', ['checkFiles' => false]);
		$instanceOpen->unzip('tests/tmp/TestLinux/');
		$this->assertFileExists('tests/tmp/TestLinux/manifest.xml');
		$this->assertFileExists('tests/tmp/TestLinux/languages/pl-PL/TestLinux.json');
		\vtlib\Functions::recurseDelete('tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'TestLinux');
	}

	/**
	 * Testing linux file extract.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testExtractLinuxFile(): void
	{
		$instanceOpen = \App\Zip::openFile('tests/data/TestLinux.zip', ['checkFiles' => false]);
		$instanceOpen->extract('tests/tmp/TestLinux/');
		$this->assertFileExists('tests/tmp/TestLinux/manifest.xml');
		$this->assertFileExists('tests/tmp/TestLinux/languages/pl-PL/TestLinux.json');
		\vtlib\Functions::recurseDelete('tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'TestLinux');
	}

	/**
	 * Testing file creation in not existent directory.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testCreateFileBadDir(): void
	{
		$zip = \App\Zip::createFile(ROOT_DIRECTORY . '/tests/data/NxDir/NxFile.zip');
		$zip->addFromString('filename.txt', '<minimal content>');
		$this->expectWarning();
		$this->assertFalse($zip->close());
	}
}
