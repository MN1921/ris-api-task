<?php

require_once("/app/lib/socks.php");
require_once("/app/lib/uuid.php");
require_once("/app/lib/sql.php");
require_once("/app/lib/enums/queryCottonPartOperation.php");
require_once("/app/lib/enums/color.php");
require_once("/app/lib/checks.php");

use function is_null as isNull;
use function http_response_code as httpResponseCode;
use function array_push as arrayPush;

// api/socks?color=black&operation=lessThan?cottonPart=10

$queryParameters = [
    "color" => $_GET["color"] ?? null,
    "cottonPartOperation" => $_GET["operation"] ?? null,
    "cottonPart" => $_GET["cottonPart"] ?? null,
];


try {
    if (!isNull($queryParameters["color"])) {
        $queryParameters["color"] = colorFromQueryParameters($queryParameters["color"]);
    }
    if (!isNull($queryParameters["cottonPart"])) {
        $queryParameters["cottonPart"] = checkCottonPart($queryParameters["cottonPart"]);
    }
    if (!isNull($queryParameters["cottonPartOperation"])) {
        $queryParameters["cottonPartOperation"] = queryCottonPartOperation::from($queryParameters["cottonPartOperation"]);
    }
} catch (ValueError $e) {
    httpResponseCode(400);
    exit();
}

$data = selectWarehouseSocksQuantityWithParameters($queryParameters);

// response
header("content-type: application/json; charset=utf-8");
$json = json_encode($data, JSON_PRETTY_PRINT);
echo $json;
exit();

?>