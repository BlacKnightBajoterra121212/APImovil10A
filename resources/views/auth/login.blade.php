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

                    <div class="px-5 ms-xl-4">
                        <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
                        <span class="h1 fw-bold mb-0">Logo</span>
                    </div>

                    <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

                        <form style="width: 23rem;" action="/dashboard">

                            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Inicio de sesion</h3>

                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="email" id="form2Example18" class="form-control form-control-lg" />
                                <label class="form-label" for="form2Example18">Correo electronico</label>
                            </div>

                            <div data-mdb-input-init class="form-outline mb-4">
                                <input type="password" id="form2Example28" class="form-control form-control-lg" />
                                <label class="form-label" for="form2Example28">Contraseña</label>
                            </div>

                            <div class="pt-1 mb-4">
                                <button class="btn btn-info btn-lg btn-block" type="submit">
                                    Iniciar sesión
                                </button>
                            </div>

                            <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">Olvidaste tu contraseña?</a>
                            </p>
                            <p>No tienes cuenta? <a href="#!" class="link-info">Registrate aqui</a></p>

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
</body>

</html>