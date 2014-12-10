<?

namespace UniPlug\MetaCaptcha;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class System {

	public static function getInfo() {
		return array(
			"TITLE"       => Loc::getMessage("UNIPLUG_METACAPTCHA_SYSTEM_TITLE"),
			"DESCRIPTION" => Loc::getMessage("UNIPLUG_METACAPTCHA_SYSTEM_DESCRIPTION"),
			"CLASS"       => __CLASS__,
			"CONFIG_URL"  => '/bitrix/admin/captcha.php',
		);
	}

	public static function getCaptcha() {
		include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/captcha.php");

		$captcha = new \CCaptcha();
		$captcha->SetCode();
		$SID = $captcha->GetSID();

		$arResult = array(
			"SID"  => $SID,
			"IMG"  => array(
				"SRC" => '/bitrix/tools/captcha.php?captcha_code=' . $SID,
			),
			"HTML" => false,
		);

		return $arResult;
	}

	public static function checkCaptcha($captcha_sid, $captcha_word) {
		include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/captcha.php");
		$captcha = new \CCaptcha();

		return $captcha->CheckCode($captcha_word, $captcha_sid);
	}

}
