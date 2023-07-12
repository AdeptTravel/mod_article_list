<?php

/**
 * Article List Module
 *
 * @author Brandon J. Yaniz (joomla@adept.travel)
 * @copyright 2022 The Adept Traveler, Inc., All Rights Reserved.
 * @license BSD 2-Clause; See LICENSE.txt
 */

defined('_JEXEC') or die;

//die('Item<Pre>' . print_r($item, true));

/*

* item_header
* item_title
* item_title_tag
* item_title_link

item_content
item_image
item_image_link
item_readmore
item_readmore_text

* item_info
* item_info_container
* item_info_container_tag
* item_info_container_css
* item_info_as
it* em_info_as_delimiter
* item_info_as_title
* item_info_author
* item_info_category_parent
* item_info_category
* item_info_date
* item_info_date_format
* item_info_date_created
* item_info_date_published
* item_info_date_modified
 */

$link = '';
/*
$title = htmlspecialchars(
  ((!empty($item->title)) ? $item->title : ''),
  ENT_COMPAT,
  'UTF-8'
);
*/
if ($access || in_array($item->access, $authorised)) {
  // We know that user has the privilege to view the article
  $link = \Joomla\CMS\Router\Route::_(ContentHelperRoute::getArticleRoute(
    $item->id . ':' . $item->alias,
    $item->catid,
    $item->language
  ));
} else {
  $link = \Joomla\CMS\Router\Route::_('index.php?option=com_users&view=login');
}

if ($params->get('list_container', 0) == 'section') {
  echo '<article>';
} else if ($params->get('list_container', 0) == 'ul' || $params->get('list_container', 0) == 'ol') {
  echo '<li>';
} else {
  echo '<div>';
}


if ($params->get('item_header', 1)) {
  echo '<header>';
}

// Title
if ($params->get('item_title')) {
  if ($params->get('item_title_tag', 'h4')) {
    echo '<' . $params->get('item_title_tag', 'h4') . ' itemprop="headline">';
  }


  if ($params->get('item_title_link')) {
    echo '<a href="' . $link . '">';
  }

  echo $title;

  if ($params->get('item_title_link')) {
    echo '</a>';
  }

  if ($params->get('item_title_tag', 'h4')) {
    echo '</' . $params->get('item_title_tag', 'h4') . '>';
  }
}

if ($params->get('item_info')) {
  $tag = $params->get('item_info_as', 'div');

  if ($params->get('item_info_container', 0)) {
    echo '<' . $params->get('item_info_container_tag', 'div');
    if (!empty($params->get('item_info_container_css'))) {
      echo ' class="' . trim($params->get('item_info_container_css')) . '"';
    }
    echo '>';
  }

  if ($tag == 'ul' || $tag == 'ol' || $tag == 'dl') {
    echo '<' . $tag . '>';

    if ($tag == 'dl') {
      $tag = 'dd';
    } else {
      $tag = 'li';
    }
  }

  if ($params->get('item_info_author', 0)) {
    if ($tag == 'dd') {
      echo '<dt class="author">By</dt>';
    }

    echo '<' . $tag . ' class="author" itemprop="author">' . $item->author . '</' . $tag . '>';
  }

  // Parent Category
  if ($params->get('item_info_category_parent', 0)) {
    $category = $categories->get($item->catid);
    $parent = $categories->get($category->parent_id);

    if ($tag == 'dd') {
      echo '<dt class="parent">Parent</dt>';
    }

    echo '<' . $tag . ' class="parent" itemprop="genre">' . $parent->title . '</' . $tag . '>';
  }

  // Category
  if ($params->get('item_info_category', 0)) {
    $category = $categories->get($item->catid);

    if ($tag == 'dd') {
      echo '<dt class="category">category</dt>';
    }

    echo '<' . $tag . ' class="category" itemprop="genr e">' . $category->title . '</' . $tag . '>';
  }

  if ($params->get('item_info_date', 0)) {
    $format = $params->get('info_date_format', 'F d, Y');

    if ($params->get('item_info_date_created', 0)) {
      // Created
      if ($tag == 'dd') {
        echo '<dt class="created">Created</dt>';
      }

      echo '<' . $tag . ' class="created">';
      echo '<time datetime="' . \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, 'c') . '" itemprop="dateCreated">';
      echo \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, \Joomla\CMS\Language\Text::_($format));
      echo '</time>';
      echo '</' . $tag . '>';
    }

    // Published
    if ($params->get('item_info_date_published', 0)) {
      if ($tag == 'dd') {
        echo '<dt class="published">Published</dt>';
      }

      echo '<' . $tag . ' class="published">';
      echo '<time datetime="' . \Joomla\CMS\HTML\HTMLHelper::_('date', $item->publish_up, 'c') . '" itemprop="datePublished">';
      echo \Joomla\CMS\HTML\HTMLHelper::_('date', $item->publish_up, \Joomla\CMS\Language\Text::_($format));
      echo '</time>';
      echo '</' . $tag . '>';
    }

    // Modified
    if ($params->get('item_info_date_modified', 0)) {
      if ($tag == 'dd') {
        echo '<dt class="modified">Modified</dt>';
      }

      echo '<' . $tag . ' class="modified">';
      echo '<time datetime="' . \Joomla\CMS\HTML\HTMLHelper::_('date', $item->modified, 'c') . '" itemprop="datePublished">';
      echo \Joomla\CMS\HTML\HTMLHelper::_('date', $item->modified, \Joomla\CMS\Language\Text::_($format));
      echo '</time>';
      echo '</' . $tag . '>';
    }
  }
}

