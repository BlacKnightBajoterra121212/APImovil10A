# API móvil (aislada del sistema web)

Esta carpeta contiene una capa REST para consumo móvil sin alterar la lógica del sistema web existente.

## Estructura

- `routes/api.php`: rutas de la API (`/api/v1/...`).
- `Controllers/AuthController.php`: login, logout y usuario autenticado.
- `Services/AuthService.php`: reutiliza la autenticación actual (`Auth::validate`) con `status=active`.
- `Services/ApiTokenService.php`: emisión y revocación de token Bearer usando cache.
- `Middleware/AuthenticateApiSession.php`: protege endpoints privados con Bearer token.
- `Responses/ApiResponse.php`: formato uniforme de respuestas JSON.

## Endpoints iniciales

- `POST /api/v1/login`
- `POST /api/v1/logout` (requiere `Authorization: Bearer <token>`)
- `GET /api/v1/me` (requiere `Authorization: Bearer <token>`)

## Flujo de consumo para app móvil

1. La app envía email/password a `POST /api/v1/login`.
2. Si es correcto, recibe `access_token`.
3. La app guarda el token de forma segura (por ejemplo, Keychain/Keystore).
4. La app envía `Authorization: Bearer <token>` en endpoints privados (`/me`, `/logout`).
5. Al cerrar sesión, llamar `POST /api/v1/logout` para invalidar el token.

## Ejemplo request login

```json
{
  "email": "usuario@empresa.com",
  "password": "secret123"
}
```

## Ejemplo response login exitoso

```json
{
  "success": true,
  "message": "Login exitoso.",
  "data": {
    "user": {
      "id": 10,
      "name": "María López",
      "email": "usuario@empresa.com",
      "status": "active",
      "role": "Administrador",
      "id_role": 1
    },
    "auth": {
      "access_token": "TOKEN_GENERADO",
      "token_type": "Bearer",
      "expires_in": 604800
    }
  },
  "errors": null
}
```

## Ejemplo response login inválido

```json
{
  "success": false,
  "message": "Las credenciales no son válidas o el usuario está inactivo.",
  "data": null,
  "errors": {
    "credentials": [
      "Credenciales inválidas."
    ]
  }
}
```
