# session login

まずindex.phpがよばれてsessionのstatusを確認します。
```
index.php 

<?php
session_start();

if ($_SESSION['loginStatus'] == false) {
    header("Location: ./login_form.php");
    exit;
}
?>
```
最初はfalseなのでheader関数でlogin_formに飛ばされる

login_form.php
主にフォームの処理(無条件にform.phpに飛ばす)
```
login_form.php
(4行目) $sessionEmail = (!empty($_SESSION['email'])) ? $_SESSION['email']: "";

(24行目) <input type=“text” name="email" type="email" required value=<?php echo $sessionEmail; ?>><br />
                                                              //重要なのはsessionが空じゃなければvalueにそのアドレスを入れる所。
                                                              //こうするとアドレスをいちいち打ち直さなくて良いです！！
//                               
//  _._ _..._ .-',     _.._(`))
// '-. `     '  /-._.-'    ',/
//    )                     '.
//   / _    _    |             |
//  |  q    p    /              |
//     .-.                     ;  
//   '-('' ).-'       ,'       ;
//      '-;           |      .'                        
//         | 7  .__  _.-   
//         | |  |  ``/  /`  /
//        /,_|  |   /,_/   /
//           /,_/      '`-'
//
```

login.php
ログイン認証を行う
ログインできればindex.phpに飛ばしできなければエラーメッセージを表示する。

この時点でsessionにtrueが入るのでindex.phpにアクセスができる。

こういった流れで動いています。

