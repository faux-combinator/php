<?php
require __DIR__ . '/_helpers.php';
use FauxCombinator\Parser;
use FauxCombinator\ParserException;

class ParenParser extends Parser {
  public function parse() {
    $this->expect('lparen');
    $this->expect('rparen');
    return true;
  }
}

$lparen_token = ['type' => 'lparen', 'value' => '('];
$rparen_token = ['type' => 'rparen', 'value' => '('];
$num_token  = ['type' => 'num', 'value' => '5'];
// ()
$ast = [$lparen_token, $rparen_token];

check(parse('ParenParser', $ast), true, "can parse basic stuff");
try {
  parse('ParenParser', []);
  fail("The parser should fail on expect()");
} catch (ParserException $e) {
  pass("The parser should fail on expect()");
}

class NestedParenParser extends Parser {
  public function parse() {
    return $this->parseExpr();
  }

  protected function parseExpr() {
    $this->expect('lparen');
    $result = $this->maybe('num');
    $this->expect('rparen');
    return $result ? $result['value'] : true;
  }

  protected function parseNum() {
    return $this->expect('num');
  }
}

try {
  // ()
  $ast = [$lparen_token, $rparen_token];
  check(parse('NestedParenParser', $ast), true, "can still parse basic stuff");
} catch (ParserException $e) {
  fail("cannot parse basic stuff anymore");
}

// (())
$ast = [$lparen_token, $num_token, $rparen_token];
check(parse('NestedParenParser', $ast), 5, "can parse optional tokens");
