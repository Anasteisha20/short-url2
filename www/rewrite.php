<?
include '../www/api/classes/FormatUrl/FormatUrl.php';
include '../www/api/classes/FormatUrl/Validator.php';

$result = new Api\Classes\FormatUrl\FormatUrl();
$link = $result->RedirectLink($_GET['route_short']);

if (json_decode($link)->error) {
    header('Location: http://short-url/');
} else {
    if (strpos($link, 'https://') === false && strpos($link, 'http://') === false) {
        $link = 'https://'.$link;
    }
    header('Location: '.$link);
}

?>