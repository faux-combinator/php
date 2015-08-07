<?php
require __DIR__ . '/_helpers.php';
use FauxCombinator\Parser;
use FauxCombinator\ParserException;

$lparen_token = ['type' => 'lparen', 'value' => '('];
$rparen_token = ['type' => 'rparen', 'value' => '('];
$num_token    = ['type' => 'num', 'value' => '5'];
$str_token    = ['type' => 'str', 'value' => 'a string here'];
$id_token     = ['type' => 'id', 'value' => 'a'];

class ParenParser extends Parser {
  public function parse() {
    $this->expect('lparen');
    $this->expect('rparen');
    return true;
  }
}

// LEX: ()
$ast = [$lparen_token, $rparen_token];

check(parse('ParenParser', $ast), true, "paren - can parse basic stuff");
try {
  parse('ParenParser', []);
  fail("paren - should fail on expect()");
} catch (ParserException $e) {
  pass("paren - should fail on expect()");
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
  // LEX: ()
  $ast = [$lparen_token, $rparen_token];
  check(parse('NestedParenParser', $ast), true,
    "nested - can still parse basic stuff");
} catch (ParserException $e) {
  fail("nested - cannot parse basic stuff anymore");
}

// LEX: (())
$ast = [$lparen_token, $num_token, $rparen_token];
check(parse('NestedParenParser', $ast), 5, "nested - can parse optional tokens");

class OneOfParser extends Parser {
  public function parse() {
    return $this->oneOf('id', 'num', 'str')['value'];
  }

  protected function parseId() {
    return $this->expect('id');
  }

  protected function parseNum() {
    return $this->expect('num');
  }

  protected function parseStr() {
    return $this->expect('str');
  }
}

// LEX: a
$ast = [$id_token];
check(parse('OneOfParser', $ast), 'a', "oneOf - can parse first case");

// LEX: 5
$ast = [$num_token];
check(parse('OneOfParser', $ast), 5, "oneOf - can parse second case");

// LEX: "a string here"
$ast = [$str_token];
check(parse('OneOfParser', $ast), "a string here", "oneOf - can parse third case");

try {
  // LEX: (
  $ast = [$lparen_token];
  parse('OneOfParser', $ast);
  fail("oneOf - should refuse tokens NOT LISTED in anyOf");
} catch (ParserException $e) {
  pass("oneOf - should refuse tokens NOT LISTED in anyOf");
}

class AnyOfParser extends Parser {
  public function parse() {
    return $this->anyOf('num');
  }

  protected function parseNum() {
    return $this->expect('num')['value'];
  }
}

// LEX: 
$ast = [];
check(parse('AnyOfParser', $ast), [], "anyOf - can parse empty number of occurences");

// LEX: 5
$ast = [$num_token];
check(parse('AnyOfParser', $ast), [5], "anyOf - can parse one occurence");

// LEX: 5 5 5
$ast = [$num_token, $num_token, $num_token];
check(parse('AnyOfParser', $ast), [5, 5, 5], "anyOf - can parse many occurences");

class ManyOfParser extends Parser {
  public function parse() {
    return $this->manyOf('num');
  }

  protected function parseNum() {
    return $this->expect('num')['value'];
  }
}

try {
  // LEX: 
  $ast = [];
  parse('ManyOfParser', $ast);
  fail("manyOf - should NOT parse empty number of occurences");
} catch (ParserException $e) {
  pass("manyOf - should NOT parse empty number of occurences");
}

// LEX: 5
$ast = [$num_token];
check(parse('ManyOfParser', $ast), [5], "manyOf - can parse one occurence");

// LEX: 5 5 5
$ast = [$num_token, $num_token, $num_token];
check(parse('ManyOfParser', $ast), [5, 5, 5], "manyOf - can parse many occurences");
