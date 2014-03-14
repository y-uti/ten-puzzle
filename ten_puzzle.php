<?php

$OPERATOR_COMBINATIONS = build_operator_combinations();
function build_operator_combinations()
{
    $results = array();
    $symbols = array('+', '-', '*', '/');
    for ($n = 0; $n < 64; ++$n) {
        $operators = array();
        foreach (to_digits($n, 4, 3) as $i) {
            $operators[] = $symbols[$i];
        }
        $results[] = $operators;
    }

    return $results;
}

$APPLICATION_ORDERS = build_application_orders();
function build_application_orders()
{
    return array(
        array(0, 1, 2),
        array(0, 2, 1), // array(2, 0, 1) はこれと等しいので除外
        array(1, 0, 2),
        array(1, 2, 0),
        array(2, 1, 0),
    );
}

function main()
{
    for ($n = 0; $n < 10000; ++$n) {
        find_all(to_digits($n, 10, 4));
    }
}

function find_all(array $digits)
{
    global $OPERATOR_COMBINATIONS;
    global $APPLICATION_ORDERS;

    foreach ($OPERATOR_COMBINATIONS as $operators) {
        foreach ($APPLICATION_ORDERS as $order) {
            $result = evaluate($digits, $operators, $order);
            if ($result == 10) {
                print to_formula($digits, $operators, $order) . "\n";
            }
        }
    }
}

function evaluate(array $digits, array $operators, array $order)
{
    while (count($digits) > 1) {
        $i = shift_order($order);
        $a = $digits[$i];
        $b = $digits[$i + 1];
        $o = array_shift(array_splice($operators, $i, 1));
        switch ($o) {
        case '+':
            $v = $a + $b;
            break;
        case '-':
            $v = $a - $b;
            break;
        case '*':
            $v = $a * $b;
            break;
        case '/':
            if ($b == 0) {
                return false;
            }
            $v = $a / $b;
            break;
        default:
            return false;
        }
        array_splice($digits, $i, 2, $v);
    }

    // 除算を含む場合の計算誤差を丸める。精度は適当
    return round($digits[0], 3);
}


function to_formula(array $digits, array $operators, array $order)
{
    while (count($digits) > 1) {
        $i = shift_order($order);
        $a = $digits[$i];
        $b = $digits[$i + 1];
        $o = array_shift(array_splice($operators, $i, 1));
        array_splice($digits, $i, 2, "($a $o $b)");
    }

    return $digits[0];
}

function shift_order(&$order)
{
    $i = array_shift($order);
    foreach ($order as &$o) {
        if ($i < $o) {
            --$o;
        }
    }

    return $i;
}

function to_digits($n, $base, $length)
{
    $digits = array_fill(0, $length, 0);
    for ($i = 0; $i < $length; ++$i) {
        $digits[$length - $i - 1] = $n % $base;
        $n = floor($n / $base);
    }

    return $digits;
}

main();
