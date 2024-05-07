<?php

/**
 * MultiImage test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 * @author    Radosław Skrzypczak <r.skrzypczak@yetiforce.com>
 */

namespace Tests\Base;

class Z_MultiImage extends \Tests\Base
{
	/** @var string[] Files array. */
	public static $files = ['0.jpg', '1.png', '2.png', '3.jpg'];

	/** @var bool Cache */
	private static $cache = [];

	/**
	 * @codeCoverageIgnore
	 * Setting of tests.
	 */
	public static function setUpBeforeClass(): void
	{
		\mkdir(ROOT_DIRECTORY . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'MultiImage', 0777, true);
	}

	/**
	 * Data provider for the attach image to record test.
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function providerImageForRecord()
	{
		$data = [];
		for ($i = 0; $i < 4; ++$i) {
			$data[] = ['Users', 'imagename', $i];
			$data[] = ['Contacts', 'imagename', $i];
			$data[] = ['Products', 'imagename', $i];
		}
		return $data;
	}

	/**
	 * Data provider for the delete record images test.
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function providerDeleteImageForRecord()
	{
		return [
			['Users', 'imagename'],
			['Contacts', 'imagename'],
			['Products', 'imagename'],
		];
	}

	/**
	 * Attach image to record test.
	 *
	 * @param $module
	 * @param $record
	 * @param $field
	 * @param $file
	 *
	 * @throws \App\Exceptions\AppException
	 * @dataProvider providerImageForRecord
	 */
	public function testAttachImageToRecord($module, $field, $file): void
	{
		switch ($module) {
			case 'Users':
				$record = self::$cache['Users'] ?? \App\User::getUserIdByName('admin');
				break;
			case 'Contacts':
				$record = self::$cache['Contacts'] ?? \Tests\Base\C_RecordActions::createContactRecord(false)->getId();
				break;
			case 'Products':
				$record = self::$cache['Products'] ?? \Tests\Base\C_RecordActions::createProductRecord(false)->getId();
				break;
			default:
				return; // @codeCoverageIgnore
				break;
		}
		self::$cache[$module] = $record;
		$filePathSrc = ROOT_DIRECTORY . '/tests/data/MultiImage/' . self::$files[$file];
		$filePathDst = ROOT_DIRECTORY . '/storage/MultiImage/' . md5(random_int(0, 9999)) . substr(self::$files[$file], \strpos(self::$files[$file], '.'));
		\copy($filePathSrc, $filePathDst);
		$fileObj = \App\Fields\File::loadFromPath($filePathDst);
		$hash = $fileObj->generateHash(true, $filePathDst);
		$attach[] = [
			'name' => self::$files[$file],
			'size' => $fileObj->getSize(),
			'key' => $hash,
			'path' => $fileObj->getPath(),
		];
		$recordModel = \Vtiger_Record_Model::getInstanceById($record, $module);
		$this->assertSame($record, $recordModel->getId(), 'Record ' . $record . '(' . $module . ') load error');
		$recordModel->set($field, \App\Json::encode($attach));
		$recordModel->save();
		$recordModel = \Vtiger_Record_Model::getInstanceById($record, $module);
		$fieldModel = $recordModel->getField($field);
		$data = \App\Json::decode(\App\Purifier::decodeHtml($fieldModel->getUITypeModel()->getDisplayValueEncoded($recordModel->get($field), $recordModel->getId(), $fieldModel->getFieldInfo()['limit'])));
		$dataPath = \App\Json::decode($recordModel->get($field))[0]['path'];
		$this->assertNotEmpty($data);
		$this->assertSame(self::$files[$file], $data[0]['name'], 'File name should be equal');
		$this->assertSame($fileObj->getSize(), $data[0]['size'], 'File size should be equal');
		$this->assertFileExists($dataPath, 'File should exists');
		$this->assertSame($hash, $data[0]['key'], 'Key should be equal');
		parse_str(\parse_url($data[0]['imageSrc'])['query'], $url);
		$this->assertSame($module, $url['module'], 'Module in image url should be equal to provided');
		$this->assertSame($field, $url['field'], 'Field name in image url should be equal to provided');
		$this->assertSame($hash, $url['key'], 'Key in image url should be equal to provided');
		$this->assertSame((string) $record, $url['record'], 'Record in image url should be equal to provided');
	}

