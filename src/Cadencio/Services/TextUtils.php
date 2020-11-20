<?php namespace Cadencio\Services;

define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE) . chr(0xFF));
define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define ('UTF8_BOM'               , chr(0xEF) . chr(0xBB) . chr(0xBF));


class TextUtils {

    /**
     *
     * FROM GoldenLibs Wimm_Utils_Text
     * @param $str
     * @return bool|false|mixed|string
     */
    public static function detect_encoding($str)
    {
        $first2 = substr($str, 0, 2);
        $first3 = substr($str, 0, 3);
        $first4 = substr($str, 0, 3);

		// fb("Detecting encoding from BOM...");
		if ($first3 == UTF8_BOM) return 'utf8';
        elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'utf32be';
        elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'utf32le';
        elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'utf16be';
        elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'utf16le';

		// fb("Detecting encoding with iconv...");
		foreach (array("cp1252","utf8", "iso88591", "mac", "utf16le", "utf16be", "utf32le", "utf32be") as $encoding) {
            $valid_for_encoding = md5(@iconv($encoding, "$encoding//IGNORE", $str)) == md5($str);
            if ($valid_for_encoding) {
                if (in_array($encoding, array("iso88591", "mac"))) {

                    // mac and iso88591 are compatible, so we need to detect which one is actually used
                    // to achieve this we convert to utf8 and strip control characters and keep the one giving the longest result
                    // to work on Mac OS, we should use this, does it work on production server as well ? if so we can keep it
                    // $str_mac_utf8_stripped = preg_replace("/\pC/u", "", @iconv("mac", "utf-8//IGNORE", $str));
                    // $str_iso_utf8_stripped = preg_replace("/\pC/u", "", @iconv("iso8859-1", "utf-8//IGNORE", $str));
                    $str_mac_utf8_stripped = preg_replace("/\pC/u", "", @iconv("mac", "utf8//IGNORE", $str));
                    $str_iso_utf8_stripped = preg_replace("/\pC/u", "", @iconv("iso88591", "utf8//IGNORE", $str));
                    return mb_strlen($str_mac_utf8_stripped, "utf8") > mb_strlen($str_iso_utf8_stripped, "utf8") ? "mac" : "iso88591";
                }
                else return $encoding;
            }
        }

		// fb("Detecting encoding with mb_detect_encoding...");
		$encoding = mb_detect_encoding($str);
		if ($encoding) return strtolower(str_replace("-", "", $encoding));

		return FALSE;
	}
}