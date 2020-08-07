<?

/**
 * Автозагрузка классов из папки lib/
 * PSR-0
 * @param $className
 */
function dev_site_autoload($className)
{
    $sModuleId = basename(dirname(__FILE__));
    $className = ltrim($className, '\\');
    $arParts = explode('\\', $className);

    $sModuleCheck = strtolower($arParts[0] . '.' . $arParts[1]);

    if ($sModuleCheck != $sModuleId)
        return;

    $arParts = array_splice($arParts, 2);
    if (!empty($arParts)) {
        $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $arParts) . '.php';
        if (file_exists($fileName))
            require_once $fileName;
    }
}

spl_autoload_register('dev_site_autoload');
