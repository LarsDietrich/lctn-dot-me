<?php
# Logging in with Google accounts requires setting special identity, so this example shows how to do it.
require 'openid.php';
try {
    if(!isset($_GET['openid_mode'])) {
        if(isset($_GET['login'])) {
            $openid = new LightOpenID;
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            header('Location: ' . $openid->authUrl());
        }
        
?>
<form action="?login" method="post">
    <button>Login with Google</button>
</form>
<?php
    } elseif($_GET['openid_mode'] == 'cancel') {
?>
<form action="?login" method="post">
    <button>Login with Google</button>
</form>
<?php
    	    } else {
        $openid = new LightOpenID;
        echo ($openid->validate() ? $openid->identity: 'FAILED');
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
?>
