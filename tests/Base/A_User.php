<?php

/**
 * AddUser test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 * @author    Radosław Skrzypczak <r.skrzypczak@yetiforce.com>
 */

namespace Tests\Base;

class A_User extends \Tests\Base
{
	/**
	 * The default user password.
	 *
	 * @var string
	 */
	public static $defaultPassrowd = 'Demo12345678T';
	/**
	 * User id.
	 */
	private static $id;

	/**
	 * List of \Users_Record_Model.
	 *
	 * @var \Users_Record_Model[]
	 */
	private static $record = [];

	/**
	 * Create/return users module record model with demo user.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $login
	 *
	 * @throws \Exception
	 *
	 * @return \Users_Record_Model
	 */
	public static function createUsersRecord($login = 'demo')
	{
		if (isset(self::$record[$login])) {
			return self::$record[$login];
		}
		if (($userId = \App\User::getUserIdByName($login))) {
			self::$record[$login] = \Vtiger_Record_Model::getInstanceById($userId, 'Users');
			return self::$record[$login];
		}
		$user = \Vtiger_Record_Model::getCleanInstance('Users');
		$user->set('user_name', $login);
		$user->set('email1', "{$login}@yetiforce.com");
		$user->set('first_name', 'Demo');
		$user->set('last_name', 'YetiForce');
		$user->set('user_password', self::$defaultPassrowd);
		$user->set('confirm_password', self::$defaultPassrowd);
		$user->set('roleid', 'H2');
		$user->set('is_admin', 'on');
		$user->save();
		return self::$record[$login] = $user;
	}

	/**
	 * Testing user creation.
	 */
	public function testLoadBaseUser()
	{
		$db = \App\Db::getInstance();
		$db->createCommand()->update('vtiger_password', ['val' => 4], ['type' => 'min_length'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'big_letters'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'small_letters'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'numbers'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'special'])->execute();
		\App\User::setCurrentUserId(self::createUsersRecord()->getId());
		$this->assertIsInt(self::createUsersRecord()->getId());
	}

	/**
	 * Testing user creation.
	 */
	public function testAddUser()
	{
		$user = \Vtiger_Record_Model::getCleanInstance('Users');
		$user->set('user_name', 'testuser');
		$user->set('email1', 'testuser@yetiforce.com');
		$user->set('first_name', 'Test');
		$user->set('last_name', 'YetiForce');
		$user->set('user_password', 'testuser');
		$user->set('confirm_password', 'testuser');
		$user->set('roleid', 'H2');
		$user->save();
		self::$id = $user->getId();
		$this->assertIsInt(self::$id);
		$row = (new \App\Db\Query())->from('vtiger_users')->where(['id' => self::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . self::$id);
		$this->assertSame($row['user_name'], 'testuser');
		$this->assertSame($row['email1'], 'testuser@yetiforce.com');
		$this->assertSame($row['first_name'], 'Test');
		$this->assertSame($row['last_name'], 'YetiForce');
		$this->assertSame((new \App\Db\Query())->select(['roleid'])->from('vtiger_user2role')->where(['userid' => self::$id])->scalar(), 'H2');
	}

	/**
	 * Testing user edition.
	 */
	public function testEditUser()
	{
		$user = \Vtiger_Record_Model::getInstanceById(self::$id, 'Users');
		$this->assertNotFalse($user, 'No user');
		$user->set('user_name', 'testuseredit');
		$user->set('first_name', 'Test edit');
		$user->set('last_name', 'YetiForce edit');
		$user->set('email1', 'testuser-edit@yetiforce.com');
		$user->set('roleid', 'H1');
		$user->save();
		$row = (new \App\Db\Query())->from('vtiger_users')->where(['id' => self::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . self::$id);
		$this->assertSame($row['user_name'], 'testuseredit');
		$this->assertSame($row['email1'], 'testuser-edit@yetiforce.com');
		$this->assertSame($row['first_name'], 'Test edit');
		$this->assertSame($row['last_name'], 'YetiForce edit');
		$this->assertSame((new \App\Db\Query())->select(['roleid'])->from('vtiger_user2role')->where(['userid' => self::$id])->scalar(), 'H1');
	}

	/**
	 * Testing user deletion.
	 */
	public function testDeleteUser()
	{
		$currentUserModel = \Users_Record_Model::getCurrentUserModel();
		$this->assertNotFalse($currentUserModel, 'No current user');
		\Users_Record_Model::deleteUserPermanently(self::$id, $currentUserModel->getId());
		$this->assertFalse((new \App\Db\Query())->from('vtiger_users')->where(['id' => self::$id])->exists(), 'The record was not removed from the database ID: ' . self::$id);
	}

	/**
	 * Testing locks creation.
	 */
	public function testLocksUser()
	{
		$param = [['user' => 'H6', 'locks' => ['copy', 'paste']]];
		$moduleModel = \Settings_Users_Module_Model::getInstance();
		$this->assertNotNull($moduleModel, 'Object is null');
		$moduleModel->saveLocks($param);
		$this->assertFileExists('user_privileges/locks.php');
		$this->assertSame(['H6' => ['copy', 'paste']], $moduleModel->getLocks(), 'Unexpected value in lock array');
	}

	/**
	 * Testing locks deletion.
	 */
	public function testDeleteLocksUser()
	{
		$param = '';
		$moduleModel = \Settings_Users_Module_Model::getInstance();
		$this->assertNotNull($moduleModel, 'Object is null');
		$moduleModel->saveLocks($param);

		$this->assertFileExists('user_privileges/locks.php');
		$locks = $moduleModel->getLocks();
		$this->assertCount(0, $locks, 'Unexpected value in lock array');
	}
}
