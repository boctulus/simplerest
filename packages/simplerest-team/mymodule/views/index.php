<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MyModule' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .message {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f7f7f7;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .info-box ul {
            list-style: none;
            padding-left: 0;
        }
        .info-box li {
            padding: 5px 0;
            color: #666;
        }
        .info-box li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 5px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            transition: transform 0.2s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-secondary {
            background: #764ba2;
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= $title ?? 'MyModule' ?></h1>
        <p class="message"><?= $message ?? 'Módulo de prueba de SimpleREST' ?></p>

        <div class="info-box">
            <h3>Características del Módulo</h3>
            <ul>
                <li>Controladores organizados</li>
                <li>Sistema de rutas</li>
                <li>Vistas con plantillas</li>
                <li>Migraciones de base de datos</li>
                <li>Modelos para datos</li>
            </ul>
        </div>

        <div class="info-box">
            <h3>Rutas Disponibles</h3>
            <ul>
                <li><a href="/mymodule" style="color: #667eea;">/mymodule</a> - Página principal</li>
                <li><a href="/mymodule/about" style="color: #667eea;">/mymodule/about</a> - Información</li>
                <li><a href="/api/mymodule/data" style="color: #667eea;">/api/mymodule/data</a> - API de datos</li>
                <li><a href="/api/mymodule/about" style="color: #667eea;">/api/mymodule/about</a> - API info</li>
            </ul>
        </div>

        <div class="btn-group">
            <a href="/mymodule/about" class="btn btn-primary">Ver Información</a>
            <a href="/api/mymodule/data" class="btn btn-secondary">API de Datos</a>
        </div>

        <div class="footer">
            Módulo creado con SimpleREST Framework
        </div>
    </div>
</body>
</html>
