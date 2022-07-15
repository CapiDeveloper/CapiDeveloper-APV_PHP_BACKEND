<?php 
    // Cabeceras para otros dominios puedan hacer peticiones
// Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    include_once __DIR__.'/../Includes/app.php';

    use MVC\Router;
    use Controller\VeterinarioController;
    use Controller\PacienteController;

    $router = new Router;

    // **               RUTAS DE VETERINARIO              **
    // Rutas no protegidas
    $router->post('/api/veterinarios',[VeterinarioController::class,'registrar']);
    $router->get('/api/veterinarios/confirmar',[VeterinarioController::class,'confirmar']);
    $router->post('/api/veterinarios/login',[VeterinarioController::class,'autenticar']);

    $router->post('/api/veterinarios/olvide-password',[VeterinarioController::class,'olvidePassword']);
    $router->post('/api/veterinarios/nuevo-password',[VeterinarioController::class,'nuevoPassword']);
    
    $router->get('/api/veterinarios/establecer-password',[VeterinarioController::class,'comprobarToken']);
    $router->post('/api/veterinarios/nuevo-password',[VeterinarioController::class,'nuevoPassword']);

    // Ruta protegida con JWT
    $router->get('/api/veterinarios/perfil',[VeterinarioController::class,'perfil']);
    $router->post('/api/veterinarios/actualizar-perfil',[VeterinarioController::class,'actualizarPerfil']);
    $router->post('/api/veterinarios/actualizar-password',[VeterinarioController::class,'actualizarPassword']);

    // **               RUTAS DE PACIENTE              **
    $router->post('/api/pacientes/agregar',[PacienteController::class,'agregarPaciente']);
    $router->get('/api/pacientes/listar',[PacienteController::class,'obtenerPacientes']);
    $router->post('/api/pacientes/eliminar',[PacienteController::class,'eliminarPacientes']);

    $router->comprobarRutas();
?>