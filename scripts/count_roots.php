<?php
$html = file_get_contents(__DIR__.'/render_output.html');
$dom = new DOMDocument();
@$dom->loadHTML($html);
$body = $dom->getElementsByTagName('body')->item(0);
$count = 0;
foreach ($body->childNodes as $child) {
    if ($child->nodeType == XML_ELEMENT_NODE) {
        if ($child->tagName === 'script') continue;
        $count++;
    }
}
echo "Root element count: $count\n";
foreach ($body->childNodes as $child) {
    if ($child->nodeType == XML_ELEMENT_NODE) echo "- ".$child->tagName."\n";
echo "\n-- Detailed nodes --\n";
foreach ($body->childNodes as $i => $child) {
    if ($child->nodeType == XML_ELEMENT_NODE) {
        $outer = $dom->saveHTML($child);
        echo "Node $i (tag={$child->tagName}):\n";
        echo substr($outer,0,500) . "\n---\n";
    }
}
}
