<?
use \Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

$strRights = $APPLICATION->GetGroupRight('main');

if ($strRights > 'R') {
	Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
	Loc::loadMessages(__FILE__);

	\Bitrix\Main\Loader::includeModule('uniplug.metacaptcha');

	$obMetaCaptcha = \UniPlug\MetaCaptcha::getInstance();

	$arMess = array();
	$boolError = false;

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['save']) && check_bitrix_sessid()) {
		if( $obMetaCaptcha->setBackend($_POST["BACKEND"]) ) {
			Option::set($obMetaCaptcha::MODULE_ID, 'backend', $_POST["BACKEND"]);
			$arMess[] = Loc::getMessage("UNIPLUG_METACAPTCHA_MESS_SET_BACKEND_SUCCESS");
		} else {
			$arMess[] = Loc::getMessage("UNIPLUG_METACAPTCHA_MESS_SET_BACKEND_ERROR");
			$boolError = true;
		}
	} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_REQUEST['RestoreDefaults']) && check_bitrix_sessid()) {
		Option::set($obMetaCaptcha::MODULE_ID, 'backend', $obMetaCaptcha::BACKEND_DEFAULT);
		$arMess[] = Loc::getMessage('UNIPLUG_METACAPTCHA_MESS_RESET');
	}

	if (!empty($arMess)) {
		CAdminMessage::ShowMessage(array(
			'DETAILS' => (!empty($arMess) ? implode('<br />', $arMess) : ''),
			'HTML' => true,
			'TYPE' => ($boolError ? 'ERROR' : 'OK'),
		));
	}

	$aTabs = array(
		array('DIV' => 'edit1', 'TAB' => Loc::getMessage('UNIPLUG_METACAPTCHA_SETTINGS'), 'ICON' => '', 'TITLE' => Loc::getMessage('UNIPLUG_METACAPTCHA_SETTINGS')),
	);

	$tabControl = new CAdminTabControl('tabControl', $aTabs, true, true);

	$tabControl->Begin();
	$backend = Option::get($obMetaCaptcha::MODULE_ID, 'backend', $obMetaCaptcha::BACKEND_DEFAULT);

	?>
	<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?lang=<?= LANGUAGE_ID ?>&amp;mid=<?= htmlspecialcharsbx($mid) ?>&amp;mid_menu=1">
		<?= bitrix_sessid_post(); ?>
		<? $tabControl->BeginNextTab(); ?>
		<? foreach($obMetaCaptcha->getList() as $arBackend): ?>
		<tr>
			<td width="5%">
				<input type="radio" name="BACKEND" value="<?= $arBackend["CLASS"]?>" id="id-<?= md5($arBackend["CLASS"])?>" <?= $backend == $arBackend["CLASS"] ? 'checked="checked"' : ''?>/>
			</td>
			<td width="30%">
				<label for="id-<?= md5($arBackend["CLASS"])?>"><?= $arBackend["TITLE"] ?></label>:
			</td>
			<td width="60%">
				<?= $arBackend["DESCRIPTION"] ?>
			</td>
			<td width="10%">
				<? if ( !empty($arBackend["CONFIG_URL"]) ): ?>
					<a href="<?= $arBackend["CONFIG_URL"] ?>"><?= Loc::getMessage('UNIPLUG_METACAPTCHA_SETTINGS')?></a>
				<? endif ?>
			</td>
		</tr>
		<? endforeach ?>
		<?
		$tabControl->Buttons();
		?>
		<script type="text/javascript">
			function RestoreDefaults() {
				if (confirm('<?= GetMessageJS('MAIN_HINT_RESTORE_DEFAULTS_WARNING') ?>'))
					window.location = "<?= $APPLICATION->GetCurPage() ?>?RestoreDefaults=Y&lang=<?= LANGUAGE_ID ?>&mid=<?= urlencode($mid) ?>&<?= bitrix_sessid_get() ?>";
			}
		</script>
		<input type="submit" class="adm-btn-save" name="update" value="<?= Loc::getMessage('MAIN_SAVE') ?>">
		<input type="hidden" name="save" value="Y">
		<input type="reset" name="reset" value="<?= Loc::getMessage('MAIN_RESET')?>">
		<input type="button" title="<?= Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS') ?>" onclick="RestoreDefaults();" value="<?= Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>">
	</form>
	<?
	$tabControl->End();
}
?>
