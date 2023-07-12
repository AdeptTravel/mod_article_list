<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Adept\Module\ArticleList\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class ArticlesModel extends \Joomla\Component\Content\Site\Model\ArticlesModel
{

    protected function getListQuery()
    {
        //$lang = \Joomla\CMS\Factory::getLanguage();
        $app = \Joomla\CMS\Factory::getContainer()->get(\Joomla\CMS\Application\SiteApplication::class);
        $option = $app->input->getCmd('option', '');
        $view = $app->input->getCmd('view', '');
        $query = parent::getListQuery();

        if ($option == 'content' && $view == 'article') {

            $id = $app->input->getCmd('id', '');

            if (!empty($id) && $id > 0 && is_numeric($id)) {
                $db = $this->getDatabase();

                $query
                    ->where($db->quoteName('a.id') . 'IS NOT :articleid')
                    ->bind(':articleid', $id);
            } else {
                echo '<h1>B: Nope</h1>';
            }
        } else {
            echo '<h1>A: Nope</h1>';
        }

        return $query;
    }
}
