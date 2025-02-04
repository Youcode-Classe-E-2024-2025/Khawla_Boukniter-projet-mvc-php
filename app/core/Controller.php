<?php

namespace App\Core;

abstract class Controller
{
    protected $db;
    protected $view;
    protected $logger;
    protected $security;
    protected $session;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->view = new View();
        $this->logger = new Logger();
        $this->security = new Security();
        $this->session = new Session();
    }

    protected function render(string $template, array $data = [])
    {
        $data['session'] = new Session();
        $this->logger->debug("Rendering template: {$template}");
        return $this->view->render($template, $data);
    }
}
