<?php

namespace Dev\Site\Handlers;

class Iblock
{
    public function addLog(&$arFields)
    {
        $el = new \CIBlockElement;

        $res = \CIBlock::GetList(
            [],
            [
                "CODE" => 'LOG',
            ]
        )->Fetch();  // в $res['ID'] id инфоблока с кодом ЛОГ

        if ($res['ID'] == $arFields['IBLOCK_ID'])// проверка что добавляем элемент не в ЛОГ
        {
            return;
        }

        $result = \CIBlock::GetList(
            [],
            [
                "ID" => $arFields['IBLOCK_ID'],
            ]
        )->Fetch();
        //  $result['NAME'] имя и
        //  $result['CODE'] код инфоблока, изменение которого логируется

        $list = \CIBlockSection::GetList(
            [],
            ['IBLOCK_ID' => $arFields['IBLOCK_ID']],
            false,
            ['ID', 'IBLOCK_SECTION_ID', 'NAME']
        );
        while ($arSections = $list->GetNext()) {
            $mas[] = $arSections;// массив с разделами исходного инфоблока
        }

        $ITEMS = [];// массив для анонса
        self::myFunc($arFields['IBLOCK_SECTION'][0], $mas, $ITEMS);
        array_unshift($ITEMS, $result['NAME']);
        $ITEMS[] = $arFields['NAME'];


        $db_list = \CIBlockSection::GetList([], ['IBLOCK_ID' => $res['ID'], 'CODE' =>$result['CODE']])->Fetch();

        if (!$db_list)// совпадений нет, создаем раздел и в нем элемент
        {
            //-------создаем раздел--------
            $bs = new \CIBlockSection;
            $arSection = [
                "IBLOCK_ID" => $res['ID'],
                "NAME" => $result['NAME'],
                "CODE" => $result['CODE'],
            ];
            if ($ID = $bs->Add($arSection)) {
                echo "Добавлен раздел в инфоблок LOG : " . $ID . "<br>";
            } else {
                echo "Error: " . $bs->LAST_ERROR . '<br>';
            }
        } else// совпадения есть
        {
            $ID = $result['ID'];
        }

        $getElement = \CIBlockElement::GetList(
            ["SORT" => "ASC"],
            ['IBLOCK_ID' => $res['ID'], 'NAME' => $arFields['ID']],
            false,
            false,
            ['ID', 'NAME']
        )->Fetch();


        if (!$getElement)// если нету то добавляем
        {
            //--------- создаем элемент-------
            $arLoadProduct = [
                "IBLOCK_SECTION_ID" => $ID,
                "IBLOCK_ID" => $res['ID'],
                "NAME" => $arFields['ID'],
                "PREVIEW_TEXT" => implode("->", $ITEMS),
                "ACTIVE_FROM" => date('d.m.Y'),
            ];
            $el->Add($arLoadProduct);

        } else { // иначе изменяем уже существующий
            $arLoadProduct = [
                "PREVIEW_TEXT" => implode("->", $ITEMS),
            ];
            $el->Update($getElement['ID'], $arLoadProduct);
        }

    }

    public function myFunc($n, $arr, &$array)// рекурсивная функция поиска имен разделов
    {
        if ($n == '')// если раздел корневой - return
        {
            return;
        }
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i]['ID'] == $n) {
                array_unshift($array, $arr[$i]['NAME']);
                $n = $arr[$i]['IBLOCK_SECTION_ID'];
                self::myFunc($n, $arr, $array);
            }
        }
    }
}
