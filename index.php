<?php

// set_include_path() ... includeやrequireで読み込むファイルの検索パスを設定する
// get_include_path() ... 現在のinclude_path設定を取得する (.:/usr/share/php)
// PATH_SEPARATOR ... OSによって異なるパス区切り文字を返す
// realpath() ... 絶対パスを取得する

// プロジェクトのルートディレクトリをinclude_pathに追加
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/..')); 

include 'vendor/autoload.php';

$DEBUG = true;

// REQUEST_URIが静的リソースのパスと一致する場合、falseを返す
if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

// ルーティング
$routes = include 'Routing/routes.php';

// リクエストURLを取得
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// 先頭のスラッシュを削除
$path = ltrim($path, '/');

if (isset($routes[$path])) {
    $renderer = $routes[$path]();
    try {
        // ヘッダーフィールドを設定
        foreach ($renderer->getFields() as $name => $value) {
            // ヘッダーに設定する値を無害なものにサニタイズ
            $sanitized_value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // サニタイズされた値がもとの値と一致する場合、ヘッダーに設定
            if ($sanitized_value === $value) {
                header("{$name}: {$value}");
            } else {
                // 一致しない場合、ログに記録するか処理する
                // エラー処理によっては例外をスローするか、デフォルトのまま続行
                http_response_code(500);
                if ($DEBUG) {
                    print ("Failed setting header - original value: '{$value}', sanitized value: '{$sanitized_value}'");
                }
                exit();
            }
        }
        print ($renderer->getContent());
    } catch (Exception $e) {
        http_response_code(500);
        print "Internal error, please contact the admin. <br>";

        if ($DEBUG) {
            print ($e->getMessage());
        }
    }
} else {
    // 一致するルートがない場合、404 Not Foundページを表示
    http_response_code(404);
    include '/Views/layout/header.php';
    include '/Views/404.php';
    include '/Views/layout/footer.php';
}