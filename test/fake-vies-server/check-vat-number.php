<?php

$acceptFound = false;
$contentTypeFound = false;
foreach (getallheaders() as $name => $value) {
    switch (strtolower($name)) {
        case 'accept':
            if (!preg_match('/^application\/json(;\s*=[Uu][Tt][Ff]-8)?$/', $value)) {
                http_response_code(417);
                die("Invalid Accept request header: {$value}");
            }
            $acceptFound = true;
            break;
        case 'content-type':
            if (!preg_match('/^application\/json(;\s*=[Uu][Tt][Ff]-8)?$/', $value)) {
                http_response_code(417);
                die("Invalid Content-Type request header: {$value}");
            }
            $contentTypeFound = true;
            break;
    }
}
if (!$acceptFound) {
    http_response_code(417);
    die('Missing Accept request header');
}
if (!$contentTypeFound) {
    http_response_code(417);
    die('Missing Content-Type request header');
}
if (PHP_VERSION_ID < 70000) {
    /** @var string $HTTP_RAW_POST_DATA */
    $request = is_string($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
} else {
    $request = file_get_contents('php://input');
    if ($request === false) {
        http_response_code(417);
        die('Failed to read the request body');
    }
}
if ($request === '') {
    http_response_code(417);
    die('Empty request body');
}
if ($request === 'null') {
    $data = null;
} else {
    $data = json_decode($request, true);
    if ($data === null) {
        http_response_code(417);
        die('Invalid JSON received');
    }
}
if (!is_array($data)) {
    http_response_code(417);
    die('Wrong JSON received');
}

/**
 * @return string
 */
function getRequestDate()
{
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

    return $now->format('Y-m-d\TH:i:s.') . substr('000000' . $now->format('u'), -6) . 'Z';
}

$filename = preg_replace('/[^a-z0-9\-_\.]+/i', '_', isset($data['countryCode']) ? $data['countryCode'] : '') . '@' . (isset($data['vatNumber']) ? $data['vatNumber'] : '') . '.php';
$fullFilename = __DIR__ . '/responses/' . $filename;
if (file_exists($fullFilename)) {
    header('Content-Type: application/json;charset=UTF-8');
    echo json_encode(require $fullFilename);
} else {
    http_response_code(417);
    echo 'Unrecognized test case: missing file ' . $filename;
}
