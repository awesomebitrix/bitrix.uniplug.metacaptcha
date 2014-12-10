<?

IncludeModuleLangFile(__FILE__);

Class uniplug_metacaptcha extends CModule {
	const MODULE_ID = 'uniplug.metacaptcha';
	var $MODULE_ID = 'uniplug.metacaptcha';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	public $NEED_MODULES = array("main" => "14.0.0");

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("UNIPLUG_METACAPTCHA_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("UNIPLUG_METACAPTCHA_MODULE_DESC");

		$this->PARTNER_NAME = "UniPlug Ltd.";
		$this->PARTNER_URI = "http://uniplug.ru/";
	}

	private function checkVersion() {
		/** @global CMain $APPLICATION */
		global $APPLICATION;
		if ( is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES) ) {
			foreach ($this->NEED_MODULES as $module => $version) {
				$module = strtolower($module);
				if ( !IsModuleInstalled($module) ) {
					$APPLICATION->ThrowException(GetMessage('UNIPLUG_MODULES_NEED_INSTALL', array('#MODULE#' => $module, '#VERSION#' => $version)));

					return false;
				} else {
					$info = \CModule::CreateModuleObject($module);
					if ( !$info || !CheckVersion($info->MODULE_VERSION, $version) ) {
						$APPLICATION->ThrowException(GetMessage('UNIPLUG_MODULES_NEED_UPDATE', array('#MODULE#' => $module, '#VERSION#' => $version)));

						return false;
					}
				}
			}
		}

		return true;
	}

	function DoInstall() {
		if ( !$this->checkVersion() ) {
			return false;
		}

		RegisterModule(self::MODULE_ID);

		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->registerEventHandler(self::MODULE_ID, 'getInfo', self::MODULE_ID, '\UniPlug\MetaCaptcha\System', 'getInfo', 1);

		return true;
	}

	function DoUninstall() {
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->unRegisterEventHandler(self::MODULE_ID, 'getInfo', self::MODULE_ID, '\UniPlug\MetaCaptcha\System', 'getInfo');

		UnRegisterModule(self::MODULE_ID);

		return true;
	}
}
