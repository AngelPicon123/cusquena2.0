<?php
function verificarPermiso($permisosPermitidos) {
    session_start();
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $permisosPermitidos)) {
        exit();
    }
}
