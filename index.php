<?php

header('Content-Type: application/json');

$result = get_default_result();

$xml = simplexml_load_file('https://www.reddit.com/r/TellAlexa.rss');

if ($xml === null || $xml === false || sizeof($xml->entry) === 0) {
	$result->titleText = 'Unknown error reading r/TellAlexa';
	$result->mainText = 'Sorry, I am currently unable to reach r slash tell alexa';
	$result->redirectionUrl = 'https://reddit.com/r/TellAlexa/hot';
}
else {
	$result->titleText = 'The top post from r/TellAlexa';
	$result->mainText = (string) $xml->entry[0]->title;
	$result->redirectionUrl = 'https://reddit.com/r/TellAlexa/hot';
}

echo json_encode($result);

die();




function get_default_result() {
	$dt = new DateTime();
	$dt->setTimeZone(new DateTimeZone('UTC'));

	$result = new stdClass();
	$result->uid = 'urn:uuid:' . get_uuid();
	$result->updateDate = $dt->format('Y-m-d\TH:i:s.\0\Z');
	
	return $result;
}

function get_uuid() {
	$data = random_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}