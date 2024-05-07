<?php
/**
 * RecordConverter test file.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Tests\App;

/**
 * RecordConverter test class.
 */
class RecordConverter extends \Tests\Base
{
	/**
	 * Initialize test.
	 */
	public function testInitialize()
	{
		$dbCommand = \App\Db::getInstance('admin')->createCommand();
		$dbCommand->insert(
			'a_yf_record_converter',
			[
				'name' => 'Record converter test',
				'status' => 1,
				'source_module' => 89,
				'destiny_module' => 90,
				'field_mapping' => \App\Json::encode(
					['auto']
				),
				'inv_field_mapping' => \App\Json::encode(
					['auto']
				),
				'show_in_list' => 1,
				'show_in_detail' => 1,
			]
		)->execute();
		$this->assertTrue(\App\RecordConverter::isActive('SQuotes', 'Detail'));
	}

	/**
	 * Process test.
	 */
	public function testProcess()
	{
		$this->markTestSkipped('unfinished');
		// $id = (new \App\Db\Query())->select(['squotesid'])->from('u_#__squotes')->scalar();
		// if ($id) {
			// $recordModel = \Vtiger_Record_Model::getInstanceById($id, 'SQuotes');
			// $this->logs = [
			// 	'data' => $recordModel->getData(),
			// 	'inventory' => $recordModel->getInventoryData(),
			// ];
			// $convertInstance = \App\RecordConverter::getInstanceById(1);
			// $convertInstance->process([$id]);

			// $this->assertCount(1, $convertInstance->createdRecords);
			// foreach ($convertInstance->createdRecords as $id) {
			// 	$this->assertTrue((new \App\Db\Query())->from('u_yf_ssingleorders')->where(['ssingleordersid' => $id])->exists());
			// }
		// }
	}
}
