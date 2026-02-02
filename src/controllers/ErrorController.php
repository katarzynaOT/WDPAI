<?php

class ErrorController extends AppController
{
    public function show405(): void
    {
        http_response_code(405);
        $this->render('errors/405');
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', [
            'page' => 'error'
        ]);
    }

    public function internalServerError(): void
    {
        http_response_code(500);
        $this->render('errors/500', [
            'page' => 'error'
        ]);
    }
}