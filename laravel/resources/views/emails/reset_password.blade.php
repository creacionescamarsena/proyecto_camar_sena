<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Restablecer contraseña</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif; color:#222;">
  <div style="max-width:600px;margin:0 auto;padding:20px;">
    <h2>Hola {{ $name }}</h2>
    <p>Recibimos una solicitud para restablecer la contraseña asociada a este correo.</p>
    <p>Haz clic en el siguiente botón para restablecer tu contraseña:</p>
    <p style="text-align:center;margin:24px 0;"><a href="{{ $url }}" style="background:#506d2f;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;">Restablecer contraseña</a></p>
    <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
    <p>Saludos,<br>Equipo Creaciones Camar</p>
  </div>
</body>
</html>
