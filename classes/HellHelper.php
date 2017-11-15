<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 27. 7. 2017
 * Time: 11:07
 */

class HellHelper
{
    public static function IsNullOrEmptyString($question){
        return (!isset($question) || trim($question)==='');
    }

    public static function remove_accents($text) {
        $trans = array(
            'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A','Ç'=>'C','È'=>'E',
            'É'=>'E','Ê'=>'E','Ë'=>'E','Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I','Ñ'=>'N',
            'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O','Ù'=>'U','Ú'=>'U',
            'Û'=>'U','Ü'=>'U','Ý'=>'Y','à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'å'=>'a','ç'=>'c','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','ì'=>'i','í'=>'i',
            'î'=>'i','ï'=>'i','ñ'=>'n','ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ø'=>'o','ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y','Ā'=>'A',
            'ā'=>'a','Ă'=>'A','ă'=>'a','Ą'=>'A','ą'=>'a','Ć'=>'C','ć'=>'c','Ĉ'=>'C',
            'ĉ'=>'c','Ċ'=>'C','ċ'=>'c','Č'=>'C','č'=>'c','Ď'=>'D','ď'=>'d','Đ'=>'D',
            'đ'=>'d','Ē'=>'E','ē'=>'e','Ĕ'=>'E','ĕ'=>'e','Ė'=>'E','ė'=>'e','Ę'=>'E',
            'ę'=>'e','Ě'=>'E','ě'=>'e','Ĝ'=>'G','ĝ'=>'g','Ğ'=>'G','ğ'=>'g','Ġ'=>'G',
            'ġ'=>'g','Ģ'=>'G','ģ'=>'g','Ĥ'=>'H','ĥ'=>'h','Ħ'=>'H','ħ'=>'h','Ĩ'=>'I',
            'ĩ'=>'i','Ī'=>'I','ī'=>'i','Ĭ'=>'I','ĭ'=>'i','Į'=>'I','į'=>'i','İ'=>'I',
            'ı'=>'i','Ĵ'=>'J','ĵ'=>'j','Ķ'=>'K','ķ'=>'k','Ĺ'=>'L','ĺ'=>'l','Ļ'=>'L',
            'ļ'=>'l','Ľ'=>'L','ľ'=>'l','Ŀ'=>'L','ŀ'=>'l','Ł'=>'L','ł'=>'l','Ń'=>'N',
            'ń'=>'n','Ņ'=>'N','ņ'=>'n','Ň'=>'N','ň'=>'n','ŉ'=>'n','Ō'=>'O','ō'=>'o',
            'Ŏ'=>'O','ŏ'=>'o','Ő'=>'O','ő'=>'o','Ŕ'=>'R','ŕ'=>'r','Ŗ'=>'R','ŗ'=>'r',
            'Ř'=>'R','ř'=>'r','Ś'=>'S','ś'=>'s','Ŝ'=>'S','ŝ'=>'s','Ş'=>'S','ş'=>'s',
            'Š'=>'S','š'=>'s','Ţ'=>'T','ţ'=>'t','Ť'=>'T','ť'=>'t','Ŧ'=>'T','ŧ'=>'t',
            'Ũ'=>'U','ũ'=>'u','Ū'=>'U','ū'=>'u','Ŭ'=>'U','ŭ'=>'u','Ů'=>'U','ů'=>'u',
            'Ű'=>'U','ű'=>'u','Ų'=>'U','ų'=>'u','Ŵ'=>'W','ŵ'=>'w','Ŷ'=>'Y','ŷ'=>'y',
            'Ÿ'=>'Y','Ź'=>'Z','ź'=>'z','Ż'=>'Z','ż'=>'z','Ž'=>'Z','ž'=>'z','ƀ'=>'b',
            'Ɓ'=>'B','Ƃ'=>'B','ƃ'=>'b','Ƈ'=>'C','ƈ'=>'c','Ɗ'=>'D','Ƌ'=>'D','ƌ'=>'d',
            'Ƒ'=>'F','ƒ'=>'f','Ɠ'=>'G','Ɨ'=>'I','Ƙ'=>'K','ƙ'=>'k','ƚ'=>'l','Ɲ'=>'N',
            'ƞ'=>'n','Ɵ'=>'O','Ơ'=>'O','ơ'=>'o','Ƥ'=>'P','ƥ'=>'p','ƫ'=>'t','Ƭ'=>'T',
            'ƭ'=>'t','Ʈ'=>'T','Ư'=>'U','ư'=>'u','Ʋ'=>'V','Ƴ'=>'Y','ƴ'=>'y','Ƶ'=>'Z',
            'ƶ'=>'z','ǅ'=>'D','ǈ'=>'L','ǋ'=>'N','Ǎ'=>'A','ǎ'=>'a','Ǐ'=>'I','ǐ'=>'i',
            'Ǒ'=>'O','ǒ'=>'o','Ǔ'=>'U','ǔ'=>'u','Ǖ'=>'U','ǖ'=>'u','Ǘ'=>'U','ǘ'=>'u',
            'Ǚ'=>'U','ǚ'=>'u','Ǜ'=>'U','ǜ'=>'u','Ǟ'=>'A','ǟ'=>'a','Ǡ'=>'A','ǡ'=>'a',
            'Ǥ'=>'G','ǥ'=>'g','Ǧ'=>'G','ǧ'=>'g','Ǩ'=>'K','ǩ'=>'k','Ǫ'=>'O','ǫ'=>'o',
            'Ǭ'=>'O','ǭ'=>'o','ǰ'=>'j','ǲ'=>'D','Ǵ'=>'G','ǵ'=>'g','Ǹ'=>'N','ǹ'=>'n',
            'Ǻ'=>'A','ǻ'=>'a','Ǿ'=>'O','ǿ'=>'o','Ȁ'=>'A','ȁ'=>'a','Ȃ'=>'A','ȃ'=>'a',
            'Ȅ'=>'E','ȅ'=>'e','Ȇ'=>'E','ȇ'=>'e','Ȉ'=>'I','ȉ'=>'i','Ȋ'=>'I','ȋ'=>'i',
            'Ȍ'=>'O','ȍ'=>'o','Ȏ'=>'O','ȏ'=>'o','Ȑ'=>'R','ȑ'=>'r','Ȓ'=>'R','ȓ'=>'r',
            'Ȕ'=>'U','ȕ'=>'u','Ȗ'=>'U','ȗ'=>'u','Ș'=>'S','ș'=>'s','Ț'=>'T','ț'=>'t',
            'Ȟ'=>'H','ȟ'=>'h','Ƞ'=>'N','ȡ'=>'d','Ȥ'=>'Z','ȥ'=>'z','Ȧ'=>'A','ȧ'=>'a',
            'Ȩ'=>'E','ȩ'=>'e','Ȫ'=>'O','ȫ'=>'o','Ȭ'=>'O','ȭ'=>'o','Ȯ'=>'O','ȯ'=>'o',
            'Ȱ'=>'O','ȱ'=>'o','Ȳ'=>'Y','ȳ'=>'y','ȴ'=>'l','ȵ'=>'n','ȶ'=>'t','ȷ'=>'j',
            'Ⱥ'=>'A','Ȼ'=>'C','ȼ'=>'c','Ƚ'=>'L','Ⱦ'=>'T','ȿ'=>'s','ɀ'=>'z','Ƀ'=>'B',
            'Ʉ'=>'U','Ɇ'=>'E','ɇ'=>'e','Ɉ'=>'J','ɉ'=>'j','ɋ'=>'q','Ɍ'=>'R','ɍ'=>'r',
            'Ɏ'=>'Y','ɏ'=>'y','ɓ'=>'b','ɕ'=>'c','ɖ'=>'d','ɗ'=>'d','ɟ'=>'j','ɠ'=>'g',
            'ɦ'=>'h','ɨ'=>'i','ɫ'=>'l','ɬ'=>'l','ɭ'=>'l','ɱ'=>'m','ɲ'=>'n','ɳ'=>'n',
            'ɵ'=>'o','ɼ'=>'r','ɽ'=>'r','ɾ'=>'r','ʂ'=>'s','ʄ'=>'j','ʈ'=>'t','ʉ'=>'u',
            'ʋ'=>'v','ʐ'=>'z','ʑ'=>'z','ʝ'=>'j','ʠ'=>'q','ͣ'=>'a','ͤ'=>'e','ͥ'=>'i',
            'ͦ'=>'o','ͧ'=>'u','ͨ'=>'c','ͩ'=>'d','ͪ'=>'h','ͫ'=>'m','ͬ'=>'r','ͭ'=>'t',
            'ͮ'=>'v','ͯ'=>'x','ᵢ'=>'i','ᵣ'=>'r','ᵤ'=>'u','ᵥ'=>'v','ᵬ'=>'b','ᵭ'=>'d',
            'ᵮ'=>'f','ᵯ'=>'m','ᵰ'=>'n','ᵱ'=>'p','ᵲ'=>'r','ᵳ'=>'r','ᵴ'=>'s','ᵵ'=>'t',
            'ᵶ'=>'z','ᵻ'=>'i','ᵽ'=>'p','ᵾ'=>'u','ᶀ'=>'b','ᶁ'=>'d','ᶂ'=>'f','ᶃ'=>'g',
            'ᶄ'=>'k','ᶅ'=>'l','ᶆ'=>'m','ᶇ'=>'n','ᶈ'=>'p','ᶉ'=>'r','ᶊ'=>'s','ᶌ'=>'v',
            'ᶍ'=>'x','ᶎ'=>'z','ᶏ'=>'a','ᶑ'=>'d','ᶒ'=>'e','ᶖ'=>'i','ᶙ'=>'u','᷊'=>'r',
            'ᷗ'=>'c','ᷚ'=>'g','ᷜ'=>'k','ᷝ'=>'l','ᷠ'=>'n','ᷣ'=>'r','ᷤ'=>'s','ᷦ'=>'z',
            'Ḁ'=>'A','ḁ'=>'a','Ḃ'=>'B','ḃ'=>'b','Ḅ'=>'B','ḅ'=>'b','Ḇ'=>'B','ḇ'=>'b',
            'Ḉ'=>'C','ḉ'=>'c','Ḋ'=>'D','ḋ'=>'d','Ḍ'=>'D','ḍ'=>'d','Ḏ'=>'D','ḏ'=>'d',
            'Ḑ'=>'D','ḑ'=>'d','Ḓ'=>'D','ḓ'=>'d','Ḕ'=>'E','ḕ'=>'e','Ḗ'=>'E','ḗ'=>'e',
            'Ḙ'=>'E','ḙ'=>'e','Ḛ'=>'E','ḛ'=>'e','Ḝ'=>'E','ḝ'=>'e','Ḟ'=>'F','ḟ'=>'f',
            'Ḡ'=>'G','ḡ'=>'g','Ḣ'=>'H','ḣ'=>'h','Ḥ'=>'H','ḥ'=>'h','Ḧ'=>'H','ḧ'=>'h',
            'Ḩ'=>'H','ḩ'=>'h','Ḫ'=>'H','ḫ'=>'h','Ḭ'=>'I','ḭ'=>'i','Ḯ'=>'I','ḯ'=>'i',
            'Ḱ'=>'K','ḱ'=>'k','Ḳ'=>'K','ḳ'=>'k','Ḵ'=>'K','ḵ'=>'k','Ḷ'=>'L','ḷ'=>'l',
            'Ḹ'=>'L','ḹ'=>'l','Ḻ'=>'L','ḻ'=>'l','Ḽ'=>'L','ḽ'=>'l','Ḿ'=>'M','ḿ'=>'m',
            'Ṁ'=>'M','ṁ'=>'m','Ṃ'=>'M','ṃ'=>'m','Ṅ'=>'N','ṅ'=>'n','Ṇ'=>'N','ṇ'=>'n',
            'Ṉ'=>'N','ṉ'=>'n','Ṋ'=>'N','ṋ'=>'n','Ṍ'=>'O','ṍ'=>'o','Ṏ'=>'O','ṏ'=>'o',
            'Ṑ'=>'O','ṑ'=>'o','Ṓ'=>'O','ṓ'=>'o','Ṕ'=>'P','ṕ'=>'p','Ṗ'=>'P','ṗ'=>'p',
            'Ṙ'=>'R','ṙ'=>'r','Ṛ'=>'R','ṛ'=>'r','Ṝ'=>'R','ṝ'=>'r','Ṟ'=>'R','ṟ'=>'r',
            'Ṡ'=>'S','ṡ'=>'s','Ṣ'=>'S','ṣ'=>'s','Ṥ'=>'S','ṥ'=>'s','Ṧ'=>'S','ṧ'=>'s',
            'Ṩ'=>'S','ṩ'=>'s','Ṫ'=>'T','ṫ'=>'t','Ṭ'=>'T','ṭ'=>'t','Ṯ'=>'T','ṯ'=>'t',
            'Ṱ'=>'T','ṱ'=>'t','Ṳ'=>'U','ṳ'=>'u','Ṵ'=>'U','ṵ'=>'u','Ṷ'=>'U','ṷ'=>'u',
            'Ṹ'=>'U','ṹ'=>'u','Ṻ'=>'U','ṻ'=>'u','Ṽ'=>'V','ṽ'=>'v','Ṿ'=>'V','ṿ'=>'v',
            'Ẁ'=>'W','ẁ'=>'w','Ẃ'=>'W','ẃ'=>'w','Ẅ'=>'W','ẅ'=>'w','Ẇ'=>'W','ẇ'=>'w',
            'Ẉ'=>'W','ẉ'=>'w','Ẋ'=>'X','ẋ'=>'x','Ẍ'=>'X','ẍ'=>'x','Ẏ'=>'Y','ẏ'=>'y',
            'Ẑ'=>'Z','ẑ'=>'z','Ẓ'=>'Z','ẓ'=>'z','Ẕ'=>'Z','ẕ'=>'z','ẖ'=>'h','ẗ'=>'t',
            'ẘ'=>'w','ẙ'=>'y','ẚ'=>'a','Ạ'=>'A','ạ'=>'a','Ả'=>'A','ả'=>'a','Ấ'=>'A',
            'ấ'=>'a','Ầ'=>'A','ầ'=>'a','Ẩ'=>'A','ẩ'=>'a','Ẫ'=>'A','ẫ'=>'a','Ậ'=>'A',
            'ậ'=>'a','Ắ'=>'A','ắ'=>'a','Ằ'=>'A','ằ'=>'a','Ẳ'=>'A','ẳ'=>'a','Ẵ'=>'A',
            'ẵ'=>'a','Ặ'=>'A','ặ'=>'a','Ẹ'=>'E','ẹ'=>'e','Ẻ'=>'E','ẻ'=>'e','Ẽ'=>'E',
            'ẽ'=>'e','Ế'=>'E','ế'=>'e','Ề'=>'E','ề'=>'e','Ể'=>'E','ể'=>'e','Ễ'=>'E',
            'ễ'=>'e','Ệ'=>'E','ệ'=>'e','Ỉ'=>'I','ỉ'=>'i','Ị'=>'I','ị'=>'i','Ọ'=>'O',
            'ọ'=>'o','Ỏ'=>'O','ỏ'=>'o','Ố'=>'O','ố'=>'o','Ồ'=>'O','ồ'=>'o','Ổ'=>'O',
            'ổ'=>'o','Ỗ'=>'O','ỗ'=>'o','Ộ'=>'O','ộ'=>'o','Ớ'=>'O','ớ'=>'o','Ờ'=>'O',
            'ờ'=>'o','Ở'=>'O','ở'=>'o','Ỡ'=>'O','ỡ'=>'o','Ợ'=>'O','ợ'=>'o','Ụ'=>'U',
            'ụ'=>'u','Ủ'=>'U','ủ'=>'u','Ứ'=>'U','ứ'=>'u','Ừ'=>'U','ừ'=>'u','Ử'=>'U',
            'ử'=>'u','Ữ'=>'U','ữ'=>'u','Ự'=>'U','ự'=>'u','Ỳ'=>'Y','ỳ'=>'y','Ỵ'=>'Y',
            'ỵ'=>'y','Ỷ'=>'Y','ỷ'=>'y','Ỹ'=>'Y','ỹ'=>'y','Ỿ'=>'Y','ỿ'=>'y','ⁱ'=>'i',
            'ⁿ'=>'n','ₐ'=>'a','ₑ'=>'e','ₒ'=>'o','ₓ'=>'x','⒜'=>'a','⒝'=>'b','⒞'=>'c',
            '⒟'=>'d','⒠'=>'e','⒡'=>'f','⒢'=>'g','⒣'=>'h','⒤'=>'i','⒥'=>'j','⒦'=>'k',
            '⒧'=>'l','⒨'=>'m','⒩'=>'n','⒪'=>'o','⒫'=>'p','⒬'=>'q','⒭'=>'r','⒮'=>'s',
            '⒯'=>'t','⒰'=>'u','⒱'=>'v','⒲'=>'w','⒳'=>'x','⒴'=>'y','⒵'=>'z','Ⓐ'=>'A',
            'Ⓑ'=>'B','Ⓒ'=>'C','Ⓓ'=>'D','Ⓔ'=>'E','Ⓕ'=>'F','Ⓖ'=>'G','Ⓗ'=>'H','Ⓘ'=>'I',
            'Ⓙ'=>'J','Ⓚ'=>'K','Ⓛ'=>'L','Ⓜ'=>'M','Ⓝ'=>'N','Ⓞ'=>'O','Ⓟ'=>'P','Ⓠ'=>'Q',
            'Ⓡ'=>'R','Ⓢ'=>'S','Ⓣ'=>'T','Ⓤ'=>'U','Ⓥ'=>'V','Ⓦ'=>'W','Ⓧ'=>'X','Ⓨ'=>'Y',
            'Ⓩ'=>'Z','ⓐ'=>'a','ⓑ'=>'b','ⓒ'=>'c','ⓓ'=>'d','ⓔ'=>'e','ⓕ'=>'f','ⓖ'=>'g',
            'ⓗ'=>'h','ⓘ'=>'i','ⓙ'=>'j','ⓚ'=>'k','ⓛ'=>'l','ⓜ'=>'m','ⓝ'=>'n','ⓞ'=>'o',
            'ⓟ'=>'p','ⓠ'=>'q','ⓡ'=>'r','ⓢ'=>'s','ⓣ'=>'t','ⓤ'=>'u','ⓥ'=>'v','ⓦ'=>'w',
            'ⓧ'=>'x','ⓨ'=>'y','ⓩ'=>'z','Ⱡ'=>'L','ⱡ'=>'l','Ɫ'=>'L','Ᵽ'=>'P','Ɽ'=>'R',
            'ⱥ'=>'a','ⱦ'=>'t','Ⱨ'=>'H','ⱨ'=>'h','Ⱪ'=>'K','ⱪ'=>'k','Ⱬ'=>'Z','ⱬ'=>'z',
            'Ɱ'=>'M','ⱱ'=>'v','Ⱳ'=>'W','ⱳ'=>'w','ⱴ'=>'v','ⱸ'=>'e','ⱺ'=>'o','ⱼ'=>'j',
            'Ꝁ'=>'K','ꝁ'=>'k','Ꝃ'=>'K','ꝃ'=>'k','Ꝅ'=>'K','ꝅ'=>'k','Ꝉ'=>'L','ꝉ'=>'l',
            'Ꝋ'=>'O','ꝋ'=>'o','Ꝍ'=>'O','ꝍ'=>'o','Ꝑ'=>'P','ꝑ'=>'p','Ꝓ'=>'P','ꝓ'=>'p',
            'Ꝕ'=>'P','ꝕ'=>'p','Ꝗ'=>'Q','ꝗ'=>'q','Ꝙ'=>'Q','ꝙ'=>'q','Ꝛ'=>'R','ꝛ'=>'r',
            'Ꝟ'=>'V','ꝟ'=>'v','Ａ'=>'A','Ｂ'=>'B','Ｃ'=>'C','Ｄ'=>'D','Ｅ'=>'E','Ｆ'=>'F',
            'Ｇ'=>'G','Ｈ'=>'H','Ｉ'=>'I','Ｊ'=>'J','Ｋ'=>'K','Ｌ'=>'L','Ｍ'=>'M','Ｎ'=>'N',
            'Ｏ'=>'O','Ｐ'=>'P','Ｑ'=>'Q','Ｒ'=>'R','Ｓ'=>'S','Ｔ'=>'T','Ｕ'=>'U','Ｖ'=>'V',
            'Ｗ'=>'W','Ｘ'=>'X','Ｙ'=>'Y','Ｚ'=>'Z','ａ'=>'a','ｂ'=>'b','ｃ'=>'c','ｄ'=>'d',
            'ｅ'=>'e','ｆ'=>'f','ｇ'=>'g','ｈ'=>'h','ｉ'=>'i','ｊ'=>'j','ｋ'=>'k','ｌ'=>'l',
            'ｍ'=>'m','ｎ'=>'n','ｏ'=>'o','ｐ'=>'p','ｑ'=>'q','ｒ'=>'r','ｓ'=>'s','ｔ'=>'t',
            'ｕ'=>'u','ｖ'=>'v','ｗ'=>'w','ｘ'=>'x','ｙ'=>'y','ｚ'=>'z',);
        return strtr($text, $trans);
    }

