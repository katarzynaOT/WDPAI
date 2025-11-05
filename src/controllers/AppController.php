<?php
// W kontrolerach bedzie wszytsko (powiazanie z baza itd.)

class AppController {

    protected function isGet(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'GET';
    }

    protected function isPost(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'POST';
    }
 

    protected function render(string $template = null, array $variables = []) #template=filename
    {
        $templatePath = 'public/views/'. $template.'.html';
        $templatePath404 = 'public/views/404.html';
        $output = "";
                 
        if(file_exists($templatePath)){
            // ["message" => "Błędne hasło!"]
            extract($variables); // wyodrebnienie zmiennych z tablicy asocjacyjnej -> bedzie na kolokwium
            // $message = "Błędne hasło!"
            // echo $message

            
            ob_start();
            include $templatePath;  // załączenie pliku html (za pomocą streama)
            $output = ob_get_clean();
        } else {
            ob_start();
            include $templatePath404;
            $output = ob_get_clean();
        }
        echo $output;
    }

}