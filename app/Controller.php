<?php

class Controller
{
    protected function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }

    protected function render(string $template, array $data = []): Response
    {
        // make path to the template file
        $path = __DIR__ . "/../templates/$template.php";

        // checking if the file exists
        if (!file_exists($path)) {
            throw new InvalidArgumentException("Template file not found: $template.");
        }

        // checking if the file is readable
        if (!is_readable($path)) {
            throw new InvalidArgumentException("Template file not readable: $template");
        }

        // start buffer
        ob_start();

        // declare all the variables
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        // load the template file
        require_once $path;

        // get the response
        $str = ob_get_contents();

        // cleanup
        ob_end_clean();

        // set response back to browser
        return new Response(200, $str);
    }
}
