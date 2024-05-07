<?php

/**
 * Cron test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 * @author    Radosław Skrzypczak <r.skrzypczak@yetiforce.com>
 */

namespace Tests\Base;

class G_Cron extends \Tests\Base
{
	/**
	 * Prepare mail config for mail functionalities.
	 *
	 * @codeCoverageIgnore
	 */
	public static function setUpBeforeClass(): void
	{
		if (!empty($_SERVER['YETI_MAIL_PASS'])) {
			$db = \App\Db::getInstance();
			$db->createCommand()
				->insert('roundcube_users', [
					'username' => 'yetiforcetests@yahoo.com',
					'mail_host' => 'imap.mail.yahoo.com',
					'language' => 'en_US',
					'preferences' => '',
					'password' => $_SERVER['YETI_MAIL_PASS'],
					'crm_user_id' => '1',
					'actions' => 'CreatedEmail,CreatedHelpDesk,BindAccounts,BindContacts,BindLeads,BindHelpDesk,BindSSalesProcesses,BindCampaigns,BindCompetition,BindOSSEmployees,BindPartners,BindProject,BindServiceContracts,BindVendors',
				])->execute();
			$db->createCommand()
				->insert('vtiger_ossmailscanner_folders_uid', [
					'user_id' => '1',
					'type' => 'Received',
					'folder' => 'INBOX',
					'uid' => '0',
				])->execute();
		}
		foreach (['EUR', 'USD', 'GBP', 'CNY'] as $value) {
			$row = (new \App\Db\Query())
				->select(['vtiger_currencies.*'])
				->from('vtiger_currencies')
				->leftJoin('vtiger_currency_info', 'vtiger_currencies.currency_code = vtiger_currency_info.currency_code')
				->where(['vtiger_currencies.currency_code' => $value, 'vtiger_currency_info.currency_code' => null])->one();
			if ($row) {
				unset($row['currencyid']);
				$row['conversion_rate'] = 1;
				$row['currency_status'] = 'Active';
				\App\Db::getInstance()->createCommand()->insert('vtiger_currency_info', $row)->execute();
			}
		}
	}

	/**
	 * Cron testing.
	 */
	public function test(): void
	{
		\App\Cron::updateStatus(\App\Cron::STATUS_DISABLED, 'LBL_UPDATER_RECORDS_COORDINATES');
		\App\Cron::updateStatus(\App\Cron::STATUS_DISABLED, 'LBL_UPDATER_COORDINATES');
		\App\Cron::updateStatus(\App\Cron::STATUS_DISABLED, 'LBK_SYSTEM_WARNINGS');
		\App\Cron::updateStatus(\App\Cron::STATUS_DISABLED, 'LBL_MAIL_SCANNER_ACTION');
		require_once 'cron.php';
		$rows = (new \App\Db\Query())->select(['modue' => 'setype', 'rows' => 'count(*)'])->from('vtiger_crmentity')->groupBy('setype')->orderBy(['rows' => SORT_DESC])->all();
		$c = '';
		foreach ($rows as $value) {
			$c .= "{$value['modue']} = {$value['rows']}, | ";
		}
		\file_put_contents(ROOT_DIRECTORY . '/tests/records.log', $c . PHP_EOL, FILE_APPEND);
		$this->assertFalse((new \App\Db\Query())->from('vtiger_cron_task')->where(['status' => 2])->exists());
	}

	/**
	 * Testing last cron start getter.
	 */
	public function testGetLastCronStart(): void
	{
		$module = \Settings_CronTasks_Module_Model::getInstance('Settings:CronTasks');
		$this->assertNotSame(0, $module->getLastCronStart(), 'Last cron start is 0');
	}
}
