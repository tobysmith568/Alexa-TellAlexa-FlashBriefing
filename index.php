<?php

header('Content-Type: application/json');

$result = get_default_result();

$feed = strtolower($_GET['feed']);

if ($feed === null || $feed == false) {
	$result->titleText = 'Unknown Input!';
	$result->mainText = 'Sorry, You need to give me a feed type';
	$result->redirectionUrl = 'https://reddit.com/r/TellAlexa';
	send_result($result);
}

switch($feed) {
	
	case 'hot':
	case 'new':
	case 'controversial':
	case 'top':
	case 'rising':
		$xml = simplexml_load_file("https://www.reddit.com/r/TellAlexa/$feed/.rss");
		break;
		
	default:
		$result->titleText = 'Unknown Input!';
		$result->mainText = "Sorry, I am not aware of the subreddit feed type $feed";
		$result->redirectionUrl = 'https://reddit.com/r/TellAlexa';
		send_result($result);
		break;
}

if ($xml === null || $xml === false) {
	$result->titleText = 'Unknown error reading r/TellAlexa';
	$result->mainText = 'Sorry, I am currently unable to reach R slash tell Alexa';
	$result->redirectionUrl = 'https://reddit.com/r/TellAlexa';
	send_result($result);
}

if (sizeof($xml->entry) === 0) {
	$result->titleText = "The top $feed post from r/TellAlexa";
	$result->mainText = "Sorry, currently there are no $feed posts!";
	$result->redirectionUrl = "https://reddit.com/r/TellAlexa/$feed";
	send_result($result);
}

$post = cleanString((string) $xml->entry[0]->title);

if (strlen($post) > 140)
{
	$post = substr($post, 0, 140);
}

$result->titleText = "The top $feed post from r/TellAlexa";
$result->mainText = $post;
$result->redirectionUrl = "https://reddit.com/r/TellAlexa/$feed";

send_result($result);

//	Functions
//	=========

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

function cleanString($text) {
    $utf8 = array(
        '/[áàâãªä]/u'     => 'a',
        '/[ÁÀÂÃÄ]/u'      => 'A',
        '/[ÍÌÎÏ]/u'       => 'I',
        '/[íìîï]/u'       => 'i',
        '/[éèêë]/u'       => 'e',
        '/[ÉÈÊË]/u'       => 'E',
        '/[óòôõºö]/u'     => 'o',
        '/[ÓÒÔÕÖ]/u'      => 'O',
        '/[úùûü]/u'       => 'u',
        '/[ÚÙÛÜ]/u'       => 'U',
        '/ç/'             => 'c',
        '/Ç/'             => 'C',
        '/ñ/'             => 'n',
        '/Ñ/'             => 'N',
		'/ \& /u'         => ' and ',
		'/ \@ /u'         => ' at ',
        '/–/'             => '-',
        '/[\’\‘\‹\›\‚]/u' => '\'',
        '/[\“\”\«\»\„]/u' => '"',
        '/ /'             => ' ',
			'/[^ a-zA-Z1-9\!\"\\\'\£\$\€\¥\%\-\;\:\,\.\?\=\+\-\*\/]/u' => ''
    );
	
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

function send_result($result) {
	echo json_encode($result);
	die();
}