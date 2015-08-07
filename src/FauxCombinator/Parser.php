<?php
namespace FauxCombinator;

abstract class Parser {
  private $tokens;

  /**
   * @param Array<Array<String, String>> $tokens
   *  An array of tokens, constructed by the Lexer
   */
  public function __construct(array $tokens) {
    $tokens[] = ['type' => 'eof']; // "end of file" token
    $this->tokens = $tokens;
  }

  private function run($rule) {
    $this->{'parse'.ucfirst($rule)}();
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

  public function match(Callable $matcher) {
    call_user_func($matcher);
  }

  abstract public function parse();
}

class ParserException extends \RuntimeException {
}
