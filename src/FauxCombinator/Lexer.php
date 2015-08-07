<?php
namespace FauxCombinator;

class Lexer {
  private $rules;

  public function __construct(array $rules) {
    $this->rules = $rules;
  }

  public function parse($code) {
    $tokens = [];
    while ($code) {
      $code = ltrim($code);
      foreach ($this->rules as $rule) {
        list($pattern, $type) = $rule;
        if (preg_match("/$pattern/", $code, $capture)) {
          $tokens[] = [
            'type' => $type,
            'value' => $capture[0],
          ];
          $code = substr($code, strlen($capture[0]));
          continue 2;
        }
      }
      throw new \RuntimeException("Unable to match " . substr($code, 0, 15));
    }
    return $tokens;
  }
};