    public static function getAttribute($attributeNameValue, $attributes){
        foreach ($attributes as $attribute) {
            $dColor = $attribute['name'];
            if ($dColor == $attributeNameValue) {
                return $attribute;
            }
        }

        //Create new attribute
        return null;
    }

    public static function transferColor($index){
        $tab = array(
            '00' => 'unknown',
            '01' => 'white',
            '02' => 'black',
            '03' => 'blue',
            '04' => 'red',
            '05' => 'pink',
            '06' => 'yellow',
            '07' => 'orange',
            '08' => 'grey',
            '09' => 'green',
            '10' => 'purple',
            '11' => 'beige',
        );
        $tab = array(
            '00' => 'Neznámá',
            '01' => 'Bílá',
            '02' => 'Černá',
            '03' => 'Modrá',
            '04' => 'Červená',
            '05' => 'Růžová',
            '06' => 'Žlutá',
            '07' => 'Oranžová',
            '08' => 'Šedá',
            '09' => 'Zelená',
            '10' => 'Fialová',
            '11' => 'Béžová',
        );
        return $tab[$index];
    }

    public static function transferCut($cut_from_vario){
        $tab = array(
            'D' => 'Female',
            'P' => 'Male',
            'U' => 'Unisex'
        );
        $tab = array(
            'D' => 'Dámské',
            'P' => 'Pánské',
            'U' => 'Unisex'
        );
        return $tab[$cut_from_vario];
    }

