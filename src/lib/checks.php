<?php

require_once("/app/lib/enums/color.php");

use function is_null as isNull;
use function preg_match as regMatch;
use function is_int as isInt;


function checkColor($value) {
    if (isNull($value)) {
        throw new ValueError("Value is null");
    }
    return Color::from($value);
}

function checkUuid($value) {
    if (isNull($value)) {
        throw new ValueError("Value is null");
    } 
    $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';
    if (regMatch($pattern, $value)) {
        return $value;
    } else {
        throw new ValueError("Value is not uuid");
    }
}

function checkName($value, $len) {
    $pattern = '/[\;\+\=\!]/';
    if (regMatch($pattern, $value) or mb_strlen($value, "utf8") > $len) {
        throw new ValueError("Value contains invalid characters");
    } else {
        return $value;
    }
}

function checkCottonPart($value) {
    if (!isInt($value)) {
        throw new ValueError("Value is not integer");
    };
    if ($value >= 0 and $value <= 100) {
        return $value;
    } else {
        throw new ValueError("Value must be >= 0 and <= 100");
    }
}

function checkQuantity($value) {
    if (!isInt($value)) {
        throw new ValueError("Value is not integer");
    };
    if ($value > 0) {
        return $value;
    } else {
        throw new ValueError("Value must be > 0");
    }
}

?>