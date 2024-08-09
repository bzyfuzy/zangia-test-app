<?php

namespace App\Views;

class ViewEngine
{
    protected $variables = [];
    protected $viewsDir = __DIR__;

    public function __construct()
    {
    }

    public function assign($key, $value)
    {
        $this->variables[$key] = $value;
    }

    public function render($view)
    {
        $filePath = $this->viewsDir . '/' . $view . '.view.php';

        if (file_exists($filePath)) {
            extract($this->variables);
            include($filePath);
            $content = ob_get_clean();
            return $content;
        }
        return "View not found!";
    }
}
