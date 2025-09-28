<?php

enum Color: string {
    case red = 'Красный';
    case black = 'Черный';
    case green = 'Зеленый';
    case grey = 'Серый';
    case multicolor = 'Разноцветный';
}

function colorFromQueryParameters($value) {
    switch ($value) {
        case "red":
            return Color::red;
            break;
        case "black":
            return Color::black;
            break;
        case "green":
            return Color::green;
            break;
        case "grey":
            return Color::grey;
            break;
        case "multicolor":
            return Color::multicolor;
            break;
        default:
            throw new ValueError();
            break;
    }
}

?>