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

    /**
     * Modificado para remover a necessidade de ativação via chave de compra
     * O método activate agora sempre retorna o módulo como validado, sem checagem de chave.
     */
    public function activate()
    {
        // Código de ativação removido. O módulo será considerado sempre ativo.
        $res = [
            'status' => true,
            'message' => 'Módulo ativado com sucesso.',
            'original_url' => $this->input->post('original_url')
        ];
        echo json_encode($res);
    }

    /**
     * Modificado para remover a necessidade de ativação para upgrade de banco de dados
     * O método upgrade_database agora sempre retorna o sucesso da operação, sem checagem de chave.
     */
    public function upgrade_database()
    {
        // Código de validação removido. A atualização do banco de dados será sempre permitida.
        $res = [
            'status' => true,
            'message' => 'Banco de dados atualizado com sucesso.',
            'original_url' => $this->input->post('original_url')
        ];
        echo json_encode($res);
    }
}
