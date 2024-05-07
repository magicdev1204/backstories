<?php
// URL of the webpage you want to fetch
// $url = 'https://www.timeanddate.com/worldclock/';
$url = 'https://www.upwork.com/';

// Fetch the content of the webpage
$html = file_get_contents($url);

if (file_put_contents("./file.html", $html . PHP_EOL, FILE_APPEND) === false) {
    echo "Error: Failed to write to log file.";
    return;
}

// Create a DOMDocument object
$dom = new DOMDocument();


// Load the HTML content into the DOMDocument
libxml_use_internal_errors(true); // Suppress HTML5 parsing errors
$dom->loadHTML($html);
libxml_clear_errors();

// Create an XML structure for the RSS feed
$rss = new DOMDocument('1.0', 'UTF-8');
$rss->formatOutput = true;

// Create the root element <rss>
$rssRoot = $rss->createElement('rss');
$rssRoot->setAttribute('version', '2.0');
$rss->appendChild($rssRoot);

// Create the <channel> element
$channel = $rss->createElement('channel');
$rssRoot->appendChild($channel);

// Find RSS feed elements by class name or any other suitable method
var_dump($dom->getElementsByTagName('a'));exit();
$items = $dom->getElementsByTagName('item');

foreach ($items as $item) {
    // Extract title, link, and description from each item
    $title = '';
    $link = '';
    $description = '';

    // Adjust the parsing logic based on the structure of the HTML content
    // For example:
    $titleElement = $item->getElementsByTagName('title')->item(0);
    $title = $titleElement ? $titleElement->nodeValue : '';

    $linkElement = $item->getElementsByTagName('link')->item(0);
    $link = $linkElement ? $linkElement->nodeValue : '';

    $descriptionElement = $item->getElementsByTagName('description')->item(0);
    $description = $descriptionElement ? $descriptionElement->nodeValue : '';

    // Create the <item> element
    $rssItem = $rss->createElement('item');

    // Add title
    $titleElement = $rss->createElement('title', $title);
    $rssItem->appendChild($titleElement);

    // Add link
    $linkElement = $rss->createElement('link', $link);
    $rssItem->appendChild($linkElement);

    // Add description
    $descriptionElement = $rss->createElement('description', $description);
    $rssItem->appendChild($descriptionElement);

    // Add the <item> element to the <channel>
    $channel->appendChild($rssItem);
}

// Output the RSS feed
echo $rss->saveXML();
// var_dump($rss);
?>
