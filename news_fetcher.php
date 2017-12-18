<?php

class news_wrapper {
  public $url;
  public $news_rss;

  function __construct() {
    $this->url = "https://news.google.com/news/rss/?gl=US&ned=us&hl=en&output=rss";
    $this->news_rss = simplexml_load_file($this->url);
  }

  function get_newsfeed() {
    if (!empty($this->news_rss)) {
      $i = 0;
        foreach ($this->news_rss->channel->item as $item)
        {
            preg_match('@src="([^"]+)"@', $item->description, $match);
            $parts = explode('<font size="-1">', $item->description);

            $news_feed[$i] = [
              'title' => (string) $item->title,
              'link' => (string) $item->link,
              'image' => isset($match[1]) ? $match[1] : null
            ];

            $i++;
        }
    }
    else {
      $news_feed[0]['title'] = "";
    }
    return $news_feed;
  }
}

?>