    public static function getCsLanguage(){
        $languages = Language::getLanguages();

        foreach ($languages as $language) {
            $isoCode = $language['iso_code'];
            if ($isoCode == 'cs'){
                return $language['id_lang'];
            }
        }

        throw new Exception('Nenalezena cestina');
    }

    public static function getEnLanguage(){
        $languages = Language::getLanguages();

        foreach ($languages as $language) {
            $isoCode = $language['iso_code'];
            if ($isoCode == 'en' OR $isoCode == 'gb'){
                return $language['id_lang'];
            }
        }

        throw new Exception('Nenalezena anglictina');
    }

    public static function generateSlug($input, $sexType = null){
        $input = $input . ' ' . $sexType;
        $input = self::remove_accents($input);
        $output = trim($input);
        $output = str_replace(' ', '-', $output);
        $output = str_replace('.', '', $output);
        $output = str_replace(',', '', $output);
        $output = str_replace('´', '', $output);
        $output = str_replace('\'', '', $output);
        $output = str_replace('~', '', $output);
        $output = str_replace('&', 'AND', $output);
        $output = strtolower($output);
        return $output;
    }

    public static function removeLastItem($inputString, $delimiter){
        $array = explode($delimiter, $inputString);
        $str = '';
        foreach ($array as $item) {
            if ($item == $array[count($array) - 1]) {
                break;
            }
            $str .= $item . '/';
        }
        return $str;
    }

