<?

class dev_site extends CModule
{
    const MODULE_ID = 'dev.site';

    public $MODULE_ID = 'dev.site',
        $MODULE_VERSION,
        $MODULE_VERSION_DATE,
        $MODULE_NAME = 'Тренировочный модуль',
        $PARTNER_NAME = 'dev';

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . 'version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    function InstallFiles($arParams = array())
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallFiles();
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
    }
}
