<?php


require_once("/app/lib/enums/color.php");
require_once("/app/lib/enums/warehouseOperationType.php");
require_once("/app/lib/uuid.php");
require_once("/app/lib/socks.php");
require_once("/app/lib/sql.php");
require_once("/app/lib/checks.php");


use function is_null as isNull;
use function json_encode as jsonEncode;
use function json_decode as jsonDecode;
use function file_get_contents as fileGetContent;
use function http_response_code as httpResponseCode;


$json = fileGetContent("php://input");

$data = jsonDecode($json, true);

try {
    $data["skuName"] ?? throw new ValueError("skuName is null");
    $color = checkColor($data["color"] ?? null);
    $cottonPart = checkCottonPart($data["cottonPart"] ?? null);
    $quantity = checkQuantity($data["quantity"] ?? null);
} catch (ValueError $e) {
    httpResponseCode(400);
    exit();
}

$socks = selectSocks($data);

if (isNull($socks)) {
    $data["skuId"] = uuid();
    $socks = Socks::tryFrom($data);
    insertSocks($socks);
} else {
   $data["skuId"] = $socks->skuId;
}

$data["operationId"] = uuid();
$data["operationType"] = WarehouseOperationType::income->value;
insertWarehouseOperation($data);

header("content-type: application/json; charset=utf-8");
$json = jsonEncode($data, JSON_NUMERIC_CHECK);
echo $json;
exit();

?>