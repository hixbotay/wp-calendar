<?php

class FvnAdminView{
	/**
	 * The name of the view
	 *
	 * @var    array
	 */
	protected $_name = null;
	

	/**
	 * The base path of the view
	 *
	 * @var    string
	 */
	protected $_basePath = null;


	/**
	 * Layout name
	 *
	 * @var    string
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var    string
	 */
	protected $_layoutExt = 'php';


	/**
	 * The set of search directories for resources (templates)
	 *
	 * @var array
	 */
	protected $_path;

	/**
	 * The name of the default template source file.
	 *
	 * @var string
	 */
	protected $_template = null;

	/**
	 * The output of the template script.
	 *
	 * @var string
	 */
	protected $_output = null;

	/**
	 * Callback for escaping.
	 *
	 * @var string
	 * @deprecated 13.3
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 */
	protected $_charset = 'UTF-8';
	public $_defaultModel;
	/**
	 * Input filter
	 * @var unknown
	 */
	public $input;
	
	public function __construct($config = array())
	{
		$this->input = HBFactory::getInput();
		// Set the view name
		if (empty($this->_name))
		{
			if (array_key_exists('name', $config))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$this->_name = $this->getName();
			}
			
		}
		
		
		if(isset($_REQUEST['layout'])){
			$this->_layout = $_REQUEST['layout'] ? $_REQUEST['layout'] : 'default';
		}


		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = FVN_PATH;
		}

		$this->_path = $this->_basePath . 'includes/admin/' .$this->_name . '/tmpl';
// 		debug($this->_name);
		if (FvnImporter::find($this->_basePath. 'includes/admin/' .$this->_name, 'model')){
			
			FvnImporter::includes('admin/'.$this->_name.'/model');
			$class_model = "FvnModel{$this->_name}";
			$this->_defaultModel = $class_model;
			$this->_models[$class_model] = new $class_model();
// 			$this->model = $this->getModel();
			$this->model = $this->getModel();
		}
		

	}
	
	public function display($tpl = null)
	{
		$result = $this->loadTemplate($tpl);

		if ($result instanceof Exception)
		{
			return $result;
		}

		echo $result;
	}

	public function getState($key,$default=null){
		return $this->model->getState($key,$default);
	}
	
	public function getName()
	{
		if (empty($this->_name))
		{
			$classname = get_class($this);
			$viewpos = strpos($classname, 'View');

			if ($viewpos === false)
			{
				throw new Exception('view not found', 500);
			}

			$this->_name = strtolower(substr($classname, $viewpos + 4));
		}

		return $this->_name;
	}
	
	public function setLayout($layout)
	{
		$previous = $this->_layout;

		if (strpos($layout, ':') === false)
		{
			$this->_layout = $layout;
		}
		else
		{
			// Convert parameter to array based on :
			$temp = explode(':', $layout);
			$this->_layout = $temp[1];

		}

		return $previous;
	}
	
	public function getLayout()
	{
		return $this->_layout;
	}
		
	public function loadTemplate($tpl = null)
	{
		
		// Clear prior output
		$this->_output = null;
		
		$layout = $this->getLayout();
		

		// Create the template file name based on the layout
		$file = $layout;

		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;		
		
		$filetofind = $tpl ? $tpl : $layout;
		
		
		
		$this->_template = FvnImporter::find($this->_path, $filetofind);
// 		debug($this->_template) ;
		if ($this->_template != false)
		{
			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else
		{
			echo ('Layout file not found '.$this->_path.'/'.$file);
			return;
		}
	}
	
	/**
	 * Method to get data from a registered model or a property of the view
	 *
	 * @param   string  $property  The name of the method to call on the model or the property to get
	 * @param   string  $default   The name of the model to reference or the default value [optional]
	 *
	 * @return  mixed  The return value of the method
	 *
	 * @since   12.2
	 */
	public function get($property, $default = null)
	{
		// If $model is null we use the default model
		if (is_null($default))
		{
			$model = $this->_defaultModel;
		}
		else
		{
			$model = strtolower($default);
		}
		// First check to make sure the model requested exists
		if (isset($this->_models[$model]))
		{
			// Model exists, let's build the method name
			$method = 'get' . ucfirst($property);
			// Does the method exist?
			if (method_exists($this->_models[$model], $method))
			{
				// The method exists, let's call it and return what we get
				$result = $this->_models[$model]->$method();
				return $result;
			}
		}


		return $result;
	}

	/**
	 * Method to get the model object
	 *
	 * @param   string  $name  The name of the model (optional)
	 *
	 * @return  mixed  JModelLegacy object
	 *
	 * @since   12.2
	 */
	public function getModel($name = null)
	{
		if ($name === null)
		{
			$name = $this->_defaultModel;
		}

		return $this->_models[($name)];
	}
	
	/**
	 * Method to add a model to the view.  We support a multiple model single
	 * view system by which models are referenced by classname.  A caveat to the
	 * classname referencing is that any classname prepended by JModel will be
	 * referenced by the name without JModel, eg. JModelCategory is just
	 * Category.
	 *
	 * @param   JModelLegacy  $model    The model to add to the view.
	 * @param   boolean       $default  Is this the default model?
	 *
	 * @return  object   The added model.
	 *
	 * @since   12.2
	 */
	public function setModel($model, $default = false)
	{
		$name = strtolower($model->getName());
		$this->_models[$name] = $model;

		if ($default)
		{
			$this->_defaultModel = $name;
		}

		return $model;
	}
	
}