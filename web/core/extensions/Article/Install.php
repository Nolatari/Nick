<?php

namespace Nick\Article;

use Nick\Article\Entity\Article;
use Nick\ExtensionManager\InstallInterface;
use Nick\Logger;
use Nick\Menu\Entity\Menu;

/**
 * Class Install
 *
 * @package Nick\Article
 */
class Install implements InstallInterface {

  /**
   * {@inheritDoc}
   */
  public function condition() {
    $overview = \Nick::EntityManager()->loadByProperties(['type' => 'menu', 'route' => 'article.overview']);
    return $overview !== FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function doInstall() {
    $menu = new Menu([
      'route' => 'article.overview',
      'title' => 'Articles',
      'description' => 'Overview of all articles',
      'structure' => 0,
      'type' => 'link',
      'translatable' => TRUE,
      'parent' => 0,
      'owner' => 1,
      'status' => 1,
    ]);
    if (!$menu->save()) {
      return FALSE;
    }
    \Nick::Logger()->add('Added menu item', Logger::TYPE_INFO, 'Article');

    $articles = \Nick::EntityManager()->loadByProperties([
      'type' => 'article',
      'title' => 'Make Your Life Better by Saying Thank You in These 7 Situations',
    ]);
    d($articles);exit;
    $article = new Article([
      'owner' => 1,
      'status' => 1,
      'title' => 'Make Your Life Better by Saying Thank You in These 7 Situations',
      'body' => htmlspecialchars(file_get_contents('core/extensions/Article/tests/example_article_body.txt')),
    ]);

    if (!$article->save()) {
      return FALSE;
    }
    \Nick::Logger()->add('Added example article item', Logger::TYPE_INFO, 'Article');
    return TRUE;
  }

}
