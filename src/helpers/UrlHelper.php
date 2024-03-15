<?php

namespace bdmtra\feedmeextension\helpers;

use bdmtra\feedmeextension\FeedMeMetaTagParser;

use Craft;
use craft\base\Component;
use craft\helpers;

class UrlHelper extends Component
{
    public static function getUrlData($url)
    {
        $result = false;

        $contents = UrlHelper::getUrlContents($url);

        if (isset($contents) && is_string($contents))
        {
            $metaTags = null;
            $metaProperties = null;

            preg_match_all(
                '/(.*?(name|property)="(.*?)").*?(content|value)="(.*?)"/',
                $contents,
                $match
            );

            $names = $match[3];
            $values = $match[5];

            $result = array_combine($names, $values);
        }

        return $result;
    }

    public static function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0)
    {
        $result = false;

        $contents = @file_get_contents($url);

        // Check if we need to go somewhere else

        if (isset($contents) && is_string($contents))
        {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1)
            {
                if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections)
                {
                    return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
                }

                $result = false;
            }
            else
            {
                $result = $contents;
            }
        }

        return $contents;
    }
}
