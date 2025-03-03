<?php

class UserView {
    public function renderHome() {
        header("Content-Type: text/html; charset=UTF-8");
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Iniciar Sesion</title>
            <link rel="stylesheet" href="/app/views/styles/userStyle.css">
            <script src="https://kit.fontawesome.com/a83aa45581.js" crossorigin="anonymous"></script>
        </head>
        <body>

        <div class="container">
            <form id="login-form">
                <h1>Iniciar Sesion</h1>
                <input type="text" name="username" placeholder="Usuario o Email">
                <input type="password" name="password" placeholder="Contraseña">
                <button type="submit" class="auth-button" data-form="login">Iniciar Sesion</button>
                <div class="link">
                    ¿No tenes cuenta? <a href="#" class="go-to-auth" data-target="register">Registrate</a>
                </div>
            </form>

            <form id="register-form" style="display: none" method="POST">
                <h1>Registro</h1>
                <input type="text" name="username" placeholder="Usuario">
                <input type="password" name="password" placeholder="Contraseña">
                <button type="submit" class="auth-button" data-form="register">Registrarse</button>
                <div class="link">
                    ¿Ya tenes cuenta? <a href="#" class="go-to-auth" data-target="login">Inicia sesion</a>
                </div>
            </form>
        </div>

        <script src="/app/views/scripts/userScript.js"></script>
        </body>
        </html>
        <?php
    }
}
?>
