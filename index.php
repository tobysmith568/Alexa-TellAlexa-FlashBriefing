<?php

header('Content-Type: application/json');

$result = get_default_result();

$xml = simplexml_load_file('https://www.reddit.com/r/TellAlexa.rss');

$result->titleText = 'The top post from r/TellAlexa';
$result->mainText = (string) $xml->entry[0]->title;
$result->redirectionUrl = 'https://reddit.com/r/TellAlexa/hot';

echo json_encode($result);

die();




function get_default_result() {
	$dt = new DateTime();
	$dt->setTimeZone(new DateTimeZone('UTC'));

	$result = new stdClass();
	$result->uid = 'urn:uuid:' . uniqid();
	$result->updateDate = $dt->format('Y-m-d\TH:i:s.\0\Z');
	
	return $result;
}