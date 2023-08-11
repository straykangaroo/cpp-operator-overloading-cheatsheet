#!/usr/bin/php
<?php
// extract_body.php: extract the body tag and its content from an html file


if( $argc != 2 ) {
    exit("usage: " . $argv[0] . " <html_file>\n");
} else {
    $file_name = $argv[1];
}

$dom = new DOMDocument();
if( (@$dom->loadHTMLFile($file_name,LIBXML_DTDVALID|LIBXML_HTML_NOIMPLIED)) === FALSE ) {
    exit("error: cannot open/load html file: " . $file_name . "\n");
}

$node_list = $dom->getElementsByTagName("body");
if( $node_list->count() != 1 ) {
    exit("error: zero or more than one <body> tags in " . $file_name . "\n");
}

$body = $node_list[0];

$output_html = $dom->saveHTML($body);
if( $output_html === FALSE ) {
    exit("error: cannot output html body content\n");
}
echo $output_html;

?>
