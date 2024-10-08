<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            access_denied('Settings');
        }
    }

    public function index()
    {
        $this->load->view('connect_inter/settings');
    }

    public function upload()
    {
        // Verifica se os arquivos foram enviados corretamente
        if (
            isset($_FILES['crt_file']) && $_FILES['crt_file']['error'] === UPLOAD_ERR_OK &&
            isset($_FILES['key_file']) && $_FILES['key_file']['error'] === UPLOAD_ERR_OK
        ) {

            // Diretório onde os arquivos serão salvos
            $uploadDir = CONNECT_INTER_MODULE_NAME_UPLOADS_FOLDER . 'ssl_files/';

            // Verifica se o diretório existe, se não, cria o diretório
            if (!is_dir($uploadDir)) {
                _maybe_create_upload_path($uploadDir);
            }

            // Extensões permitidas para cada arquivo
            $allowedExtensions = [
                'crt' => ['crt'],
                'key' => ['key']
            ];


            $hash = app_generate_hash();

            // Processa o arquivo .crt
            $crtUpload = connectInterProcessUpload($_FILES['crt_file'], $allowedExtensions['crt'], $uploadDir, $hash);

            // Processa o arquivo .key
            $keyUpload = connectInterProcessUpload($_FILES['key_file'], $allowedExtensions['key'], $uploadDir, $hash);

            // Verifica o resultado dos uploads
            if ($crtUpload['status'] && $keyUpload['status']) {
                update_option('connect_inter_ssl_file_hash', $hash);
                set_alert('success', 'Ambos os arquivos foram enviados com sucesso!');
            } else {
                set_alert('danger', 'Erro ao enviar os arquivos. Verifique se os arquivos foram enviados corretamente.');
            }
        } else {
            set_alert('danger', 'Ambos os arquivos são obrigatórios. Verifique se os arquivos foram enviados corretamente.');
        }

        redirect(admin_url('connect_inter/v3/settings'));
    }
}
