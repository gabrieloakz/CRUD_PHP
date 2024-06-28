<?php

function ligarBD($host, $user, $pass, $bd)
{
    $con = new mysqli($host, $user, $pass, $bd);

    if ($con->connect_errno != 0) {
        echo "Ocorreu um erro de ligação à base de dados:", $con->connect_errno;
        return FALSE;
    }
    return $con;
}
?>
