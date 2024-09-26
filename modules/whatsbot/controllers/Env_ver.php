<?php
defined('BASEPATH') || exit('No direct script access allowed');

class Env_ver extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        show_404();
    }

    // Removendo a necessidade de validação do módulo e chave de compra
    public function activate()
    {
        // Simula uma resposta positiva sem verificar a chave
        $res = [
            'status' => true,
            'original_url' => $this->input->post('original_url'),
            'message' => 'Module activated successfully'
        ];
        
        echo json_encode($res);
    }

    // Removendo a necessidade de validação do módulo para upgrade do banco de dados
    public function upgrade_database()
    {
        // Simula uma resposta positiva sem verificar a chave
        $res = [
            'status' => true,
            'original_url' => $this->input->post('original_url'),
            'message' => 'Database upgrade successful'
        ];

        echo json_encode($res);
    }
}
