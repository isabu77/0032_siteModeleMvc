<?php
// page 404
$page = $_SERVER["REQUEST_URI"];
echo "
    <html>
    <head>
        <title>Page d'erreur HTTP <?=code?></title>
    </head>
    <body>
        <div style='text-align:center;'><h1>- 404 -</h1></div>
        <div style='text-align:center;'>Vous venez de rencontrer une erreur HTTP pour la page " . $page . "</div>
    </body>
    </html>
";