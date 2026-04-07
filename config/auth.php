<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin() {
    if (!isset($_SESSION['login'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function checkLoginRoot() {
    if (!isset($_SESSION['login'])) {
        header("Location: auth/login.php");
        exit;
    }
}

function allowRoles(array $roles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        echo "Akses ditolak!";
        exit;
    }
}