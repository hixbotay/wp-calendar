<?php
//use HB\AImporter;
class FvnAction{
	
	//protected $methods;
	//task is method to execute
	protected $task;
	//list of public methods in the class that can execute
	protected $taskMap;
	//name of the controller, it defines param in url and where is the folder of the controller file
	public $name;
	//input of request
	public $input;
	public $model;
	//duong dan den file cua thu muc dang lam viec
	public $path;
	public $_redirect;
	
	public function __construct(){
		$this->taskMap = array();
		$this->name = $this->getName();
		//$this->methods = array();
		$r = new ReflectionClass($this);
		$rMethods = $r->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($rMethods as $rMethod)
		{
			$mName = $rMethod->getName();
			// Add default display method if not explicitly declared.
			$this->taskMap[strtolower($mName)] = $mName;
		}
		$this->input = HBFactory::getInput();
		$this->_redirect = admin_url('admin.php?page='.$this->getName());
// 		$this->model = $this->getModel('#__'.$this->getName(),'id');
	}
	
	/**
	 * Execute a function in the controller
	 * Enter description here ...
	 * @param $task
	 */
	public function execute($task)
	{
		$this->task = $task;

		$task = strtolower($task);

		if (isset($this->taskMap[$task]))
		{
			$doTask = $this->taskMap[$task];
		}
		else
		{
			throw new Exception(sprintf(__('Task not found "%s" in class %s'), $task, get_class($this)), 404);
		}

		// Record the actual task being fired
		$this->doTask = $doTask;

		$this->$doTask();
		wp_safe_redirect($this->_redirect);
		return;
	}

	public function setRedirect($url){
		$this->_redirect = $url;
	}
	
	public function display($file){
		$file = $file.'.php';
		$find[] = HB_Template_Loader::getRoot().$file;
		$template       = locate_template( array_unique( $find ) );		
		include $template;
	}
	
	function save_and_close(){
		return $this->save();
	}
	//hook before save to get data
	public function getInputData(){
		$post = $this->input->getPost();
		$data = $post['data'];
		$this->loadModel();
		// 		die('34');
		
		
		$primary_key = $this->model->get_primary_key();
		foreach($primary_key as $key){
			if($this->input->get($key)){
				$data[$key] = $this->input->get($key);
			}
		}
		return $data;
	}

	//hook after save
	public function hookAfterSave($data){
		return true;
	}
	
	public function save(){
		//check captcha
// 		global
		global $wpdb;
		$post = $this->input->getPost();
		$this->loadModel();
		$primary_key = $this->model->get_primary_key();
		$data = $this->getInputData();
// 		debug($data);die;
		$result = $this->model->save($data);
		if($result){
			$result = $this->hookAfterSave($data);
		}
// 		debug($this->model);die;
// 		debug($result);die;
		$first_key = reset($primary_key);
		
	
		if($result){
			hb_enqueue_message(_('Save item success'));
			$_SESSION[$this->name]['data'] = false;
		}
		else{
			//neu that bai luu du lieu nay lai de user khong phai nhap lai du lieu nay
			$_SESSION[$this->name]['data'] = (object)$data;
			//debug($wpdb);die;
			hb_enqueue_message($wpdb->last_error,'error');
		}
		$url_key = [];
		foreach($primary_key as $key){
			$url_key[] = "{$key}={$this->model->$key}"; 
		}
		if($this->get_task()=='save_and_close'){
			wp_safe_redirect(admin_url('admin.php?page='.$this->getName()));
		}else{
			wp_safe_redirect(admin_url('admin.php?page='.$this->getName().'&layout=edit&'.implode('&', $url_key)));
		}
		exit;
	}
	
	private function get_task(){
		return $this->input->get('task');
	}
	


	function delete(){
		$this->model = $this->getModel();
		$primary_key = $this->model->get_primary_key();
// 		debug($primary_key);
		foreach($primary_key as $key){
			if($this->input->get($key)){
				$data[$key] = $this->input->get($key);
			}
		}
// 		debug($data);die;
		if(!$data){
			hb_enqueue_message(_('Please select item'));
			wp_redirect(admin_url('admin.php?page='.$this->getName()));
			return false;
		}
		$this->model->load($data);
		if(!$this->model->delete()){
			hb_enqueue_message(__('Delete failed '.$this->model->getError() ),'error');			
		}else{
			hb_enqueue_message(__('Delete Success' ));
		}
		wp_redirect(admin_url('admin.php?page='.$this->getName()));
		return;
	
	}
	
/**
	 * Method to get the controller name
	 *
	 * The dispatcher name is set by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the dispatcher
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$r = null;
			
			if (!preg_match('/FvnAction(.*)/', get_class($this), $r))
			{
				throw new Exception(sprintf(__('Invalid action name')), 500);
			}
			
			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}
	
/**
	 * Method to get a reference to the current view and load it if necessary.
	 *
	 * @param   string  $name    The view name. Optional, defaults to the controller name.
	 * @param   string  $type    The view type. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for view. Optional.
	 *
	 * @return  JViewLegacy  Reference to the view or an error.
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getView($name = '', $type = '', $prefix = '', $config = array())
	{
		// @note We use self so we only access stuff in this class rather than in all classes.
		if (!isset(self::$views))
		{
			self::$views = array();
		}

		if (empty($name))
		{
			$name = $this->getName();
		}

		if (empty($prefix))
		{
			$prefix = $this->getName() . 'View';
		}

		if (empty(self::$views[$name][$type][$prefix]))
		{
			if ($view = $this->createView($name, $prefix, $type, $config))
			{
				self::$views[$name][$type][$prefix] = & $view;
			}
			else
			{
				$response = 500;
				$app = JFactory::getApplication();

				/*
				 * With URL rewriting enabled on the server, all client
				 * requests for non-existent files are being forwarded to
				 * Joomla.  Return a 404 response here and assume the client
				 * was requesting a non-existent file for which there is no
				 * view type that matches the file's extension (the most
				 * likely scenario).
				 */
				if ($app->get('sef_rewrite'))
				{
					$response = 404;
				}

				throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $name, $type, $prefix), $response);
			}
		}

		return self::$views[$name][$type][$prefix];
	}
	//get path of folder
	private function getPath(){
		$this->_path = FVN_PATH . 'includes/admin/' .$this->name .'/';
		return $this->_path;
	}
	
	private function loadFile($file){
		$this->path = $this->getPath();
		return require_once $this->path.$file.'.php';
	}
	
	function loadModel(){
		if(!$this->model){
			$this->model = $this->getModel();
		}
	}
	
	public function getModel(){
		$this->loadFile('model');
		$model = "FvnModel".$this->getName();
		$this->model = new $model();
		return $this->model;
	}
	
	public function ajax_process_order($result){
		
		echo json_encode($result);
		exit;
	}
	
	public function renderJson($data){
		// Use the correct json mime-type
	    header('Content-Type: application/json');	
	    // Change the suggested filename
	    header('Content-Disposition: attachment;filename="response.json"');
	    header('Access-Control-Allow-Origin: *');
		echo json_encode($data);
		exit;
	}
	
	public function renderError($msg){
		header("HTTP/1.0 404 Not Found");
		echo $msg;
		exit;
	}
	
	
	
}