<?php

require_once("/app/lib/database.php");
require_once("/app/lib/socks.php");
require_once("/app/lib/enums/warehouseOperationType.php");
require_once("/app/lib/enums/queryCottonPartOperation.php");

use function is_null as isNull;
use function array_push as arrayPush;

function selectSocks($data) {
    $database = getDatabase();
    $sql = $database->prepareSql('
        SELECT "skuId", "skuName", "color", "cottonPart"
        FROM "Socks" WHERE "skuName" = :skuName AND "color" = :color AND "cottonPart" = :cottonPart LIMIT 1;
    ');
    $sql->bindParam(":skuName", $data["skuName"]);
    $sql->bindParam(":color", $data["color"]);
    $sql->bindParam(":cottonPart", $data["cottonPart"]);
    $sql->execute();
    $row = $sql->fetch();
    
    $data = [
        "skuId" => $row["skuId"] ?? null, 
        "skuName" => $row["skuName"] ?? null, 
        "color" => $row["color"] ?? null, 
        "cottonPart" => $row["cottonPart"] ?? null,
    ];
    
    return Socks::tryFrom($data);
}

function insertSocks($socks) {
    if (isNull($socks)) {
        return false;
    }
    $data = $socks->getData();
    $database = getDatabase();
    $sql = $database->prepareSql('
        INSERT INTO "Socks" VALUES (:skuId, :skuName, :color, :cottonPart);
    ');
    $sql->bindParam(":skuId", $data["skuId"]);
    $sql->bindParam(":skuName", $data["skuName"]);
    $sql->bindParam(":color", $data["color"]);
    $sql->bindParam(":cottonPart", $data["cottonPart"]);
    $database->pdo->beginTransaction();
    $sql->execute();
    $database->pdo->commit();
    return true;
}

function insertWarehouseOperation($data) {
    $database = getDatabase();
    $sql = $database->prepareSql('
        INSERT INTO "Warehouse" VALUES (:operationId, :skuId, :quantity, :operationType);
    ');
    $sql->bindParam(":operationId", $data["operationId"]);
    $sql->bindParam(":skuId", $data["skuId"]);
    $sql->bindParam(":quantity", $data["quantity"]);
    $sql->bindParam(":operationType", $data["operationType"]);
    $database->pdo->beginTransaction();
    $sql->execute();
    $database->pdo->commit();
    return true;
}

function selectWarehouseSocksQuantity() {
    $database = getDatabase();
    $sql = $database->prepareSql('
        WITH "Warehouse" AS (
            SELECT 
                "skuId",
                SUM(CASE WHEN "operationType" = :operationTypeIncome THEN "quantity" ELSE 0 END) AS "income",
                SUM(CASE WHEN "operationType" = :operationTypeOutcome THEN "quantity" ELSE 0 END) AS "outcome"
            FROM "Warehouse" GROUP BY "skuId"
        )
        SELECT
            "Warehouse"."skuId" AS "skuId",
            "Socks"."skuName" AS "skuName", 
            "Socks"."color" AS "color",
            "Socks"."cottonPart" AS "cottonPart",
            "Warehouse"."income" - "Warehouse"."outcome" as "quantity"
        FROM "Warehouse"
        LEFT JOIN "Socks" ON "Socks"."skuId" = "Warehouse"."skuId";
    ');
    $operationTypeIncome = WarehouseOperationType::income->value;
    $operationTypeOutcome = WarehouseOperationType::outcome->value;
    $sql->bindParam(":operationTypeIncome", $operationTypeIncome);
    $sql->bindParam(":operationTypeOutcome", $operationTypeOutcome);
    $sql->execute();
    $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
    $data = [];
    foreach ($rows as $row) {
        arrayPush($data, [
            "skuId" => $row["skuId"],
            "skuName" => $row["skuName"],
            "color" => $row["color"],
            "cottonPart" => $row["cottonPart"],
            "quantity" => $row["quantity"],
        ]);
    }
    return $data;
}

function selectWarehouseSocksQuantityWithParameters($queryParameters) {
    
    $q = chr(34);
    $where = ";";
    $conditionColor = null;
    $conditionCottonPart = null;

    if (!isNull($queryParameters["color"])) {
        
        $conditionColor = "{$q}Socks{$q}.{$q}color{$q} = '{$queryParameters["color"]->value}'";
    }

    if (!isNull($queryParameters["cottonPartOperation"]) && !isNull($queryParameters["cottonPart"])) {
       
        $cottonPartOperation = null;
        switch ($queryParameters["cottonPartOperation"]) {
            case QueryCottonPartOperation::moreThan:
                $cottonPartOperation = ">";
                break;
            case QueryCottonPartOperation::lessThan:
                $cottonPartOperation = "<";
                break;
            case QueryCottonPartOperation::equal:
                $cottonPartOperation = "=";
                break;
        }
        $conditionCottonPart = "{$q}Socks{$q}.{$q}cottonPart{$q} {$cottonPartOperation} {$queryParameters["cottonPart"]}";
    }

    if (!isNull($conditionColor)) {
        $where = "WHERE {$conditionColor};";
    }

    if (!isNull($conditionCottonPart)) {
        $where = "WHERE {$conditionCottonPart};";
    }

    if (!isNull($conditionColor) && !isNull($conditionCottonPart)) {
        $where = "WHERE {$conditionColor} AND {$conditionCottonPart};";
    }
   
    $database = getDatabase();
    $sql = $database->prepareSql('
        WITH "Warehouse" AS (
            SELECT 
                "skuId",
                SUM(CASE WHEN "operationType" = :operationTypeIncome THEN "quantity" ELSE 0 END) AS "income",
                SUM(CASE WHEN "operationType" = :operationTypeOutcome THEN "quantity" ELSE 0 END) AS "outcome"
            FROM "Warehouse" GROUP BY "skuId"
        )
        SELECT
            "Warehouse"."skuId" AS "skuId",
            "Socks"."skuName" AS "skuName", 
            "Socks"."color" AS "color",
            "Socks"."cottonPart" AS "cottonPart",
            "Warehouse"."income" - "Warehouse"."outcome" as "quantity"
        FROM "Warehouse"
        LEFT JOIN "Socks" ON "Socks"."skuId" = "Warehouse"."skuId"
    '.$where);

    $operationTypeIncome = WarehouseOperationType::income->value;
    $operationTypeOutcome = WarehouseOperationType::outcome->value;
    $sql->bindParam(":operationTypeIncome", $operationTypeIncome);
    $sql->bindParam(":operationTypeOutcome", $operationTypeOutcome);
    $sql->execute();
    $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
    $data = [];
    foreach ($rows as $row) {
        arrayPush($data, [
            "skuId" => $row["skuId"],
            "skuName" => $row["skuName"],
            "color" => $row["color"],
            "cottonPart" => $row["cottonPart"],
            "quantity" => $row["quantity"],
        ]);
    }
    return $data;
}

?>