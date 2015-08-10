<?php
namespace FauxCombinator;

abstract class Parser {
  private $tokens;

  /**
   * @param Array<Array<String, String>> $tokens
   *  An array of tokens constructed by the Lexer
   */
  public function __construct(array $tokens) {
    $tokens[] = ['type' => 'eof']; // "end of file" token
    $this->tokens = $tokens;
  }

  private function run($rule) {
    return $this->{'parse'.ucfirst($rule)}();
  }

  /**
   * @param string $type a token type (from the lexer)
   */
  public function expect($type) {
    $token = array_shift($this->tokens);
    if ($token['type'] != $type) {
      throw new ParserException("Expected token $type, found token {$token{'type'}} instead.");
    }
    return $token;
  }

  /**
   * @param string $matcher the name of a rule (parse + `XYZ`)
   *  to be "tried": will just return false if not matched
   */
  public function maybe($matcher) {
    $tokens = $this->tokens; // copy the array
    try {
      return $this->run($matcher);
    } catch (ParserException $e) {
      $this->tokens = $tokens;
      return false;
    }
  }

  /**
   * @param string... $matchers list of rules to try.
   *  At least one has to match!
   */
  public function oneOf() {
    $matchers = func_get_args(); // TODO use greater PHP version
    foreach ($matchers as $matcher) {
      if ($result = $this->maybe($matcher)) {
        return $result;
      }
    }
    throw new ParserException("Unable to parse oneOf cases: " . print_r($matchers, true) . "\nTokens: " . print_r($this->tokens, true));
  }

  /**
   * @param string $matcher a rule that'll be matched
   *  zero or more times
   */
  public function anyOf($matcher) {
    $values = [];
    while ($value = $this->maybe($matcher)) {
      $values[] = $value;
    }
    return $values;
  }

  /**
   * @param string $matcher a rule that'll be matched
   *  one or more times
   */
  public function manyOf($matcher) {
    return array_merge([$this->run($matcher)], $this->anyOf($matcher));
  }

  abstract public function parse();
}

class ParserException extends \RuntimeException {
}
