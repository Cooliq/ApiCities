<?
class cooliq_api extends CModule
{
  var $MODULE_ID = "cooliq.api";
  var $MODULE_VERSION;
  var $MODULE_VERSION_DATE;
  var $MODULE_NAME;
  var $MODULE_DESCRIPTION;

  function __construct()
  {
    $this->MODULE_VERSION = "1.0.0";
    $this->MODULE_VERSION_DATE = "08.07.2023";
    $this->MODULE_NAME = "Cooliq Cities";
    $this->MODULE_DESCRIPTION = "Отзывы для городов";
  }

  function DoInstall()
  {
    RegisterModule($this->MODULE_ID);
    $this->InstallEvents();
    return true;
  }

  function DoUninstall()
  {
    UnRegisterModule($this->MODULE_ID);
    $this->UnInstallEvents();
    return true;
  }

  function InstallEvents()
  {
    $eventManager = \Bitrix\Main\EventManager::getInstance();
    $eventManager->registerEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Cooliq\\Api\\PropertyCity', 'OnIBlockPropertyBuildList');
    return true;
  }

  function UnInstallEvents()
  {
    $eventManager = \Bitrix\Main\EventManager::getInstance();
    $eventManager->unRegisterEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Cooliq\\Api\\PropertyCity', 'OnIBlockPropertyBuildList');
    return true;
  }
}