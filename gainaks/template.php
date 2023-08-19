<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
   <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <div>
           <a href="<? echo $arItem["DETAIL_PAGE_URL"]; ?>"><? echo $arItem["NAME"]; ?></a><br>
           <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
              alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
              title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>" />
              
	       <p><? echo $arItem["PREVIEW_TEXT"]; ?></p><br>
	       
	       <a href="<? echo $arItem["DETAIL_PAGE_URL"]; ?>">Подробнее</a>
	    </div>    
    <? endforeach; ?>