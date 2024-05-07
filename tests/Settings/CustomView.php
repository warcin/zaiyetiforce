<?php
/**
 * CustomView test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 * @author    Radosław Skrzypczak <r.skrzypczak@yetiforce.com>
 */

namespace Tests\Settings;

class CustomView extends \Tests\Base
{
	/**
	 * Testing module model.
	 */
	public function testModuleModel()
	{
		$moduleModel = \Settings_CustomView_Module_Model::getInstance('Settings:CustomView');
		$cvsData = \App\CustomView::getFiltersByModule('Leads');
		$this->assertIsArray($cvsData, 'Custom views list should be array type');
		$this->assertNotEmpty($cvsData, 'Leads module should contain views');
		$cvData = \array_pop($cvsData);
		$this->assertNotEmpty($cvData, 'Leads custom view record should contain data');
		$recordModel = \CustomView_Record_Model::getInstanceById($cvData['cvid']);

		$this->assertIsArray($moduleModel->getFilterPermissionsView($recordModel->getId(), 'default'), 'Custom view permissions list(default) should be array type');
		$this->assertEmpty($moduleModel->getFilterPermissionsView($recordModel->getId(), 'default'), 'Custom view permissions list(default) should be empty');
		$user = 'Users:' . \App\User::getActiveAdminId();
		$recordModel->setDefaultForMember($user);
		$filterPermsView = $moduleModel->getFilterPermissionsView($recordModel->getId(), 'default');
		$this->assertNotEmpty($filterPermsView, 'Custom view permissions list(default) should be not empty');
		$filterPermsFound = false;
		foreach ($filterPermsView as $val) {
			if (\in_array($user, $val)) {
				$filterPermsFound = true;
			}
		}
		$this->assertTrue($filterPermsFound, 'Created default users filter view entry not found');
		$recordModel->removeDefaultForMember($user);
		$this->assertEmpty($moduleModel->getFilterPermissionsView($recordModel->getId(), 'default'), 'Custom view permissions list(default) should be emptied');
		$this->assertIsArray($moduleModel->getFilterPermissionsView($recordModel->getId(), 'featured'), 'Custom view permissions list(featured) should be array type');
		$this->assertEmpty($moduleModel->getFilterPermissionsView($recordModel->getId(), 'featured'), 'Custom view permissions list(featured) should be empty');
		$recordModel->setFeaturedForMember($user);
		$this->assertNotEmpty($moduleModel->getFilterPermissionsView($recordModel->getId(), 'featured'), 'Custom view permissions list(featured) should be not empty');
		$recordModel->removeFeaturedForMember($user);
		$this->assertEmpty($moduleModel->getFilterPermissionsView($recordModel->getId(), 'featured'), 'Custom view permissions list(featured) should be empty');

		$supportedModules = \Settings_CustomView_Module_Model::getSupportedModules();
		$this->assertNotEmpty($supportedModules, 'System should have any custom view supported modules');
		$this->assertSame($supportedModules[\App\Module::getModuleId('Leads')], 'Leads', 'Module mapping mismatch');
		$this->assertSame('index.php?module=CustomView&view=EditAjax&source_module=Leads&record=115', $moduleModel->getUrlToEdit('Leads', 115), 'Generated edit url mismatch');
		$this->assertSame('index.php?module=CustomView&view=EditAjax&source_module=Leads', $moduleModel->getCreateFilterUrl('Leads'), 'Generated create filter url mismatch');
		$this->assertSame('index.php?module=CustomView&parent=Settings&view=FilterPermissions&type=default&sourceModule=Leads&cvid=115&isDefault=1', $moduleModel->getUrlDefaultUsers('Leads', 115, 1), 'Generated default users url mismatch');
		$this->assertSame('index.php?module=CustomView&parent=Settings&view=FilterPermissions&type=featured&sourceModule=Leads&cvid=115', $moduleModel->getFeaturedFilterUrl('Leads', 115), 'Generated featured filter url mismatch');

		$newCustomViewModel = \CustomView_Record_Model::getCleanInstance();
		$newCustomViewModel->setModule('Leads');
		$customViewData = [
			'viewname' => 'DeleteTest',
			'setdefault' => 0,
			'setmetrics' => 0,
			'status' => 0,
			'featured' => 0,
			'color' => '',
			'description' => 'Record delete test',
			'columnslist' => array_keys(\CustomView_Record_Model::getInstanceById($recordModel->getId())->getSelectedFields()),
		];
		$newCustomViewModel->setData($customViewData);
		$newCustomViewModel->save();
		$newCvid = $newCustomViewModel->getId();
		$this->assertNotNull($newCvid, 'Expected cvid');

		$newCustomViewModel->set('mode', 'edit');
		$newCustomViewModel->set('setdefault', 1);
		$newCustomViewModel->save();

		$leadsDefCvid = (new \App\Db\Query())->select(['cvid'])->from('vtiger_customview')->where(['entitytype' => 'Leads', 'setdefault' => 1])->scalar();
		$this->assertSame($newCustomViewModel->getId(), $leadsDefCvid, 'Default cvid for module Leads mismatch');

		\CustomView_Record_Model::getInstanceById($newCvid)->delete();
		$this->assertEmpty((new \App\Db\Query())->select(['cvid'])->from('vtiger_customview')->where(['cvid' => $newCvid])->scalar(), 'New CustomView should be removed');
	}
}
