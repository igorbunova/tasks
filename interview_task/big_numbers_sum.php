<?php

use http\Exception\RuntimeException;

/**
 * @param string $arg
 * @return string Validate whether  a string represents number
 */

function validateNumber(string $arg) {
    if (!preg_match("/^-?[0-9]{1,}(\.[0-9]{1,})?$/", $arg))
        throw new RuntimeException("Failed arg " . $arg);
}

function foldZeroStart(string $arg, int $len) {
    return str_repeat("0", $len) . $arg;
}

function foldZeroEnd(string $arg, int $len) {
    return $arg . str_repeat("0", $len);
}

function padLeft(string $source, string $relative) {
    return foldZeroStart($source, max(0, strlen($relative) - strlen($source)));
}

function padRight(string $source, string $relative) {
    return foldZeroEnd($source, max(0, strlen($relative) - strlen($source)));
}

/**
 * @param string $arg1
 * @param string $arg2
 * @return string Returns a sum of numbers
 */
function sumPositiveNumbers(string $arg1, string $arg2) {
    $parts1 = preg_split("/\./", $arg1);
    $parts2 = preg_split("/\./", $arg2);

    if (count($parts1) == 1) {
        $parts1[1] = "";
    }
    if (count($parts2) == 1) {
        $parts2[1] = "";
    }
    $parts1[0] = padLeft($parts1[0], $parts2[0]);
    $parts2[0] = padLeft($parts2[0], $parts1[0]);

    $parts1[1] = padRight($parts1[1], $parts2[1]);
    $parts2[1] = padRight($parts2[1], $parts1[1]);

    $result_ceil = "";
    $result_fraction  = "";

    $overflow = 0;
    for ($i = strlen($parts1[1]) - 1; $i >= 0; $i --) {
        $val = $parts1[1][$i] + $parts2[1][$i] + $overflow;
        if ($val > 9) {
            $overflow  = 1;
            $val  = $val % 10;
        } else {
            $overflow = 0;
        }
        $result_fraction = $val . $result_fraction;
    }

    for ($i = strlen($parts1[0]) - 1; $i >= 0; $i --) {
        $val = $parts1[0][$i] + $parts2[0][$i] + $overflow;
        if ($val > 9) {
            $overflow  = 1;
            $val  = $val % 10;
        } else {
            $overflow = 0;
        }
        $result_ceil = $val . $result_ceil;
    }

    if (strlen($result_fraction) > 0) {
        return $result_ceil . "." . $result_fraction;
    }
    return $result_ceil;
}

function substractParts(array $decreasing, array $decrement) {
    $result_ceil = "";
    $result_fraction  = "";

    $overflow = 0;
    for ($i = strlen($decreasing[1]) - 1; $i >= 0; $i --) {
        if ($decreasing[1][$i] - $overflow < $decrement[1][$i]) {
            $val = $decreasing[1][$i] - $overflow + 10 - $decrement[1][$i];
            $overflow = 1;
        } else {
            $val = $decreasing[1][$i] - $overflow - $decrement[1][$i];
            $overflow = 0;
        }
        $result_fraction = $val . $result_fraction;
    }

    for ($i = strlen($decreasing[0]) - 1; $i >= 0; $i --) {
        if ($decreasing[0][$i] - $overflow < $decrement[0][$i]) {
            $val = $decreasing[0][$i] - $overflow + 10 - $decrement[0][$i];
            $overflow = 1;
        } else {
            $val = $decreasing[0][$i] - $overflow - $decrement[0][$i];
            $overflow = 0;
        }
        $result_ceil = $val . $result_ceil;
    }

    if (strlen($result_fraction) > 0) {
        return $result_ceil . "." . $result_fraction;
    }
    return $result_ceil;
}

function substractPositiveNumbers(string $arg1, string $arg2) {
    $parts1 = preg_split("/\./", $arg1);
    $parts2 = preg_split("/\./", $arg2);

    if (count($parts1) == 1) {
        $parts1[1] = "";
    }
    if (count($parts2) == 1) {
        $parts2[1] = "";
    }
    $parts1[0] = padLeft($parts1[0], $parts2[0]);
    $parts2[0] = padLeft($parts2[0], $parts1[0]);

    $parts1[1] = padRight($parts1[1], $parts2[1]);
    $parts2[1] = padRight($parts2[1], $parts1[1]);

    $cmp = strcmp($parts1[0], $parts2[0]);
    if ($cmp  == 0) {
        $cmp = strcmp($parts1[1], $parts2[1]);
    }
    if ($cmp == 0) {
        return "0";
    }

    if ($cmp > 0) {
        return substractParts($parts1, $parts2);
    } else {
        return "-" . substractParts($parts2, $parts1);
    }
}

function sum(string $arg1, string $arg2) {
    validateNumber($arg1);
    validateNumber($arg2);

    if (strpos($arg1, '-') === 0 && strpos($arg2, '-') === 0) {
        return "-" . sumPositiveNumbers(substr($arg1, 1), substr($arg2, 1));
    }
    if (strpos($arg1, '-') === 0) {
        return substractPositiveNumbers($arg2, substr($arg1, 1));
    }

    if (strpos($arg2, "-") === 0) {
        return substractPositiveNumbers($arg1, substr($arg2, 1));
    }

    return sumPositiveNumbers($arg1, $arg2);
}



echo sum("123456.342", "567809877654.1567") . "\n";
echo sum("1238761876",  "45618723654165436541653423.32459872765431543") . "\n";
echo sum("45618723654165436541653423.32459872765431543", "1238761876") . "\n";
echo sum("567809877654.1567", "123456.342") . "\n";
echo sum ("122635415433", "28764786146544") . "\n";
echo sum ("28764786146544", "122635415433") . "\n";
echo sum("-123", "-23"). "\n";
echo sum("-23", "-123"). "\n";
echo sum("-11.2", "14.3"). "\n";
echo sum("14.3", "-11.2"). "\n";
echo sum("11.2", "-14.3"). "\n";
echo sum("-14.3", "11.2"). "\n";
