<!--
Online HTML, CSS and JavaScript editor to run code online.
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>Nueva Justificación</title>
</head>

<style>
    body,
    table,
    td,
    a {
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }

    table,
    td {
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
    }

    img {
        -ms-interpolation-mode: bicubic;
    }

    img {
        border: 0;
        height: auto;
        line-height: 100%;
        outline: none;
        text-decoration: none;
    }

    table {
        border-collapse: collapse !important;
    }

    body {
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    div[style*="margin: 16px 0;"] {
        margin: 0 !important;
    }
</style>

<body style="background-color: #f7f5fa; margin: 0 !important; padding: 0 !important;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#426899" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="480">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;">
                            <div style="display: block; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-size: 18px;"
                                border="0">Justificación-App</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#426899" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="480">
                    <tr>
                        <td bgcolor="#ffffff" align="left" valign="top"
                            style="padding: 30px 30px 20px 30px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; line-height: 48px;">
                            <h1 style="font-size: 32px; font-weight: 400; margin: 0;">¡Nueva Justificación!</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="480">
                    <tr>
                        <td bgcolor="#ffffff" align="left">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td colspan="2"
                                        style="padding-left:30px;padding-right:15px;padding-bottom:10px; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                                        <p>Estimado {{ "{$args['profesor']->nombre} {$args['profesor']->apellido_paterno} {$args['profesor']->apellido_materno}" }}.</p>
                                        <p>Espero que este correo le encuentre bien. Me dirijo a usted para notificarle
                                            que he adjuntado una nueva justificación para mi ausencia en las clases
                                            programadas para {{ $args['justificacion']->fecha_inicio}} a {{ $args['justificacion']->fecha_fin}}.</p>
                                        <p>Si es necesario, estaré encantado de proporcionar cualquier información
                                            adicional o documentación adicional que pueda necesitar. Por favor, hágamelo
                                            saber lo antes posible para poder colaborar en lo que sea necesario.</p>
                                        <p>Agradezco su atención y le pido disculpas por cualquier inconveniente que
                                            esta situación pueda ocasionar. Espero poder reincorporarme a las clases lo
                                            antes posible.</p>
                                        <p>Saludos cordiales,</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th align="left" valign="top"
                                        style="padding-left:30px;padding-right:15px;padding-bottom:10px; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        E-Mail</th>
                                    <td align="left" valign="top"
                                        style="padding-left:15px;padding-right:30px;padding-bottom:10px;font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        {{ $args['alumno']->email }}</td>
                                </tr>
                                <tr>
                                    <th align="left" valign="top"
                                        style="padding-left:30px;padding-right:15px;padding-bottom:10px; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        Alumno</th>
                                    <td align="left" valign="top"
                                        style="padding-left:15px;padding-right:30px;padding-bottom:10px;font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        {{ "{$args['alumno']->nombre} {$args['alumno']->apellido_paterno} {$args['alumno']->apellido_materno}" }}</td>
                                </tr>
                                <tr>
                                    <th align="left" valign="top"
                                        style="padding-left:30px;padding-right:15px;padding-bottom:30px; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        Grupo</th>
                                    <td align="left" valign="top"
                                        style="padding-left:15px;padding-right:30px;padding-bottom:30px;font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        {{ $args['grupo']->nombre}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td bgcolor="#ffffff" align="center"
                                        style="padding: 30px 30px 30px 30px; border-top:1px solid #dddddd;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="left" style="border-radius: 3px;" bgcolor="#426899">

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="480">
                    <tr>
                        <td bgcolor="#f4f4f4" align="left"
                            style="padding: 30px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;">
                            <p style="margin: 0;">Correo Enviado por "<a href="#" target="_blank"
                                    style="color: #111111; font-weight: 700;">Justificación-App<a>".</p>
                        </td>
                    </tr>
            </td>
        </tr>
    </table>

</body>

</html>
