<?php

require_once("/app/lib/enums/color.php");
require_once("/app/lib/checks.php");

use function is_null as isNull;

class Socks {
    public $skuId;
    public $skuName;
    public $color;
    public $cottonPart;

    public function __construct($skuId, $skuName, $color, $cottonPart) {
        $this->skuId = checkUuid($skuId);
        $this->skuName = checkName($skuName, 30);
        $this->color = Color::from($color);
        $this->cottonPart = checkCottonPart($cottonPart);
    }

    public static function tryFrom($data) {
        try {
            if (isNull($data)) {
                throw new ValueError("Value is null");
            };
            return new Socks(
                $data["skuId"],
                $data["skuName"],
                $data["color"],
                $data["cottonPart"],
            );
        } catch (ValueError $e) {
            return null;
        }
    }

    public function getData() {
        $data = get_object_vars($this);
        $data["color"] = $data["color"]->value;
        return $data;
    }

}

?>