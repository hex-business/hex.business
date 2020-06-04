<?php

require_once 'session.php';

class Base
{
    public function verifyToken() {

        if(!isset($_POST['token']) || empty(trim($_POST['token']))) {
            throw new InvalidArgumentException("token cannot be empty");
        }
        $token = trim($_POST['token']);
        if ($token!==$_SESSION['token']) {
            throw new InvalidArgumentException("Invalid token or Token Expired");
        }
    }
}

