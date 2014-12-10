<?

namespace UniPlug;

use \Bitrix\Main\Config\Option;

class MetaCaptcha {
	const MODULE_ID = 'uniplug.metacaptcha';
	private $backend = false;
	const BACKEND_DEFAULT = 'UniPlug\MetaCaptcha\System';

	protected static $instance;

	private function __construct() {
		$backend = Option::get(self::MODULE_ID, 'backend');
		if ( !empty($backend) ) {
			$this->setBackend($backend);
		} else {
			$this->setBackend();
		}
	}

	/**
	 * @static
	 * @return \UniPlug\MetaCaptcha
	 */
	public static function getInstance() {
		if ( !isset(self::$instance) ) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	/**
	 * @return array Array of installed captcha
	 */
	public function getList() {
		$arResult = array();
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$arrList = $eventManager->findEventHandlers(self::MODULE_ID, "getInfo");

		foreach ($arrList as $arInfoEvent) {
			$arInfo = ExecuteModuleEventEx($arInfoEvent);
			$arResult[$arInfo["CLASS"]] = $arInfo;
		}

		return $arResult;
	}

	/**
	 * @param string $strBackend Backend (class) of captcha to use, default is system captcha
	 *
	 * @return bool Result if setting backend
	 */
	public function setBackend($strBackend = self::BACKEND_DEFAULT) {
		$arBackend = $this->getList();
		if ( !empty($arBackend[$strBackend]) ) {
			$this->backend = $arBackend[$strBackend];

			return true;
		}

		return false;
	}

	/**
	 * @param string $strMethod Method to call
	 * @param array $arParams Params to send
	 *
	 * @return bool|mixed
	 */
	private function callBackend($strMethod, $arParams = array()) {
		if ( empty($strMethod) || !isset($this->backend["CLASS"]) || !class_exists($this->backend["CLASS"]) ) {
			return false;
		}
		$callback = $this->backend["CLASS"] . '::' . $strMethod;
		if ( !is_callable($callback) ) {
			return false;
		}
		return call_user_func_array($callback, $arParams);
	}

	/**
	 * @param array $arParams Params to send
	 *
	 * @return bool|array false or array of captcha
	 */
	public function getCaptcha($arParams = array()) {
		return $this->callBackend("getCaptcha", $arParams);
	}

	/**
	 * @param string $captcha_sid Captcha SID
	 * @param string $captcha_word Check word
	 *
	 * @return bool
	 */
	public function checkCaptcha($captcha_sid, $captcha_word) {
		return $this->callBackend("checkCaptcha", array($captcha_sid, $captcha_word));
	}

}
