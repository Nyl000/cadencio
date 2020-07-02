<?php

namespace Cadencio\Services;

class CsvUtils
{
    public static function decode($str, $encoding)
    {
        if ($encoding == "auto") {
            $encoding = TextUtils::detect_encoding($str);
            if ($encoding === FALSE) throw new \Exception("Encoding could not be detected");
        }

		if ($encoding == "iso88591") $encoding = "latin1"; // cf. iso88591 not known on mac and windows
		if ($encoding == "utf8") $encoding = "utf-8"; // cf. on windows
		return @iconv($encoding, "utf-8//IGNORE", $str);
    }
}