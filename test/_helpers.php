<?php
require __DIR__ . '/../src/FauxCombinator/Lexer.php';
require __DIR__.'/../src/FauxCombinator/Parser.php';
use FauxCombinator\Lexer;
use FauxCombinator\Parser;

function check($got, $expected, $message = '') {
  if ($got != $expected) {
    echo "Expected '".print_r($expected, true)."', Got '".print_r($got, true)."' in $message\n";
  } else {
    echo "OK: $message\n";
  }
}

function ok($cond, $message = '') {
  if ($cond) {
    echo "OK: $message\n";
  } else {
    echo "Assert failed in $message!";
    debug_print_backtrace();
    echo "\n\n";
  }
}

function pass($message = '') {
  echo "OK: $message\n";
}

function fail($message = '') {
  echo "failed test: $message\n";
}

// then, helpers...

function lex($rules, $code) {
  return (new Lexer($rules))->parse($code);
}

function parse($parser, array $tokens) {
  return (new $parser($tokens))->parse();
}
