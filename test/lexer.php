<?php
require __DIR__ . '/_helpers.php';

$rules = [
  [ '=', 'eq' ]
];

$eq_token = ['type' => 'eq', 'value' => '='];

check(lex($rules, '='), [$eq_token],
    "basic parsing works");
check(lex($rules, '=='), [$eq_token, $eq_token],
    "can parse multiple occurences");
check(lex($rules, '= ='), [$eq_token, $eq_token],
    "can parse multiple, space-separated occurences");

$rules = [
  [ '=', 'eq' ],
  [ '-', 'dash' ],
  [ '_', 'under' ],
];
$dash_token = ['type' => 'dash', 'value' => '-'];
$under_token = ['type' => 'under', 'value' => '_'];

check(lex($rules, '='), [$eq_token], "multiple rules can find first");
check(lex($rules, '-'), [$dash_token], "multiple rules can find second");
check(lex($rules, '_'), [$under_token], "multiple rules can find third");

check(lex($rules, '=-_'), [$eq_token, $dash_token, $under_token], "multiple rules can match all");
check(lex($rules, '=-  _'), [$eq_token, $dash_token, $under_token], "multiple rules can match all with space separation");

# TODO: add tests for [ 'x', 'y', function($val){} ] form