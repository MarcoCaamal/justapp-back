<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nueva Justificacion</title>
</head>
<body>
    <h1>Usted tiene una nueva justificacion</h1>
    <p><b>Alumno:</b> {{ "{$args['alumno']->nombre} {$args['alumno']->apellido_paterno} {$args['alumno']->apellido_materno}"}}</p>
    <p><b>Correo:</b> {{ $args['alumno']->email }}</p>
    <p><b>Grupo:</b> {{ $args['grupo']->nombre }}</p>
    <p><b>Fecha de inicio:</b> {{ $args['justificacion']->fecha_inicio }}</p>
    <p><b>Fecha de fin:</b> {{ $args['justificacion']->fecha_fin}}</p>
    <p><b>Motivo:</b> {{ $args['justificacion']->motivo }}</p>

</body>
</html>
