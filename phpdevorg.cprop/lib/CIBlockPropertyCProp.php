<?php

use \Bitrix\Main\Localization\Loc;

class CIBlockPropertyCProp
{
    private static $showedCss = false;
    private static $showedJs = false;

    public function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'C',
            'DESCRIPTION' => Loc::getMessage('IEX_CPROP_DESC'),
            'GetPropertyFieldHtml' => array(__CLASS__,  'GetPropertyFieldHtml'),
            'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
            'ConvertFromDB' => array(__CLASS__,  'ConvertFromDB'),
            'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
            'PrepareSettings' => array(__CLASS__, 'PrepareUserSettings'),
            'GetLength' => array(__CLASS__, 'GetLength'),
            'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML')
        );
    }

    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $hideText = Loc::getMessage('IEX_CPROP_HIDE_TEXT');
        $clearText = Loc::getMessage('IEX_CPROP_CLEAR_TEXT');

        self::showCss();
        self::showJs();

        if(!empty($arProperty['USER_TYPE_SETTINGS'])){
            $arFields = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);
        }
        else{
            return '<span>'.Loc::getMessage('IEX_CPROP_ERROR_INCORRECT_SETTINGS').'</span>';
        }

        $result = '';
        $result .= '<div class="mf-gray"><a class="cl mf-toggle">'.$hideText.'</a>';
        if($arProperty['MULTIPLE'] === 'Y'){
            $result .= ' | <a class="cl mf-delete">'.$clearText.'</a></div>';
        }
        $result .= '<table class="mf-fields-list active">';


        foreach ($arFields as $code => $arItem){
            if($arItem['TYPE'] === 'string'){
                $result .= self::showString($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'file'){
                $result .= self::showFile($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'text'){
                $result .= self::showTextarea($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'date'){
                $result .= self::showDate($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'element'){
                $result .= self::showBindElement($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
        }

        $result .= '</table>';

        return $result;
    }

    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return $value;
    }

    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $btnAdd = Loc::getMessage('IEX_CPROP_SETTING_BTN_ADD');
        $settingsTitle =  Loc::getMessage('IEX_CPROP_SETTINGS_TITLE');

        $arPropertyFields = array(
            'USER_TYPE_SETTINGS_TITLE' => $settingsTitle,
            'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'SMART_FILTER', 'WITH_DESCRIPTION', 'FILTRABLE', 'MULTIPLE_CNT', 'IS_REQUIRED'),
            'SET' => array(
                'MULTIPLE_CNT' => 1,
                'SMART_FILTER' => 'N',
                'FILTRABLE' => 'N',
            ),
        );

        self::showJsForSetting($strHTMLControlName["NAME"]);
        self::showCssForSetting();

        $result = '<tr><td colspan="2" align="center">
            <table id="many-fields-table" class="many-fields-table internal">        
                <tr valign="top" class="heading mf-setting-title">
                   <td>XML_ID</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_TITLE').'</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_SORT').'</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_TYPE').'</td>
                </tr>';


        $arSetting = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);

        if(!empty($arSetting)){
            foreach ($arSetting as $code => $arItem) {
                $result .= '
                       <tr valign="top">
                           <td><input type="text" class="inp-code" size="20" value="'.$code.'"></td>
                           <td><input type="text" class="inp-title" size="35" name="'.$strHTMLControlName["NAME"].'['.$code.'_TITLE]" value="'.$arItem['TITLE'].'"></td>
                           <td><input type="text" class="inp-sort" size="5" name="'.$strHTMLControlName["NAME"].'['.$code.'_SORT]" value="'.$arItem['SORT'].'"></td>
                           <td>
                                <select class="inp-type" name="'.$strHTMLControlName["NAME"].'['.$code.'_TYPE]">
                                    '.self::getOptionList($arItem['TYPE']).'
                                </select>                        
                           </td>
                       </tr>';
            }
        }

        $result .= '
               <tr valign="top">
                    <td><input type="text" class="inp-code" size="20"></td>
                    <td><input type="text" class="inp-title" size="35"></td>
                    <td><input type="text" class="inp-sort" size="5" value="500"></td>
                    <td>
                        <select class="inp-type"> '.self::getOptionList().'</select>                        
                    </td>
               </tr>
             </table>   
                
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="button" value="'.$btnAdd.'" onclick="addNewRows()">
                    </td>
                </tr>
                </td></tr>';

        return $result;
    }

    public static function PrepareUserSettings($arProperty)
    {
        $result = [];
        if(!empty($arProperty['USER_TYPE_SETTINGS'])){
            foreach ($arProperty['USER_TYPE_SETTINGS'] as $code => $value) {
                $result[$code] = $value;
            }
        }
        return $result;
    }

    public static function GetLength($arProperty, $arValue)
    {
        $arFields = self::prepareSetting(unserialize($arProperty['USER_TYPE_SETTINGS']));

        $result = false;
        foreach($arValue['VALUE'] as $code => $value){
            if($arFields[$code]['TYPE'] === 'file'){
                if(!empty($value['name']) || (!empty($value['OLD']) && empty($value['DEL']))){
                    $result = true;
                    break;
                }
            }
            else{
                if(!empty($value)){
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    public function ConvertToDB($arProperty, $arValue)
    {
        $arFields = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);

        foreach($arValue['VALUE'] as $code => $value){
            if($arFields[$code]['TYPE'] === 'file'){
                $arValue['VALUE'][$code] = self::prepareFileToDB($value);
            }
        }

        $isEmpty = true;
        foreach ($arValue['VALUE'] as $v){
            if(!empty($v)){
                $isEmpty = false;
                break;
            }
        }

        if($isEmpty === false){
            $arResult['VALUE'] = json_encode($arValue['VALUE']);
        }
        else{
            $arResult = ['VALUE' => '', 'DESCRIPTION' => ''];
        }

        return $arResult;
    }

    public function ConvertFromDB($arProperty, $arValue)
    {
        $return = array();

        if(!empty($arValue['VALUE'])){
            $arData = json_decode($arValue['VALUE'], true);

            foreach ($arData as $code => $value){
                $return['VALUE'][$code] = $value;
            }

        }
        return $return;
    }

    //Internals

    private static function showString($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                    <td align="right">'.$title.': </td>
                    <td><input type="text" value="'.$v.'" name="'.$strHTMLControlName['VALUE'].'['.$code.']"/></td>
                </tr>';

        return $result;
    }

    private static function showFile($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        if(!empty($arValue['VALUE'][$code]) && !is_array($arValue['VALUE'][$code])){
            $fileId = $arValue['VALUE'][$code];
        }
        else if(!empty($arValue['VALUE'][$code]['OLD'])){
            $fileId = $arValue['VALUE'][$code]['OLD'];
        }
        else{
            $fileId = '';
        }

        if(!empty($fileId))
        {
            $arPicture = CFile::GetByID($fileId)->Fetch();
            if($arPicture)
            {
                $strImageStorePath = COption::GetOptionString('main', 'upload_dir', 'upload');
                $sImagePath = '/'.$strImageStorePath.'/'.$arPicture['SUBDIR'].'/'.$arPicture['FILE_NAME'];
                $fileType = self::getExtension($sImagePath);

                if(in_array($fileType, ['png', 'jpg', 'jpeg', 'gif'])){
                    $content = '<img src="'.$sImagePath.'">';
                }
                else{
                    $content = '<div class="mf-file-name">'.$arPicture['FILE_NAME'].'</div>';
                }

                $result = '<tr>
                        <td align="right" valign="top">'.$title.': </td>
                        <td>
                            <table class="mf-img-table">
                                <tr>
                                    <td>'.$content.'<br>
                                        <div>
                                            <label><input name="'.$strHTMLControlName['VALUE'].'['.$code.'][DEL]" value="Y" type="checkbox"> '. Loc::getMessage("IEX_CPROP_FILE_DELETE") . '</label>
                                            <input name="'.$strHTMLControlName['VALUE'].'['.$code.'][OLD]" value="'.$fileId.'" type="hidden">
                                        </div>
                                    </td>
                                </tr>
                            </table>                      
                        </td>
                    </tr>';
            }
        }
        else{
            $result .= '<tr>
                    <td align="right">'.$title.': </td>
                    <td><input type="file" value="" name="'.$strHTMLControlName['VALUE'].'['.$code.']"/></td>
                </tr>';
        }

        return $result;
    }

    public static function showTextarea($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                    <td align="right" valign="top">'.$title.': </td>
                    <td><textarea rows="8" name="'.$strHTMLControlName['VALUE'].'['.$code.']">'.$v.'</textarea></td>
                </tr>';

        return $result;
    }

    public static function showDate($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                        <td align="right" valign="top">'.$title.': </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="padding: 0;">
                                        <div class="adm-input-wrap adm-input-wrap-calendar">
                                            <input class="adm-input adm-input-calendar" type="text" name="'.$strHTMLControlName['VALUE'].'['.$code.']" size="23" value="'.$v.'">
                                            <span class="adm-calendar-icon"
                                                  onclick="BX.calendar({node: this, field:\''.$strHTMLControlName['VALUE'].'['.$code.']\', form: \'\', bTime: true, bHideTime: false});"></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';

        return $result;
    }

    public static function showBindElement($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';

        $elUrl = '';
        if(!empty($v)){
            $arElem = \CIBlockElement::GetList([], ['ID' => $v],false, ['nPageSize' => 1], ['ID', 'IBLOCK_ID', 'IBLOCK_TYPE_ID', 'NAME'])->Fetch();
            if(!empty($arElem)){
                $elUrl .= '<a target="_blank" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.$arElem['IBLOCK_ID'].'&ID='.$arElem['ID'].'&type='.$arElem['IBLOCK_TYPE_ID'].'">'.$arElem['NAME'].'</a>';
            }
        }

        $result .= '<tr>
                    <td align="right">'.$title.': </td>
                    <td>
                        <input name="'.$strHTMLControlName['VALUE'].'['.$code.']" id="'.$strHTMLControlName['VALUE'].'['.$code.']" value="'.$v.'" size="8" type="text" class="mf-inp-bind-elem">
                        <input type="button" value="..." onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=ru&IBLOCK_ID=0&n='.$strHTMLControlName['VALUE'].'&k='.$code.'\', 900, 700);">&nbsp;
                        <span>'.$elUrl.'</span>
                    </td>
                </tr>';

        return $result;
    }

    private static function showCss()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .cl {cursor: pointer;}
                .mf-gray {color: #797777;}
                .mf-fields-list {display: none; padding-top: 10px; margin-bottom: 10px!important; margin-left: -300px!important; border-bottom: 1px #e0e8ea solid!important;}
                .mf-fields-list.active {display: block;}
                .mf-fields-list td {padding-bottom: 5px;}
                .mf-fields-list td:first-child {width: 300px; color: #616060;}
                .mf-fields-list td:last-child {padding-left: 5px;}
                .mf-fields-list input[type="text"] {width: 350px!important;}
                .mf-fields-list textarea {min-width: 350px; max-width: 650px; color: #000;}
                .mf-fields-list img {max-height: 150px; margin: 5px 0;}
                .mf-img-table {background-color: #e0e8e9; color: #616060; width: 100%;}
                .mf-fields-list input[type="text"].adm-input-calendar {width: 170px!important;}
                .mf-file-name {word-break: break-word; padding: 5px 5px 0 0; color: #101010;}
                .mf-fields-list input[type="text"].mf-inp-bind-elem {width: unset!important;}
            </style>
            <?
        }
    }

    private static function showJs()
    {
        $showText = Loc::getMessage('IEX_CPROP_SHOW_TEXT');
        $hideText = Loc::getMessage('IEX_CPROP_HIDE_TEXT');

        CJSCore::Init(array("jquery"));
        if(!self::$showedJs) {
            self::$showedJs = true;
            ?>
            <script>
                $(document).on('click', 'a.mf-toggle', function (e) {
                    e.preventDefault();

                    var table = $(this).closest('tr').find('table.mf-fields-list');
                    $(table).toggleClass('active');
                    if($(table).hasClass('active')){
                        $(this).text('<?=$hideText?>');
                    }
                    else{
                        $(this).text('<?=$showText?>');
                    }
                });

                $(document).on('click', 'a.mf-delete', function (e) {
                    e.preventDefault();

                    var textInputs = $(this).closest('tr').find('input[type="text"]');
                    $(textInputs).each(function (i, item) {
                        $(item).val('');
                    });

                    var textarea = $(this).closest('tr').find('textarea');
                    $(textarea).each(function (i, item) {
                        $(item).text('');
                    });

                    var checkBoxInputs = $(this).closest('tr').find('input[type="checkbox"]');
                    $(checkBoxInputs).each(function (i, item) {
                        $(item).attr('checked', 'checked');
                    });

                    $(this).closest('tr').hide('slow');
                });
            </script>
            <?
        }
    }

    private static function showJsForSetting($inputName)
    {
        CJSCore::Init(array("jquery"));
        ?>
        <script>
            function addNewRows() {
                $("#many-fields-table").append('' +
                    '<tr valign="top">' +
                    '<td><input type="text" class="inp-code" size="20"></td>' +
                    '<td><input type="text" class="inp-title" size="35"></td>' +
                    '<td><input type="text" class="inp-sort" size="5" value="500"></td>' +
                    '<td><select class="inp-type"><?=self::getOptionList()?></select></td>' +
                    '</tr>');
            }


            $(document).on('change', '.inp-code', function(){
                var code = $(this).val();

                if(code.length <= 0){
                    $(this).closest('tr').find('input.inp-title').removeAttr('name');
                    $(this).closest('tr').find('input.inp-sort').removeAttr('name');
                    $(this).closest('tr').find('select.inp-type').removeAttr('name');
                }
                else{
                    $(this).closest('tr').find('input.inp-title').attr('name', '<?=$inputName?>[' + code + '_TITLE]');
                    $(this).closest('tr').find('input.inp-sort').attr('name', '<?=$inputName?>[' + code + '_SORT]');
                    $(this).closest('tr').find('select.inp-type').attr('name', '<?=$inputName?>[' + code + '_TYPE]');
                }
            });

            $(document).on('input', '.inp-sort', function(){
                var num = $(this).val();
                $(this).val(num.replace(/[^0-9]/gim,''));
            });
        </script>
        <?
    }

    private static function showCssForSetting()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .many-fields-table {margin: 0 auto; /*display: inline;*/}
                .mf-setting-title td {text-align: center!important; border-bottom: unset!important;}
                .many-fields-table td {text-align: center;}
                .many-fields-table > input, .many-fields-table > select{width: 90%!important;}
                .inp-sort{text-align: center;}
                .inp-type{min-width: 125px;}
            </style>
            <?
        }
    }

    private static function prepareSetting($arSetting)
    {
        $arResult = [];

        foreach ($arSetting as $key => $value){
            if(strstr($key, '_TITLE') !== false) {
                $code = str_replace('_TITLE', '', $key);
                $arResult[$code]['TITLE'] = $value;
            }
            else if(strstr($key, '_SORT') !== false) {
                $code = str_replace('_SORT', '', $key);
                $arResult[$code]['SORT'] = $value;
            }
            else if(strstr($key, '_TYPE') !== false) {
                $code = str_replace('_TYPE', '', $key);
                $arResult[$code]['TYPE'] = $value;
            }
        }

        if(!function_exists('cmp')){
            function cmp($a, $b)
            {
                if ($a['SORT'] == $b['SORT']) {
                    return 0;
                }
                return ($a['SORT'] < $b['SORT']) ? -1 : 1;
            }
        }

        uasort($arResult, 'cmp');

        return $arResult;
    }

    private static function getOptionList($selected = 'string')
    {
        $result = '';
        $arOption = [
            'string' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_STRING'),
            'file' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_FILE'),
            'text' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_TEXT'),
            'date' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_DATE'),
            'element' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_ELEMENT')
        ];

        foreach ($arOption as $code => $name){
            $s = '';
            if($code === $selected){
                $s = 'selected';
            }

            $result .= '<option value="'.$code.'" '.$s.'>'.$name.'</option>';
        }

        return $result;
    }

    private static function prepareFileToDB($arValue)
    {
        $result = false;

        if(!empty($arValue['DEL']) && $arValue['DEL'] === 'Y' && !empty($arValue['OLD'])){
            CFile::Delete($arValue['OLD']);
        }
        else if(!empty($arValue['OLD'])){
            $result = $arValue['OLD'];
        }
        else if(!empty($arValue['name'])){
            $result = CFile::SaveFile($arValue, 'vote');
        }

        return $result;
    }

    private static function getExtension($filePath)
    {
        return array_pop(explode('.', $filePath));
    }
}