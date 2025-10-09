<?php

function convertToEuro(float $priceBgn): float
{
    return round($priceBgn / BGN_TO_EUR_RATE, 2);
}


/**
 * Formats and displays a price in Bulgarian Lev (BGN) and its equivalent in Euro (EUR).
 *
 * @param float $priceBgn The price in Bulgarian Lev.
 * @return string The formatted price string in BGN and EUR.
 */
function displayPrice(float $priceBgn): string
{
    $priceEur = convertToEuro($priceBgn);
    return sprintf('%.2f лв / %.2f €', $priceBgn, $priceEur);
}
