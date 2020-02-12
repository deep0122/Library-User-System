<?php
    session_start();
    #if (isset($_SESSION['user_card'])) {
    echo "UC: ".($_SESSION['user_card']);
    if($_SESSION['user_card'] != ''){
    
    }else {
        header('Location: https://web.njit.edu/~dsp49/assign4.html');
        die();
    }
?>
