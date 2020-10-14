<?php

use Nick\Language\Language;
use Nick\Cache\CacheInterface;
use Nick\Core;

/**
 * Include Settings
 */
require_once __DIR__ . '/components/Settings.php';

/** Require Nick component */
require_once 'Nick.php';

/** @var CacheInterface $cache */
$cache = Core::getCacheClass();
$cache->initializeCache();

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
function translate(string $string, $args = []) {
  /** @var Language $language */
  $language = \Nick::LanguageManager()->getCurrentLanguage();
  return $language->translate($string, $args);
}
