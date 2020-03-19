<?php

namespace Nick\Translation;

use Nick\Logger;

/**
 * Trait StringTranslation
 * 
 * @package Nick\Translation
 */
trait StringTranslation {

  /**
   * Translation service, correct usage would be to enter a literal string, for example:
   *       $animal = 'fox'; $color = 'brown';
   *       Translation::translate('The :animal jumps over the :color fence', [':animal' => $animal, ':color' => $color]);
   * This ensures the proper handling of variables in string translations.
   * This method can also be used to stack translations, for example:
   *       $companyName = Translation::translate('myCompany');
   *       Translation::translate('Welcome to :company', [':company' => $companyName]);
   *
   * @param string $string
   * @param array $args
   *
   * @return mixed
   */
  public function translate($string, array $args = []) {
    $translation = \Nick::Translation();

    if (!is_string($string)) {
      \Nick::Logger()->add('Only strings should be entered.');
      return FALSE;
    }

    if ($string === $translation->get($string, TRUE)) {
      if (!$translation->set($string, $string, $args)) {
        \Nick::Logger()->add('Something went wrong trying to set a translation.', Logger::TYPE_FAILURE, 'Translation');
      }
    }
    return $translation->get($string, TRUE);
  }

}