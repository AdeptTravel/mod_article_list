<?php

/**
 * Article List Module
 *
 * @author Brandon J. Yaniz (joomla@adept.travel)
 * @copyright 2022 The Adept Traveler, Inc., All Rights Reserved.
 * @license BSD 2-Clause; See LICENSE.txt
 */

defined('_JEXEC') or die;

//$lang = \Joomla\CMS\Factory::getLanguage();
$app = \Joomla\CMS\Factory::getContainer()->get(\Joomla\CMS\Application\SiteApplication::class);
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$context = $option . '.' . $view;

$simular = ($params->get('type', 'all') == 'simular' && $context == 'com_content.article');
$show = true;

if ($simular) {
  $articleId = $app->input->getCmd('id', '');

  $tagsHelper = new \Joomla\CMS\Helper\TagsHelper();
  $tags = $tagsHelper->getTagIds($articleId, $context);

  if (!empty($tags)) {
    $tags = explode(',', $tags);
  } else {
    $show = false;
    $module->showtitle = 0;
  }
}

if ($show) {
  $id = $params->get('category_id', [0])[0];

  //$articles->setState('params', \Joomla\CMS\Factory::getApplication()->getParams());
  //$articles->setState('filter.category_id', 19);
  //$articles->setState('filter.published', 1);
  //$articles->setState('filter.featured', 1);
  //$articles->setState('list.ordering', $this->_buildContentOrderBy());
  //$articles->setState('list.start', 0);
  //$articles->setState('list.limit', (int)$params->get('count', 5));
  //$articles->setState('filter.published', 1);
  //$articles->setState('list.direction', $this->getState('list.direction'));
  //$articles->setState('list.filter', $this->getState('list.filter'));
  //$articles->setState('filter.tag', $this->getState('filter.tag'));

  /*
category_id
tag
featured
count
maxLevel
order_by
order
*/

  $categories = \Joomla\CMS\Categories\Categories::getInstance('Content', ['countItems' => 1]);
  $category = $categories->get($id);


  $articles = \Joomla\CMS\Factory::getApplication()
    ->bootComponent('com_content')
    ->getMVCFactory()
    ->createModel('Articles', 'Site', ['ignore_request' => true]);

  $articles->setState('params', \Joomla\CMS\Factory::getApplication()->getParams());
  $articles->setState('filter.category_id', $id);

  // Set the filters based on the module params
  $limit = (int) $params->get('count', 5) + (($simular) ? 1 : 0);

  $articles->setState('list.start', 0);
  $articles->setState('list.limit', $limit);
  $articles->setState('filter.published', $articles->setState('filter.featured', 'show'));

  // Show Featured Articles
  $articles->setState('filter.featured', $params->get('featured', 'show'));

  // Tag filter
  if ($params->get('type', 'all') == 'all') {
    if (!empty($params->get('tag'))) {
      $articles->setState('load_tags', true);
      $articles->setState('filter.tag', $params->get('tag', array()));
    } else {
      // This module does not use tags data
      $articles->setState('load_tags', false);
    }
  } else if (
    $params->get('type', 'all') == 'simular'
    && $context == 'com_content.article'
  ) {
    $articles->setState('load_tags', true);
    $articles->setState('filter.tag', $tags);
  } else {
    $articles->setState('load_tags', false);
  }

  // Access filter
  $access = !\Joomla\CMS\Component\ComponentHelper::getParams('com_content')->get('show_noauth');
  $authorised = \Joomla\CMS\Access\Access::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
  $articles->setState('filter.access', $access);

  // Show articles in subcategories
  $articles->setState('filter.subcategories', (($params->get('maxLevel', -1) == 0) ? 0 : 1));
  // Subcategory depth, -1 = all
  $articles->setState('filter.max_category_levels', $params->get('maxLevel', -1));


  $orderBy = $params->get('order_by', 'a.publish_up');
  /*
if ($orderBy == 'random') {
  $orderBy = $db->getQuery(true)->Rand();
}
*/

  $articles->setState('list.ordering', $orderBy);
  $articles->setState('list.direction', $params->get('order', 'ASC'));

  $items = $articles->getItems();

  if (!empty($items) && $simular) {
    $articleId = $app->input->getCmd('id', '');

    for ($i = 0; $i < count($items); $i++) {
      if ($items[$i]->id == $articleId) {
        unset($items[$i]);
        break;
      }
    }

    if (count($items) > $params->get('count', 5)) {
      unset($items[count($items) - 1]);
    }
  }


  if (!empty($items)) {
    require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_article_list', $params->get('layout', 'default'));
  }
}
