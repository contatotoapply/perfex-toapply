<?php if(count(get_included_files()) == 1) exit("No direct script access allowed");

define("LB_API_DEBUG", false);
define("LB_SHOW_UPDATE_PROGRESS", true);

define("LB_TEXT_CONNECTION_FAILED", 'Server is unavailable at the moment, please try again.');
define("LB_TEXT_INVALID_RESPONSE", 'Server returned an invalid response, please contact support.');
define("LB_TEXT_VERIFIED_RESPONSE", 'Verified! Thanks for purchasing.');
define("LB_TEXT_PREPARING_MAIN_DOWNLOAD", 'Preparing to download main update...');
define("LB_TEXT_MAIN_UPDATE_SIZE", 'Main Update size:');
define("LB_TEXT_DONT_REFRESH", '(Please do not refresh the page).');
define("LB_TEXT_DOWNLOADING_MAIN", 'Downloading main update...');
define("LB_TEXT_UPDATE_PERIOD_EXPIRED", 'Your update period has ended or your license is invalid, please contact support.');
define("LB_TEXT_UPDATE_PATH_ERROR", 'Folder does not have write permission or the update file path could not be resolved, please contact support.');
define("LB_TEXT_MAIN_UPDATE_DONE", 'Main update files downloaded and extracted.');
define("LB_TEXT_UPDATE_EXTRACTION_ERROR", 'Update zip extraction failed.');
define("LB_TEXT_PREPARING_SQL_DOWNLOAD", 'Preparing to download SQL update...');
define("LB_TEXT_SQL_UPDATE_SIZE", 'SQL Update size:');
define("LB_TEXT_DOWNLOADING_SQL", 'Downloading SQL update...');
define("LB_TEXT_SQL_UPDATE_DONE", 'SQL update files downloaded.');
define("LB_TEXT_UPDATE_WITH_SQL_IMPORT_FAILED", 'Application was successfully updated but automatic SQL importing failed, please import the downloaded SQL file in your database manually.');
define("LB_TEXT_UPDATE_WITH_SQL_IMPORT_DONE", 'Application was successfully updated and SQL file was automatically imported.');
define("LB_TEXT_UPDATE_WITH_SQL_DONE", 'Application was successfully updated, please import the downloaded SQL file in your database manually.');
define("LB_TEXT_UPDATE_WITHOUT_SQL_DONE", 'Application was successfully updated, there were no SQL updates.');

if(!LB_API_DEBUG){
	@ini_set('display_errors', 0);
}

if((@ini_get('max_execution_time')!=='0')&&(@ini_get('max_execution_time'))<600){
	@ini_set('max_execution_time', 600);
}
@ini_set('memory_limit', '256M');

class CommissionLic{

	private $product_id = 'ED66DC54';
	private $api_url;
	private $api_key = '801929B0DF7AFE6F02C6';
	private $api_language = 'english';
	private $current_version = 'v1.0.0';
	private $verify_type = 'envato';
	private $verification_period = 30;
	private $current_path;
	private $root_path;
	private $license_file;
	private $check_interval_file;

	public function __construct(){ 
		$this->current_path = realpath(__DIR__);
		$this->root_path = realpath($this->current_path.'/..');
		$this->license_file = $this->current_path.'/.lic';
		$this->check_interval_file = $this->current_path.'/.licint';
	}

	/**
	 * check local license_exist
	 * @return bool
	 */
	public function check_local_license_exist(){
		return true;  // Always return true as license verification is removed
	}

	/**
	 * get current version
	 * @return string
	 */
	public function get_current_version(){
		return $this->current_version;
	}

	/**
	 * activate license
	 * @param  string  $license
	 * @param  string  $client
	 * @param  string  $create_lic
	 * @return array
	 */
	public function activate_license($license, $client, $create_lic = true){
		return ['status' => true, 'message' => LB_TEXT_VERIFIED_RESPONSE];  // Always return success
	}

	/**
	 * verify license
	 * @param  boolean $time_based_check
	 * @param  boolean $license  
	 * @param  boolean $client   
	 * @return array
	 */
	public function verify_license($time_based_check = false, $license = false, $client = false){
		return ['status' => true, 'message' => LB_TEXT_VERIFIED_RESPONSE];  // Always return success
	}

	/**
	 * deactivate license 
	 * @param  boolean $license 
	 * @param  boolean $client  
	 * @return json
	 */
	public function deactivate_license($license = false, $client = false){
		return ['status' => true, 'message' => 'License deactivated.'];  // Always return success
	}
}
