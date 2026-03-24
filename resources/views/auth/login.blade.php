<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Login TostaTech</title>
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 text-black">

                    <div class="px-5 ms-xl-4">
                        <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
                        <span class="h1 fw-bold mb-0">TostaTech</span>
                    </div>

                    <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

                        <form style="width: 23rem;" method="POST" action="{{ route('login') }}">
                            @csrf

                            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Inicio de sesión</h3>

                            {{-- Mostrar errores generales --}}
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                    @foreach($errors->all() as $error)
                                        <small>{{ $error }}</small><br>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Mostrar mensaje de éxito --}}
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Campo Email --}}
                            <div class="form-outline mb-4">
                                <label class="form-label" for="email">Correo electrónico</label>
                                <input type="email" id="email" name="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Campo Contraseña --}}
                            <div class="form-outline mb-4">
                                <label class="form-label" for="password">Contraseña</label>
                                <input type="password" id="password" name="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    required>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Checkbox Recordarme --}}
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Recordarme
                                </label>
                            </div>

                            <div class="pt-1 mb-4">
                                <button class="btn btn-info btn-lg w-100" type="submit">
                                    Iniciar sesión
                                </button>
                            </div>

                            <p class="small mb-5 pb-lg-2">
                                <a class="text-muted" href="#">¿Olvidaste tu contraseña?</a>
                            </p>
                            <p>¿No tienes cuenta? <a href="#" class="link-info">Regístrate aquí</a>
                            </p>
                        </form>

                    </div>

                </div>
                <div class="col-sm-6 px-0 d-none d-sm-block">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/img3.webp"
                        alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: left;">
                </div>
            </div>
        </div>
    </section>

    {{-- Scripts de Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>