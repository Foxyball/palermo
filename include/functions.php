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

/**
 * Verify user password with backwards compatibility for md5 and password_hash
 * @param string $inputPassword The plain text password to verify
 * @param string $storedHash The stored password hash from database
 * @return bool True if password matches, false otherwise
 */
function verifyUserPassword(string $inputPassword, string $storedHash): bool
{
    // Handle empty stored hash
    if (empty($storedHash)) {
        return false;
    }
    
    // Check if it's a modern password_hash
    if (password_verify($inputPassword, $storedHash)) {
        return true;
    }
    
    // Fallback to md5 for legacy passwords
    if (md5($inputPassword) === $storedHash) {
        return true;
    }
    
    return false;
}

/**
 * Check if a password hash needs to be rehashed (for upgrading from md5 to password_hash)
 * @param string $storedHash The stored password hash from database
 * @return bool True if needs rehashing, false otherwise
 */
function passwordNeedsRehash(string $storedHash): bool
{
    // If it's not a password_hash format, it needs rehashing
    return !password_get_info($storedHash)['algo'];
}