    public static function convertCarrierNameToVarioID($reference_id)
    {
        $reference_id = intval($reference_id);

        $path = dirname(__FILE__) . '/../configuration/carrier.json';

        if (!file_exists($path)){
            $mapTable = array();

            array_push($mapTable, self::createStdClass(null, 'Česká pošta - Balík do ruky dobírka',    'E67DC21E-1C48-49FB-BC57-567E057DAF4D'));
            array_push($mapTable, self::createStdClass(null, 'Česká pošta - Balík na poštu dobírka',   '401E4433-0201-4ECA-8F50-D976C55B96CE'));
            array_push($mapTable, self::createStdClass(null, 'Česká pošta - Balík do ruky',            '1CEA970D-C0F1-44AD-892C-C421891184DD'));
            array_push($mapTable, self::createStdClass(null, 'Česká pošta - Balík na poštu',           '53885AF5-D0EC-4DC0-A961-48592127D5EF'));
            array_push($mapTable, self::createStdClass(null, 'Česká pošta - Balíkomat',                '07CAFA08-18ED-4FA0-836C-CF03F1962A78'));
            array_push($mapTable, self::createStdClass(null, 'Česká pošta - Balíkomat dobírka',        '1EEDB2EF-B91C-4B2C-8D33-A5FC64B71BBD'));
            array_push($mapTable, self::createStdClass(null, 'Osobní odběr Zásilkovna',                'DDDA1718-747D-4C7F-9E83-9DBAEFD665AF'));
            array_push($mapTable, self::createStdClass(null, 'Osobní odběr Zásilkovna dobírka',        'A25BDE3C-32D8-4A12-A687-C9D54201980A'));
            array_push($mapTable, self::createStdClass(null, 'Osobní odběr Zásilkovna SK',             '71644B7B-695A-430C-A4E6-225AEB7B9CBD'));
            array_push($mapTable, self::createStdClass(null, 'Osobní odběr Zásilkovna SK dob.',        '0EEF8E61-B2B7-4594-9968-BB5E5BA557A7'));
            array_push($mapTable, self::createStdClass(null, 'Slovenská pošta',                        'E025D2D8-2323-4741-B729-60A8783CE230'));
            array_push($mapTable, self::createStdClass(null, 'Slovenská pošta dobírka',                '3BBDD819-A2B0-40FE-A7EE-F774FBFC0F05'));

            $json = json_encode($mapTable, JSON_UNESCAPED_UNICODE);
            $f = fopen($path, 'a+');
            fwrite($f, print_r($json, true) . PHP_EOL);
            fclose($f);
        }else{
            $strJson = file_get_contents($path);
            $strJson = trim($strJson);

            $mapTable = json_decode($strJson, true);

            $resultStr = '';
            $flag = false;
            for ($i = 0; $i < strlen($strJson); $i++){
                $char = $strJson[$i];
                $ascii = ord($char);

                if ($char == "["){
                    $flag = true;
                }

                if ($flag){
                    $resultStr .= $char;
                }
            }

            unlink($path);

            $f = fopen($path, 'a+');
            fwrite($f, print_r($resultStr, true) . PHP_EOL);
            fclose($f);

            $mapTable = json_decode($resultStr, true);

        }

        foreach ($mapTable as $item){
            if ($item["id"] == $reference_id){
                return $item["varioID"];
            }
        }

        return null;
    }

    private static function createStdClass($id, $name, $varioID){
        $result = new stdClass();
        $result->id = $id;
        $result->name = $name;
        $result->varioID = $varioID;
        return $result;
    }
}