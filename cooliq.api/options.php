<?
use Bitrix\Main\Loader;
use Bitrix\Main\HttpApplication;

$moduleId = 'cooliq.api';
$request = HttpApplication::getInstance()->getContext()->getRequest();
Loader::includeModule($moduleId);
CModule::IncludeModule("iblock");

$arOptionsIblock['default'] = 'Выберите инфболок';
$resIblocks = CIBlock::GetList();
while($arIblock = $resIblocks->Fetch())
{
  $arOptionsIblock[$arIblock['ID']] = $arIblock['NAME'];
}

$aTabs = array(
  array(
    "DIV" => "edit0",
    "TAB" => "Основные настройки",
    "TITLE" => "Основные настройки",
    "OPTIONS" => array(
      array(
        'IBLOCK_ID',
        'Инфоблок городов',
        'default',
        array(
          'selectbox',
          $arOptionsIblock
        )
      ),
      array(
        'REVIEWS_IBLOCK',
        'Инфоблок отзывов',
        'default',
        array(
          'selectbox',
          $arOptionsIblock
        )
      )
    )
  )
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->begin();
?>

<form method="POST" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&mid_menu=1&mid=<?=$moduleId?>">
  <?
  echo bitrix_sessid_post();
  foreach ($aTabs as $aTab)
  {
    if ($aTab['OPTIONS'])
    {
      $tabControl->beginNextTab();
      __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
    }
  }
  $tabControl->buttons();
  ?>
  <input type="submit" name="apply" value="Сохранить" class="adm-btn-save">
  <input type="submit" name="default" value="Сбросить">
</form>

<?
$tabControl->end();

if ($request->isPost() && check_bitrix_sessid())
{
  foreach ($aTabs as $aTab)
  {
    foreach ($aTab['OPTIONS'] as $arOption)
    {
      if (!is_array($arOption))
      {
        continue;
      }
      if ($arOption['note'])
      {
        continue;
      }
      
      if ($request['apply'])
      {
        $optionValue = $request->getPost($arOption[0]);
        COption::SetOptionString($moduleId, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
      }
      elseif ($request['default'])
      {
        COption::SetOptionString($moduleId, $arOption[0], $arOption[2]);
      }
    }
  }

  LocalRedirect($APPLICATION->getCurPage().'?mid='.$moduleId.'&lang='.LANGUAGE_ID);
}
?>