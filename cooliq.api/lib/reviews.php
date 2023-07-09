<?
namespace Cooliq\Api;

class Reviews
{
  //const REVIEWS_IBLOCK_ID = \COption::GetOptionString('cooliq.api', 'REVIEWS_IBLOCK');

  public static function getReviewsList($propCity, $pageSize = 100)
  {
    $rsElements = \CIBlockElement::GetList(
      Array(),
      Array('ACTIVE' => 'Y', '?PROPERTY_CITY_VALUE' => $propCity),
      false,
      Array('nPageSize' => $pageSize),
      Array('IBLOCK_ID', 'ID', 'NAME', 'DETAIL_TEXT', 'PROPERTY_*')
    );

    while ($obElement = $rsElements->GetNextElement())
    {
      $arFields = $obElement->GetFields();  
      $arProps = $obElement->GetProperties();
      $arResult['LIST'][$arFields['ID']]['FIELDS'] = $arFields;
      $arResult['LIST'][$arFields['ID']]['PROPERTIES']['RATING'] = $arProps['RATING']['VALUE'];
      $arResult['LIST'][$arFields['ID']]['PROPERTIES']['CITY'] = $propCity;
    }
    
    return $arResult;
  }
}