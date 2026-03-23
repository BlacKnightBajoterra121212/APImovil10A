<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <title>Login - TostaTech</title>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Lado izquierdo: Formulario */
        .login-section {
            background-color: #ffffff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-logo {
            color: #ffb700;
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: -1px;
        }

        .login-title {
            font-weight: 800;
            color: #000;
            margin-bottom: 1.5rem;
        }

        /* Inputs estilizados */
        .form-control-lg {
            border-radius: 10px;
            border: 2px solid #eee;
            font-size: 1rem;
        }

        .form-control-lg:focus {
            border-color: #ffb700;
            box-shadow: none;
        }

        /* Botón TostaTech (Negro con texto Naranja o viceversa) */
        .btn-tostatech {
            background-color: #000;
            color: #ffb700;
            border: none;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
            width: 100%;
        }

        .btn-tostatech:hover {
            background-color: #ffb700;
            color: #000;
            transform: translateY(-2px);
        }

        .link-tostatech {
            color: #ff7b00;
            text-decoration: none;
            font-weight: 600;
        }

        .link-tostatech:hover {
            text-decoration: underline;
            color: #000;
        }

        /* Imagen lateral con overlay naranja */
        .login-image-container {
            position: relative;
            padding: 0;
        }

        .login-image-container img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 123, 0, 0.4), rgba(255, 183, 0, 0.4));
        }
    </style>
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 login-section px-5">

                    <div class="px-xl-5 ms-xl-4">
                        <div class="brand-logo mb-4">
                            <i class="fas fa-fire me-2"></i>TostaTech
                        </div>

                        <form style="max-width: 400px;" action="/dashboard" method="GET">
                            <h2 class="login-title">¡Bienvenido de nuevo!</h2>
                            <p class="text-muted mb-4">Ingresa tus credenciales para acceder al panel.</p>

                            <div class="form-outline mb-4">
                                <label class="form-label fw-bold" for="email">Correo electrónico</label>
                                <input type="email" id="email" class="form-control form-control-lg" placeholder="ejemplo@tosta.com" required />
                            </div>

                            <div class="form-outline mb-4">
                                <label class="form-label fw-bold" for="password">Contraseña</label>
                                <input type="password" id="password" class="form-control form-control-lg" placeholder="••••••••" required />
                            </div>

                            <div class="pt-1 mb-4">
                                <button class="btn btn-tostatech btn-lg" type="submit">
                                    Iniciar Sesión
                                </button>
                            </div>

                            <p class="small mb-3">
                                <a class="text-muted" href="#!">¿Olvidaste tu contraseña?</a>
                            </p>
                            <p>¿No tienes cuenta? <a href="#!" class="link-tostatech">Regístrate aquí</a></p>
                        </form>
                    </div>

                </div>

                <div class="col-sm-6 d-none d-sm-block login-image-container">
                    <div class="overlay"></div>
                    <img src="{{ asset('img/tostadas.jpg') }}" alt="Login image">
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>