<?php

/**
 * Article List Module
 *
 * @author Brandon J. Yaniz (joomla@adept.travel)
 * @copyright 2022 The Adept Traveler, Inc., All Rights Reserved.
 * @license BSD 2-Clause; See LICENSE.txt
 */

defined('_JEXEC') or die;

/*
category_container
category_container_css
category_title
category_title_tag
category_image_type
category_image_custom
category_image_custom_alt
category_description_type
category_description_custom
article_layout
list_layout
list_container
list_container_css
*/

if ($params->get('category_title', 0) || $params->get('category_image_type', 0) || $params->get('category_description_type')) {

  if (!empty($params->get('category_container'))) {
    echo '<' . $params->get('category_container');

    if (!empty($params->get('category_container_css'))) {
      echo ' class="' . $params->get('category_container_css') . '"';
    }
    echo '>';
  }

  if ($params->get('category_title', 0)) {
    echo '<' . $params->get('category_title_tag') . '>';
    echo $category->title;
    echo '</' . $params->get('category_title_tag') . '>';
  }

  if ($params->get('category_image_type', 0) == 'category') {
    $catParams = json_decode($category->params);

    if (!empty($catParams->image)) {
      echo '<figure>';
      echo '<img';
      echo ' src="' . substr($catParams->image, 0, strpos($catParams->image, '#')) . '"';

      if ($params->get('category_image_lazy', 0)) {
        echo ' loading="lazy"';
      }

      echo ' alt="';

      if (!empty($catParams->image_alt)) {
        echo $catParams->image_alt;
      } else {
        echo $category->title;
      }
      echo '"';

      echo '>';
      echo '</figure>';
    }
  } else if ($params->get('category_image_type', 0) == 'custom') {

    echo '<figure>';
    echo '<img';
    echo ' src="' . $params->get('category_image_custom') . '"';

    if ($params->get('category_image_lazy', 0)) {
      echo ' loading="lazy"';
    }

    echo ' alt="' . $params->get('category_image_custom_alt ') . '"';
    echo '>';
    echo '</figure>';
  }

  if ($params->get('category_description_type', 0) != 0) {
    echo '<div class="content">';

    if ($params->get('category_description_type') == 'category') {
      echo $category->description;
    } else if ($params->get('category_description_type') == 'custom') {
      echo $params->get('category_description_custom');
    }

    echo '</div>';
  }

  if (!empty($params->get('category_container'))) {
    echo '</' . $params->get('category_container') . '>';
  }
}

if ($params->get('list_container', 0)) {
  $css = trim($params->get('list_layout') . ' ' . $params->get('list_container_css'));

  echo '<' . $params->get('list_container');

  if (!empty($css)) {
    echo ' class="' . $css . '"';
  }

  echo '>';
}

foreach ($items as $item) {
  require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_article_list', 'default_item');
}

if ($params->get('list_container', 0)) {
  echo '</' . $params->get('list_container') . '>';
}
