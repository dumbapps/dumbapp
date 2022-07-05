<?php

function alert($msg=null) {
    if(isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];

        $_SESSION['msg'] = null;
        unset($_SESSION['msg']);
    }

    if($msg) {
        echo "<blockquote class=\"warn\"><p>$msg</p></blockquote>";
    }
}