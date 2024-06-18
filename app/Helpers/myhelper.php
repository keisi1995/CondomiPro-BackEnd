<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Str;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Token;

function getValueToken($request, $key) {
    $token = new Token($request->bearerToken());
    $decodedToken = JWTAuth::decode($token);
    return $decodedToken[$key];
}

function getMessageApi() {
    return [
        'required' => 'El campo :attribute es obligatorio.',
        'string' => 'El campo :attribute debe ser una cadena.',
        'numeric' => 'El campo :attribute debe ser numérico.',
        'gt' => 'El campo :attribute debe ser mayor que 0.',
        'min' => 'El campo :attribute debe tener al menos :min caracteres.',
        'max' => 'El campo :attribute debe tener como maximo :max caracteres.',
        'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
        'between' => 'El campo :attribute debe estar entre :min y :max.',
        'unique' => 'El campo :attribute ya se encuentra registrado.',
        'confirmed' => 'La confirmación del campo :attribute no coincide.'
        // Agrega más mensajes según sea necesario
    ];
}

function validateParameter($value) {
    if ($value === 'undefined') {
        return null;
    }
    return $value;
}

function sendEmail($setFrom, $subject, $body, $altBody, $arrAddAddress, $arrAddCC = [], $arrAddAttachment = null)
    {
    try {
        $rst          = new \stdClass;
        $rst->success = true;
        $rst->message = '';

        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;               //Enable verbose debug output
        $mail->isSMTP();                                  //Send using SMTP
        $mail->Host       = env('MAIL_HOST');             //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                         //Enable SMTP authentication
        $mail->Username   = env('MAIL_USERNAME');        //SMTP username
        $mail->Password   = env('MAIL_PASSWORD');        //SMTP password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  //Enable implicit TLS encryption
        $mail->SMTPSecure = 'tls';  //Enable implicit TLS encryption
        $mail->Port       = env('MAIL_PORT');             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($setFrom, 'CONDOMIPRO');

        for ($i = 0; $i < count($arrAddAddress); $i++) {
            $mail->addAddress($arrAddAddress[$i]); //Name is optional
            // $mail->addAddress('keisi_12_6@hotmail.com', 'Joe User'); //Add a recipient
        }

        for ($i = 0; $i < count($arrAddCC); $i++) {
            $mail->addCC($arrAddCC[$i]);
        }
        
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addBCC('bcc@example.com');
        
        //Attachments
        if ($arrAddAttachment) {
            for ($i = 0; $i < count($arrAddAttachment); $i++) {
                $mail->addAttachment($arrAddAttachment[$i]); //Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); //Optional name
            }
        }

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        $rst->message = 'Message has been sent';
    } catch (Exception $e) {
        $rst->message =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $rst->success = false;
    }
    return $rst;
}

function generarHtmlCorreo($correo, $clave) {
    $html = '<html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: \'Arial\', sans-serif;
                        background-color: #000;
                        color: #fff;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        padding: 20px;
                        background-color: #111;
                        border-radius: 10px;
                        box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
                    }
                    .header {
                        text-align: center;
                        color: #fff;
                        font-size: 32px;
                        margin-bottom: 20px;
                    }
                    .content {
                        font-size: 18px;
                        line-height: 1.6;
                        color: #fff;
                    }
                    .button-container {
                        text-align: center;
                        margin-top: 20px;
                    }
                    .button {
                        display: inline-block;
                        padding: 15px 30px;
                        background-color: #007BFF; /* Set button color to celeste */
                        color: #fff; /* Set text color to white */
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 18px;
                    }
                    .footer {
                        font-size: 14px;
                        color: #fff; /* Set text color to white */
                        text-align: center;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2 class="header">¡BIENVENIDO A CONDOMIPRO!</h2>
                    <div class="content">
                        <p>¡Hola! ' . $correo . ' , Te damos la bienvenida a CondomiPro, la plataforma que hará más fácil y eficiente la administración de tu condominio.</p>
                        <p>Con CondomiPro, podrás acceder a funciones como:</p>
                        <ul>
                            <li>Gestión de pagos y facturas.</li>
                            <li>Comunicación directa con otros residentes.</li>
                            <li>Reservas de áreas comunes.</li>
                            <li>Notificaciones importantes de la administración.</li>
                        </ul>
                        <p>Tu contraseña de acceso: <strong>' . $clave . '</strong></p>
                        <p>Para comenzar, haz clic en el siguiente botón:</p>
                        <div class="button-container">
                            <a href="' . env('APP_URL_FRONTEND') . '" class="button">Iniciar Sesión</a>
                        </div>
                    </div>
                    <div class="footer">
                        <p>Gracias por elegir CondomiPro. Estamos emocionados de tenerlo con nosotros y esperamos que disfrute de nuestra plataforma.</p>
                    </div>
                </div>
            </body>
        </html>';
        return $html;
}

function generarClaveRandom($longitud = 10) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $claveAleatoria = '';

    for ($i = 0; $i < $longitud; $i++) {
        $caracter = $caracteres[rand(0, strlen($caracteres) - 1)];
        $claveAleatoria .= $caracter;
    }

    return $claveAleatoria;
}