<?php

header('Content-Type: application/json');

$xml = simplexml_load_file('https://www.reddit.com/r/TellAlexa.rss');

$dt = new DateTime();
$dt->setTimeZone(new DateTimeZone('UTC'));

$result = new stdClass();
$result->uid = 'urn:uuid:' . uniqid();
$result->updateDate = $dt->format('Y-m-d\TH:i:s.\0\Z');
$result->mainText = (string) $xml->entry[0]->title;
$result->redirectionUrl = 'https://reddit.com/r/TellAlexa/hot';

$result = get_default_result();

echo json_encode($result);

die();