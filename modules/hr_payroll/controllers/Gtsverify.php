<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
// Remover a inclusão da biblioteca de licença
// require_once __DIR__ .'/../libraries/gtsslib.php';

/**
 * GTSSolution verify
 */
class Gtsverify extends AdminController{
    public function __construct(){
        parent::__construct();
    }

    /**
     * index 
     * @return void
     */
    public function index(){
        show_404();
    }

    /**
     * activate
     * @return json
     */
    public function activate(){
        // Remover a necessidade de informar a chave de compra e nome do cliente
        // $license_code = strip_tags(trim($_POST["purchase_key"]));
        // $client_name = strip_tags(trim($_POST["username"])); 
        // $api = new HRPayrollLic();
        // $activate_response = $api->activate_license($license_code, $client_name);
        
        // Retornar sempre status ativo
        $res = array();
        $res['status'] = true;
        $res['message'] = 'Module activated successfully.';
        $res['original_url'] = $this->input->post('original_url');
        
        echo json_encode($res);
    }    
}
