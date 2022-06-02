<?
include '../api/classes/FormatUrl/FormatUrl.php';
include '../api/classes/FormatUrl/Validator.php';

if (isset($_POST["text"])){
    $link = $_POST["text"];
    $result = new Api\Classes\FormatUrl\FormatUrl();
    $result = $result->checkUrl($link);
    echo $result;
}

?>