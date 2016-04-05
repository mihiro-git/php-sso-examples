<?php

require_once __DIR__ . '/functions.php';
require_logined_session();

// CSRFトークンを検証
if (!validate_token(filter_input(INPUT_GET, 'token'))) {
    // 「400 Bad Request」
    header('Content-Type: text/plain; charset=UTF-8', true, 400);
    exit('トークンが無効です');
}

// セッションファイルの破棄
session_destroy();

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<title>ログアウトしました</title>
<h1>ログアウトしました</h1>
<iframe src="http://localhost:8080/" style="visibility: hidden;"></iframe>
<iframe src="http://127.0.0.1:8081/" style="visibility: hidden;"></iframe>
<p>
    JavaScriptが無効の場合は<a href="/">こちら</a>をクリックしてください．
</p>
<script>
    new Promise(r => addEventListener('DOMContentLoaded', r))
    // DOMを読み込み終わるまで待ってから実行
    .then(() => Promise.all(
        // 全てのiframeに対して適用
        Array
        .from(document.querySelectorAll('iframe'))
        .map(iframe => {
            // a要素を使ってURLからオリジンを抽出
            let a = document.createElement('a');
            a.href = iframe.src;
            let origin = a.origin;
            // メッセージを定義
            let message = {
                operation: 'destroy-session-id'
            };
            return new Promise(r => iframe.addEventListener('load', r))
            // iframeを読み込み終わるまで待ってから実行
            .then(() => iframe.contentWindow.postMessage(JSON.stringify(message), origin));
        })
    ))
    // 全てのiframeに対してメッセージを送信し終えてから実行
    .then(() => location.replace('/'))
    .catch(e => console.error(e));
</script>
