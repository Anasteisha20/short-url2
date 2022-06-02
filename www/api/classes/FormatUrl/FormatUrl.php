<?
namespace Api\Classes\FormatUrl;
use PDO;
use Api\Classes\FormatUrl\Validator;

class FormatUrl
{
    public  $link, $short_url,$pdo;

    function __construct()
    {
        try {
            $user = 'root';
            $password = 'root';
            $db = 'url';
            $host = 'localhost';
            $charset = 'utf8';
            $pdo = new PDO("mysql:host=$host;port=3307;dbname=$db;cahrset=$charset", $user, $password, array(
                PDO::ATTR_PERSISTENT => true
            ));
            $this->pdo = $pdo;
            return  $pdo;
        } catch (PDOException $e) {
            return json_encode(["error" =>
                [
                    ["code" => $e->getCode()],
                    ["message" => "Нет связи с базой данных"]
                ]
            ]);
        }
    }

    public function checkUrl($link)
    {
        $this->link=$link;

        if (!Validator::getResponseCode($this->link)) {
            return json_encode(["error" => [
                ["status" => 400],
                ["message" => "Проверьте правильность введенной ссылки "]
            ]]);
        }

        $short_table = $this->selectDB('short', 'url', $this->link);

        if ($short_table) {
            return json_encode([ 'short' => $short_table ]);
        } else {
            $this->generationCode();
            return json_encode(['short' => $this->short_url]);
        }

    }

    public function selectDB($result_column, $select_column, $val)
    {
        $sql = 'SELECT '.$result_column.' FROM short_url WHERE '.$select_column.' = :code';
        $query = $this->pdo -> prepare($sql);
        $query -> execute(['code' => $val] );

        if($row = $query->fetch(PDO::FETCH_ASSOC)) {
            return $row[$result_column];
        }
    }

    public function generationCode()
    {
        $code = substr(bin2hex(openssl_random_pseudo_bytes(3)), 0, 5);

        $short_table = $this->selectDB('short', 'short', $code);

        if ($short_table) {
            $this->generationCode();
        } else {
            $this->insertShort($code);
        }
    }

    public function insertShort($gen_code)
    {
        $sql = 'INSERT INTO short_url (url, short) VALUES (:url, :short)';

        $query = $this->pdo -> prepare($sql);
        $query -> execute(['url' => $this->link, 'short' => $gen_code]);
        $this->short_url = $gen_code;
        return $gen_code;
    }

    public function RedirectLink($val)
    {
        $link = $this->selectDB('url', 'short', $val);
        if (!$link) {
            return  json_encode(["error" => [
                ["status" => 404],
                ["message" => "Такой ссылки нет : "]
            ]]);
        } else {
            return $link;
        }
    }

}