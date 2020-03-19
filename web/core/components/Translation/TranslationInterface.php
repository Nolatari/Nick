<?php

namespace Nick\Translation;

/**
 * Interface TranslationInterface
 *
 * @package Nick\Translation
 */
interface TranslationInterface {

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
  public function translate($string, array $args = []);

}