	/**
	 * Delete record image test.
	 *
	 * @param $module
	 * @param $field
	 *
	 * @throws \App\Exceptions\AppException
	 * @dataProvider providerDeleteImageForRecord
	 */
	public function testDeleteImage($module, $field): void
	{
		$recordModel = \Vtiger_Record_Model::getInstanceById(self::$cache[$module], $module);
		$data = \App\Json::decode($recordModel->get($field));
		$this->assertFileExists($data[0]['path'], 'File should exists');
		$recordModel->set($field, '');
		$recordModel->save();
		$recordModel = \Vtiger_Record_Model::getInstanceById(self::$cache[$module], $module);
		$this->assertSame('', $recordModel->get($field), 'Value should be empty');
		$this->assertFileDoesNotExist($data[0]['path'], 'File should be removed');
	}

	/**
	 * Add multi image test.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testAddMultiImage(): void
	{
		$recordModel = \Vtiger_Record_Model::getInstanceById(\Tests\Base\C_RecordActions::createProductRecord(false)->getId(), 'Products');
		$attach = [];
		foreach (self::$files as $i => $name) {
			$filePathSrc = 'tests' . \DIRECTORY_SEPARATOR . 'data' . \DIRECTORY_SEPARATOR . 'MultiImage' . \DIRECTORY_SEPARATOR . $name;
			$filePathDst = 'tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'MultiImage' . \DIRECTORY_SEPARATOR . md5(random_int(0, 9999)) . substr($name, \strpos($name, '.'));
			\copy($filePathSrc, $filePathDst);
			$fileObj = \App\Fields\File::loadFromPath($filePathDst);
			$hash[$i] = $fileObj->generateHash(true, $filePathDst);
			$attach[$i] = [
				'name' => $name,
				'size' => $fileObj->getSize(),
				'key' => $hash[$i],
				'path' => $fileObj->getPath(),
			];
		}
		$recordModel->set('imagename', \App\Json::encode($attach));
		$recordModel->save();
		$recordModel = \Vtiger_Record_Model::getInstanceById($recordModel->getId(), 'Products');
		$fieldModel = $recordModel->getField('imagename');
		$data = \App\Json::decode(\App\Purifier::decodeHtml($fieldModel->getUITypeModel()->getDisplayValueEncoded($recordModel->get('imagename'), $recordModel->getId(), $fieldModel->getFieldInfo()['limit'])));
		$this->assertNotEmpty($data);
		foreach (self::$files as $i => $name) {
			$this->assertSame($name, $data[$i]['name'], 'File name should be equal');
			$this->assertSame($attach[$i]['size'], $data[$i]['size'], 'File size should be equal');
			$this->assertFileExists($attach[$i]['path'], 'File should exists');
			$this->assertSame($attach[$i]['key'], $data[$i]['key'], 'Key should be equal');
			parse_str(\parse_url($data[$i]['imageSrc'])['query'], $url);
			$this->assertSame('Products', $url['module'], 'Module in image url should be equal to provided');
			$this->assertSame('imagename', $url['field'], 'Field name in image url should be equal to provided');
			$this->assertSame($data[$i]['key'], $url['key'], 'Key in image url should be equal to provided');
			$this->assertSame((string) $recordModel->getId(), $url['record'], 'Record in image url should be equal to provided');
		}
		$recordModel->delete();
		foreach ($attach as $info) {
			$this->assertFileDoesNotExist($info['path'], 'File should be removed');
		}
	}

	/**
	 * Cleaning after tests.
	 */
	public static function tearDownAfterClass(): void
	{
		\vtlib\Functions::recurseDelete('tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'MultiImage' . \DIRECTORY_SEPARATOR, true);
	}
}
