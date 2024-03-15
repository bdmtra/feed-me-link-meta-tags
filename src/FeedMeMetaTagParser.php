<?php

namespace bdmtra\feedmeextension;

use bdmtra\feedmeextension\helpers\XmlHelper as XmlHelper;
use bdmtra\feedmeextension\services\MetaTagParser as MetaTagParser;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\feedme\events\FeedDataEvent;
use craft\feedme\services\DataTypes;

use yii\base\Event;

class FeedMeMetaTagParser extends Plugin
{
    public static $plugin;

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /*
     * Collect URLs into this array.
     *
     * @var array
     */
    public $urls = [];

    /*
     * Collect meta tags into this array.
     *
     * @var array
     */
    public $data = [];

    /*
     * Collect feed items into this array.
     *
     * @var array
     */
    public $items = [];

    /*
     * Count the items in the feed
     *
     * @var int
     */
    public $count = 0;
 
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(DataTypes::class, DataTypes::EVENT_AFTER_FETCH_FEED, function(FeedDataEvent $event) {
            if ($event->response['success']) {
                $this->_processFeed($event);
            }
        });

        Event::on(DataTypes::class, DataTypes::EVENT_AFTER_PARSE_FEED, function(FeedDataEvent $event) {
            if ($event->response['success']) {
                $this->_enhanceFeed($event);
            }
        });
    }

    // Private Methods
    // =========================================================================

    private function _processFeed($event) {
        $data = $event->response['data'];
        $this->items = XmlHelper::findItems($data);
        $this->count = count($this->items);
        $this->urls = XmlHelper::findUrls($data);

        $metaTags = MetaTagParser::collectMetaTagsFromUrls($this->urls);
        foreach ($metaTags as $key => $value) {
            $this->data[] = $value;
        }
    }

    private function _enhanceFeed($event) {
        for ($i = 0; $i < $this->count; $i++) {
            foreach ($this->data[$i] as $key => $value) {
                $event->response['data'][$i][$key] = $value;
            }
        }
    }
}
