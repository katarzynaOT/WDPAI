<?php

//echo "<h1>hi, hello</h1>";

require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Routing::run($path);

//var_dump($path);

/*echo "
    <style>
        h1 { color: blue; }
//     </style>
// ";*/

// switch ($path) {
//     case 'dashboard':
//         //echo "<h1 id='title'>dashboard</h1>";
//         include 'public/views/dashboard.html';
//         break;
//     case 'login':
//         //echo "<h1 id='title'>login</h1>"; //wersja 1
//         /*echo "<style> 
//             h1 { color: red;}
//             </style>";*/
//         include 'public/views/login.html'; //wersja 2
//         break;
//     default:
//         //echo "<h1 id='title'>404</h1>";
//         include 'public/views/404.html';
//         break;
// }

// echo "
//     <script>
//     const header = document.querySelector('#title');
//     console.log(header);
//     header.addEventListener('click', () => {
//         header.style.color = 'green';
//     })
//     </script>
// ";