if ($params->get('item_info_container', 0)) {
  echo '</' . $params->get('item_info_container_tag', 'div') . '>';
}

if ($params->get('item_header', 1)) {
  echo '</header>';
}

$showImage = ($params->get('item_image') != 0);

// Intro Image
if ($showImage) {

  $images = json_decode($item->images);

  $var = 'image_' . $params->get('item_image');

  if (!empty($images->$var)) {
    echo '<figure class="intro item-image">';

    if ($params->get('item_image_link', 0)) {
      echo '<a';
      echo ' href="' . $link . '"';
      echo ' itemprop="url"';
      echo ' title="' . $title . '"';
      echo '>';
    }

    $var = 'image_' . $params->get('item_image');
    $img = \Joomla\CMS\HTML\HTMLHelper::_('cleanImageURL', $images->$var);

    echo '<img';
    echo ' src="';

    if (substr($img->url, 0, 1) != '/') {
      echo '/';
    }

    echo $img->url . '"';

    if ($img->attributes['width'] > 0 && $img->attributes['height'] > 0) {
      echo ' width="' . $img->attributes['width'] . '"';
      echo ' height="' . $img->attributes['height'] . '"';
    }

    echo ' alt="' . ((!empty($images->image_intro_alt)) ? $images->image_intro_alt : $item->title) . '"';
    echo ' itemprop="thumbnailUrl"';

    if ($params->get('item_image_lazy', 0)) {
      echo ' loading="lazy"';
    }

    echo '>';

    if ($params->get('item_image_link', 0)) {
      echo '</a>';
    }

    echo '</figure>';
  } else {
    $showImage = false;
  }
}

/*
item_content
item_readmore
item_readmore_text
*/

// Show Content
if (
  (!$params->get('item_content_noimage', 0) || !$showImage)
  && $params->get('item_content', 0) != 0
) {
  echo '<div class="content">';

  if ($params->get('item_content') == 'intro') {
    // Show intro text
    echo $item->introtext;
  } else if ($params->get('item_content') == 'full') {
    // Show fi;; text
    /*
    echo htmlspecialchars(
      ((!empty($item->fulltext)) ? $item->fulltext : ''),
      ENT_COMPAT,
      'UTF-8'
    );
    */
    echo $item->fulltext;
  }

  echo '</div>';
}
// Show the readmore link
if ($params->get('item_readmore', true)) {
  echo '<p class="readmore">';
  echo '<a';
  echo ' href="' . $link . '"';
  //echo ' aria-label="' . htmlspecialchars($params->get('item_readmore_text', 'Read More'), ENT_COMPAT, 'UTF-8') . ': ' . $item->title . '"';
  echo ' aria-label="' . $params->get('item_readmore_text', 'Read More') . ': ' . $item->title . '"';
  echo '>';
  //echo htmlspecialchars($params->get('item_readmore_text', 'Read More'), ENT_COMPAT, 'UTF-8');
  echo $params->get('item_readmore_text', 'Read More');
  echo '</a>';
  echo '</p>';
}


if ($params->get('list_container', 0) == 'section') {
  echo '</article>';
} else if ($params->get('list_container', 0) == 'ul' || $params->get('list_container', 0) == 'ol') {
  echo '</li>';
} else {
  echo '</div>';
}
