<?php

namespace bdmtra\feedmeextension\services;

use bdmtra\feedmeextension\FeedMeMetaTagParser;
use bdmtra\feedmeextension\helpers\UrlHelper;

use Craft;
use craft\base\Component;

class MetaTagParser extends Component
{
    public static function collectMetaTagsFromUrls($urls) {
        $data = [];

        foreach ($urls as $url) {
            $data[] = UrlHelper::getUrlData($url);
        }

        return $data;
    }
}
