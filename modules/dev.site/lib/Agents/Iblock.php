<?php

namespace Dev\Site\Agents;


class Iblock
{
    const MAX_ELEMENTS = 10;
    public static function clearOldLogs()
    {

            $ibLogID = \CIBlock::GetList(
                Array(),
                Array(
                    "CODE"=>'LOG'
                )
            )->GetNext();

            $sortElement = \CIBlockElement::GetList(
                ["timestamp_x" => "DESC"],
                ['IBLOCK_ID' => $ibLogID['ID']],
                false,
                false,
                ['ID']
            );
            $j = 0;
            while($ob = $sortElement->GetNextElement())
            {
                $arSelectFields = $ob->GetFields();
                if($j++ > self::MAX_ELEMENTS)
                    \CIBlockElement::Delete($arSelectFields['ID']);

            }


        return "Only\Site\Agents\Iblock::clearOldLogs();";
    }
}
