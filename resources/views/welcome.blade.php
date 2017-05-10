<!DOCTYPE html>
<html>
    <head>
        <title>Schools API</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .logo {
                background-image: url("images/schools.png")
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .content img {
                max-width: 250px;
                height: auto;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                margin-bottom: 20px;
            }

            .container a {
                font-size: 30px;
            }

            .title {
                font-size: 96px;
            }

            .description {
                font-size: 42px;
            }

            .version {
                font-size: 22px; 
                padding: 20px; 
            }

        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <img src="images/schools.png">
                <div class="title">Schools API</div>
                <div class="description">Esta API permite gerir dados administrativas organização educacional podendo ser privada ou publica. São dados administrativos tais como matriculas, registros de frequencias, notas, cargos e funções dos professores, entre outros.</div >
            </div>
            <a class="doc-link" href="/apidocs">Veja aqui a documentação da api.</a>
        </div>
    </body>
</html>
