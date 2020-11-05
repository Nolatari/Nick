<?php

namespace Nick\Translation;

use Nick;
use Nick\Logger;

/**
 * Trait StringTranslation
 *
 * @package Nick\Translation
 */
trait StringTranslation {

  /**
   * Translation service, correct usage would be to enter a literal string, for example:
   *       $color = 'brown'; $itemColor = 'yellow'; $item = 'fence';
   *       StringTranslation::translate('The :color fox jumps over the :item_color :item', [':color' => $color,
   *       ':item_color' => $itemColor, ':item' => $item]); This ensures the proper handling of variables in string
   *       translations for dynamic reusage of the string. This method can also be used to stack translations, for
   *       example:
   *       $companySuffix = StringTranslation::translate('Incorporated');
   *       $companyName = StringTranslation::translate('myCompany :suffix', [':suffix' => $companySuffix]);
   *       StringTranslation::translate('Welcome to :company', [':company' => $companyName]);
   *
   * @param string $string
   * @param array  $args
   *
   * @return mixed
   */
  public function translate(string $string, array $args = []) {
    $translation = \Nick::Translation();

    if (!is_string($string)) {
      \Nick::Logger()->add('Only strings should be entered.');
      return FALSE;
    }

    if ($string === $translation->get($string)) {
      if (!$translation->set($string, $string)) {
        \Nick::Logger()->add('Something went wrong trying to set a translation.', Logger::TYPE_FAILURE, 'StringTranslation');
      }
    }
    return $translation->get($string, $args);
  }

}