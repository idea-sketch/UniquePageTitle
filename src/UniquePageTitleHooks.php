<?php

/**
 * This file is part of the MediaWiki extension UniquePageTitle.
 *
 * UniquePageTitle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * UniquePageTitle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with UniquePageTitle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup extensions
 * @author Uwe Schützenmeister (Filburt)
 * @copyright Copyright ©2024, https://idea-sketch.com
 */

namespace MediaWiki\Extension\UniquePageTitle;

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\MediaWikiServices;
use Title;

class UniquePageTitleHooks implements ParserFirstCallInitHook
{
    /**
     * Register any render callbacks with the parser
     * 
     * @param Parser $parser
     */
    public function onParserFirstCallInit($parser)
    {
        // Create a function hook associating the "uniquepagetitle" magic word with renderParamlink()
        $parser->setFunctionHook('uniquepagetitle', [self::class, 'renderUniquepagetitle']);
    }

    /**
     * Render the output of {{#uniquepagetitle:}}
     * 
     * @param Parser $parser
     * @param string $pagetitle
     * @return array
     */
    public static function renderUniquepagetitle($parser, $pagetitle)
    {
        // If no title is specified, the function returns nothing
        if (empty($pagetitle)) {
            return array();
        }

        // Parse the title to get namespace and title
        $title = Title::newFromText($pagetitle);
        $namespace = $title->getNamespace();

        // Normalize $title
        $titleText = ucfirst(str_replace(' ', '_', $title->getText()));

        $dbr = MediaWikiServices::getInstance()->getDBLoadBalancerFactory()->getMainLB()->getConnection(DB_PRIMARY);

        $count = 0;
        $newPageTitle = $pagetitle;

        // Check whether the original title exists and if so, append a suffix to make it unique
        while (self::checkArchived($namespace, $titleText . ($count > 0 ? "_$count" : ''), $dbr)) {
            $count++;
            // Append count to the title and check again
            $newPageTitle = $title->getPrefixedText() . ($count > 0 ? "_$count" : '');
        }

        // Return the unique page title
        return array($newPageTitle);
    }

    public static function checkArchived($namespace, $pagetitle, $dbr)
    {
        // Normalize pagetitle by replacing spaces with underscores
        $pagetitle = str_replace(' ', '_', $pagetitle);

        // Check whether the title is present in the page-table
        $pageRes = $dbr->selectField(
            'page',
            '1',
            array('page_title' => $pagetitle, 'page_namespace' => $namespace),
            __METHOD__
        );

        // Check whether the title is present in the archive-table
        $archiveRes = $dbr->selectField(
            'archive',
            '1',
            array('ar_title' => $pagetitle, 'ar_namespace' => $namespace),
            __METHOD__
        );

        // If the title is present in one of the tables, return true, otherwise false
        return $pageRes !== false || $archiveRes !== false;
    }

}
