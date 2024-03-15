<?php

namespace bdmtra\feedmeextension\helpers;

use bdmtra\feedmeextension\FeedMeMetaTagParser;

use Craft;
use craft\base\Component;
use craft\helpers;

class XmlHelper extends Component
{
    public static function findItems($data) {
        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML($data);
        $items = $xmlDoc->getElementsByTagName('item');

        return $items;
    }

    public static function findUrls($data) {
        $items = XmlHelper::findItems($data);
        
        $urls = [];
        foreach ($items as $item) {
            $urls[] = $item->getElementsByTagName('link')->item(0)->nodeValue;
        }

        return $urls;
    }
}
