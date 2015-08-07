<?php
require __DIR__ . '/../src/FauxCombinator/Lexer.php';
#require __DIR__.'/../src/FauxCombinator/Parser.php';
use FauxCombinator\Lexer;

function check($got, $expected, $message = '') {
  if ($got != $expected) {
    echo "Expected '".print_r($expected, true)."', Got '".print_r($got, true)."' in $message\n";
  } else {
    echo "OK: $message\n";
  }
}

function lex($rules, $code) {
  return (new Lexer($rules))->parse($code);
}
