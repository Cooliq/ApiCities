<?
namespace Cooliq\Api;

class PropertyCity
{
   public static function OnIBlockPropertyBuildList()
  {
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'CITIES',
			'DESCRIPTION' => 'Привязка к инфоблоку и элементу',
			'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
      'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
      'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml']
		);
	}

  public static function ConvertToDB ($arProperty, $value)
  {
    $value['VALUE'] = base64_encode(serialize($value['VALUE']));
    return $value;
  }

  public static function ConvertFromDB($arProperty, $value, $format = '')
  {
    $value['VALUE'] = base64_decode($value['VALUE']);
    return $value;
  }

  public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
  {
    $propertyId = 'row_'.substr(md5($arHtmlControl['VALUE']), 0, 10);
    $fieldName =  $arHtmlControl['VALUE'];
    $arValue = unserialize($value['VALUE'], [stdClass::class]);

    $selectSection = '<select id="propSelectSection" class="select-section" name="'.$fieldName.'[SECTION]">';

    \CModule::IncludeModule("iblock");
    $rsSections = \CIBlockSection::GetList(
      Array('NAME' => 'ASC'),
      Array('ACTIVE' => 'Y', 'IBLOCK_ID' => \COption::GetOptionString('cooliq.api', 'IBLOCK_ID')),
      false,
      Array('ID', 'NAME', 'CODE'),
      false
    );

    while ($arSection = $rsSections->GetNext())
    {
      if ($arValue['SECTION'] == $arSection['ID'])
      {
        $selectSection .= '<option value="'.$arSection['ID'].'" selected="selected">'.$arSection['NAME'].'</option>';
      }
      else
      {
        $selectSection .= '<option value="'.$arSection['ID'].'">'.$arSection['NAME'].'</option>';
      }
    }

    $selectSection .= '</select>';

    $selectElement = '<select class="select-element" name="'.$fieldName.'[ELEMENT]">';

    $rsElements = \CIBlockElement::GetList(
      Array('NAME' => 'ASC'),
      Array('ACTIVE' => 'Y', 'IBLOCK_ID' => \COption::GetOptionString('cooliq.api', 'IBLOCK_ID')),
      false,
      false,
      Array('IBLOCK_ID', 'ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID')
    );

    while ($arElements = $rsElements->GetNext())
    {
      if ($arValue['ELEMENT'] == $arElements['NAME'])
      {
        $selectElement .= '<option class="prop-cities-element" data-parent-id="'.$arElements['IBLOCK_SECTION_ID'].'" value="'.$arElements['NAME'].'" selected="selected">'.$arElements['NAME'].'</option>';
      }
      else
      {
        $selectElement .= '<option class="prop-cities-element" data-parent-id="'.$arElements['IBLOCK_SECTION_ID'].'" value="'.$arElements['NAME'].'">'.$arElements['NAME'].'</option>';
      }
    }

    $selectElement .= '</select>';

    $html = '<div class="property_row" id="'.$propertyId.'">';
    $html .= '<div class="city">';
    $html .= $selectSection;
    $html .= $selectElement;
    $html .= '</div>';
    $html .= '</div>';

    $script = '<script>';
    $script .= 'function hideCities() {
      let propCitiesElements = document.querySelectorAll(".prop-cities-element");
      propCitiesElements.forEach(city => {
        if (city.dataset.parentId != document.querySelector("#propSelectSection").value) {
          city.style.display = "none";
          city.disabled = true;
        }
      });
    }
    
    hideCities();

    document.querySelector("#propSelectSection").addEventListener("selectionchange", hideCities());
    ';
    $script .= '</script>';

    $html .= $script;

    return $html;
  }
}