<?php

/**
 * String formatting test class file.
 *
 * @package   Tests
 *
 * @copyright YetiForce S.A.
 * @license   YetiForce Public License 6.5 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 */

namespace Tests\Base;

/**
 * String formatting test class.
 */
class Z_StringFormatting extends \Tests\Base
{
	/**
	 * @var string Decimal numbers separator
	 */
	public static $separatorDecimal;
	/**
	 * @var string Numbers grouping separator
	 */
	public static $separatorGrouping;
	/**
	 * @var string Currency symbol placement
	 */
	public static $symbolPlacement;
	/**
	 * @var string Numbers grouping pattern
	 */
	public static $patternGrouping;
	/**
	 * @var int Decimal places count
	 */
	public static $decimalNum;
	/**
	 * @var bool Truncate zeros in decimal numbers
	 */
	public static $truncateTrailingZeros;
	/**
	 * @var array Possible combinations cache
	 */
	public static $combinations = [];

	/**
	 * Store current user preferences.
	 *
	 * @codeCoverageIgnore
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();
		$userModel = \App\User::getCurrentUserModel();
		self::$separatorDecimal = $userModel->getDetail('currency_decimal_separator');
		self::$separatorGrouping = $userModel->getDetail('currency_grouping_separator');
		self::$symbolPlacement = $userModel->getDetail('currency_symbol_placement');
		self::$patternGrouping = $userModel->getDetail('currency_grouping_pattern');
		self::$decimalNum = $userModel->getDetail('no_of_currency_decimals');
		self::$truncateTrailingZeros = $userModel->getDetail('truncate_trailing_zeros');
	}

	/**
	 * Data provider for the numbers formatting test.
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function providerNumbers()
	{
		$combinations = [];
		foreach (
			[
				'integer',
				'double',
			] as $type) {
			$method = 'append' . \ucfirst($type);
			if (\method_exists($this, $method)) {
				$this->{$method}($combinations);
			} else {
				$this->fail('Unsupported field type: ' . \ucfirst($type));
			}
		}
		return $combinations;
	}

	/**
	 * Generate list of possible combinations.
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function getCombinations()
	{
		if (!self::$combinations) {
			$query = (new \App\Db\Query())->select(
				[
					'decimal_separator' => 'vtiger_currency_decimal_separator.currency_decimal_separator',
					'grouping_pattern' => 'vtiger_currency_grouping_pattern.currency_grouping_pattern',
					'grouping_separator' => 'vtiger_currency_grouping_separator.currency_grouping_separator',
					'symbol_placement' => 'vtiger_currency_symbol_placement.currency_symbol_placement',
					'decimals' => 'vtiger_no_of_currency_decimals.no_of_currency_decimals',
				]
			)->from('vtiger_currency_decimal_separator')->join('cross join', 'vtiger_currency_grouping_pattern')->join('cross join', 'vtiger_currency_grouping_separator')->join('cross join', 'vtiger_currency_symbol_placement')->join('cross join', 'vtiger_no_of_currency_decimals')->createCommand()->query();
			while ($combination = $query->read()) {
				if ($combination['grouping_separator'] !== $combination['decimal_separator']) {
					self::$combinations[] = $combination;
				}
			}
		}
		return self::$combinations;
	}

	/**
	 * Append integer validation data sets to test combinations.
	 *
	 * @param $combinations
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function appendInteger(&$combinations)
	{
		$dbFormat = '123456789';
		$fieldData = (new \App\Db\Query())->from('vtiger_field')->where(['uitype' => 7])->one();
		foreach ($this->getCombinations() as $combination) {
			$usrFormatTruncated = $usrFormat = \str_replace(',', $combination['grouping_separator'], $combination['grouping_pattern']);
			$combinations[] = [
				\App\Module::getModuleName($fieldData['tabid']),
				$fieldData['fieldname'],
				$usrFormatTruncated,
				$dbFormat,
				$combination['decimal_separator'],
				$combination['grouping_separator'],
				$combination['grouping_pattern'],
				$combination['decimals'],
				$combination['symbol_placement'],
				true,
				true,
			];
			$combinations[] = [
				\App\Module::getModuleName($fieldData['tabid']),
				$fieldData['fieldname'],
				$usrFormat,
				$dbFormat,
				$combination['decimal_separator'],
				$combination['grouping_separator'],
				$combination['grouping_pattern'],
				$combination['decimals'],
				$combination['symbol_placement'],
				false,
				true,
			];
		}
		return $combinations;
	}

	/**
	 * Append double validation data sets to test combinations.
	 *
	 * @param $combinations
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function appendDouble(&$combinations)
	{
		$int = '123456789';
		$fieldData = (new \App\Db\Query())->from('vtiger_field')->where(['uitype' => 7, 'typeofdata' => 'NN~O'])->one();
		foreach ($this->getCombinations() as $combination) {
			$decimals = \substr(12312, 0, $combination['decimals']);
			$dbFormat = $int . '.' . 12312;
			$usrFormatTruncated = $usrFormat = \str_replace(',', $combination['grouping_separator'], $combination['grouping_pattern']);
			if ($decimals) {
				$usrFormat .= $combination['decimal_separator'] . $decimals;
				$usrFormatTruncated .= $combination['decimal_separator'] . rtrim($decimals, '0');
			}
			$combinations[] = [
				\App\Module::getModuleName($fieldData['tabid']),
				$fieldData['fieldname'],
				$usrFormat,
				$dbFormat,
				$combination['decimal_separator'],
				$combination['grouping_separator'],
				$combination['grouping_pattern'],
				$combination['decimals'],
				$combination['symbol_placement'],
				true,
				true,
			];
			$combinations[] = [
				\App\Module::getModuleName($fieldData['tabid']),
				$fieldData['fieldname'],
				$usrFormatTruncated,
				$dbFormat,
				$combination['decimal_separator'],
				$combination['grouping_separator'],
				$combination['grouping_pattern'],
				$combination['decimals'],
				$combination['symbol_placement'],
				false,
				true,
			];
			$decimals = \substr(10000, 0, $combination['decimals']);
			$dbFormat = $int . '.' . 10000;
			$usrFormatTruncated = $usrFormat = \str_replace(',', $combination['grouping_separator'], $combination['grouping_pattern']);
			if ($decimals) {
				$usrFormat .= $combination['decimal_separator'] . $decimals;
				$usrFormatTruncated .= $combination['decimal_separator'] . rtrim($decimals, '0');
			}
			$combinations[] = [
				\App\Module::getModuleName($fieldData['tabid']),
				$fieldData['fieldname'],
				$usrFormatTruncated,
				$dbFormat,
				$combination['decimal_separator'],
				$combination['grouping_separator'],
				$combination['grouping_pattern'],
				$combination['decimals'],
				$combination['symbol_placement'],
				true,
				true,
			];
			$combinations[] = [
				\App\Module::getModuleName($fieldData['tabid']),
				$fieldData['fieldname'],
				$usrFormat,
				$dbFormat,
				$combination['decimal_separator'],
				$combination['grouping_separator'],
				$combination['grouping_pattern'],
				$combination['decimals'],
				$combination['symbol_placement'],
				false,
				true,
			];
		}
		return $combinations;
	}

	/**
	 * Numbers conversion tests.
	 *
	 * @param string $moduleName        Module name
	 * @param string $fieldName         Field name
	 * @param string $userFormat        Value in user format
	 * @param string $dbFormat          Value in database format
	 * @param string $decimalSeparator  Char used as decimal separator in string
	 * @param string $groupingSeparator Char used to separate groups in string
	 * @param string $groupingPattern   Pattern for grouping
	 * @param int    $afterDot          Number of chars after decimal separator
	 * @param string $symbolPlacement   Currency symbol placement in user format
	 * @param bool   $truncate          Truncate zeros after decimal separator
	 * @param bool   $correct           Test should be successfull
	 * @dataProvider providerNumbers
	 */
	public function testNumbers($moduleName, $fieldName, $userFormat, $dbFormat, $decimalSeparator, $groupingSeparator, $groupingPattern, $afterDot, $symbolPlacement, $truncate, $correct = true): void
	{
		$userModel = \Vtiger_Record_Model::getInstanceById(\App\User::getCurrentUserId(), 'Users');
		$userModel->set('currency_decimal_separator', $decimalSeparator);
		$userModel->set('currency_grouping_separator', $groupingSeparator);
		$userModel->set('currency_symbol_placement', $symbolPlacement);
		$userModel->set('currency_grouping_pattern', $groupingPattern);
		$userModel->set('no_of_currency_decimals', $afterDot);
		$userModel->set('truncate_trailing_zeros', $truncate ? '1' : '0');
		$userModel->save();
		$recordModel = \Vtiger_Record_Model::getCleanInstance($moduleName);
		$recordModel->set($fieldName, $dbFormat);
		$this->assertSame($userFormat, $recordModel->getDisplayValue($fieldName), 'Display value different than expected ' . $recordModel->get($fieldName) . " !== $dbFormat | userFormat: $userFormat | fieldName: $fieldName | getDisplayValue: " . $recordModel->getDisplayValue($fieldName));
		$this->assertSame($dbFormat, $recordModel->get($fieldName), 'Database value different than expected | ' . $dbFormat . ' !== ' . $recordModel->get($fieldName));
	}

	/**
	 * Restore current user preferences.
	 *
	 * @throws \Exception
	 */
	public static function tearDownAfterClass(): void
	{
		$userModel = \Vtiger_Record_Model::getInstanceById(\App\User::getCurrentUserId(), 'Users');
		$userModel->set('currency_decimal_separator', self::$separatorDecimal);
		$userModel->set('currency_grouping_separator', self::$separatorGrouping);
		$userModel->set('currency_symbol_placement', self::$symbolPlacement);
		$userModel->set('currency_grouping_pattern', self::$patternGrouping);
		$userModel->set('no_of_currency_decimals', self::$decimalNum);
		$userModel->set('truncate_trailing_zeros', self::$truncateTrailingZeros);
		$userModel->save();
		parent::tearDownAfterClass();
	}
}
