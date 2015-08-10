<?php
namespace FauxCombinator;

/**
 * A lexer object. Constructed with an array of rules
 */
class Lexer {
  /**
   *  Format: [[rule-regexp, rule-name], ...]
   */
  private $rules;

  public function __construct(array $rules) {
    $this->rules = $rules;
  }

  /**
   * @param string $code the code to parse
   * @throws \RuntimeException if the code can't be parsed
   */
  public function parse($code) {
    $tokens = [];
    while ($code) {
      $code = ltrim($code); // skip whitespace
      foreach ($this->rules as $rule) {
        list($pattern, $type) = $rule;
        if (preg_match("/$pattern/", $code, $capture)) {
          // if we matched, add token to the list, and remove
          //  the code we just matched.
          $tokens[] = [
            'type' => $type,
            'value' => $capture[0],
          ];
          $code = substr($code, strlen($capture[0]));
          continue 2; // go back to the while()
        }
      }
      throw new \RuntimeException("Unable to match " . substr($code, 0, 15));
    }
    return $tokens;
  }
};
