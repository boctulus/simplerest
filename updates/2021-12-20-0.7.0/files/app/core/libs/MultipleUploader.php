<?php declare(strict_types=1);

namespace simplerest\core\libs;





use simplerest\core\libs\Arrays;
use simplerest\core\libs\Files;
use simplerest\libs\Debug;

class MultipleUploader
{
	protected $filenames  = [];
	protected $settings	= [];
	protected $location = UPLOADS_PATH;
	protected $erroneous = [];
	protected $renamerFn = null;
	protected const WILDCARD = '*';
	
	
	public function __construct(){
	}	
	
	// @param string path (sin / al final)
	public function setLocation($path){
		$this->location = $path;
		return $this;
	}	

	
	public function setFileHandler($fn, ...$params){
		$this->renamer = [$fn, $params];
		return $this;
	}
	
	
	public function getFileNames(){
		return $this->filenames;
	}
	
	
	public function getErrors(){
		return $this->erroneous;
	}
		
		
	public function doUpload($input_name = NULL)
	{		
		if(empty($_FILES))
			return $this;
					
		$renamer = $this->renamer[0];
		$subfijo = $this->renamer[1][0];	

		// reset	
		$this->filenames  = [];	
		$this->erroneous = [];
			
		Files::mkDirOrFail($this->location);
		Files::writableOrFail($this->location);
		
		$key_0 = Arrays::array_key_first($_FILES);
		$file0 = $_FILES[$key_0]; 
		$name = $input_name != NULL ? $input_name : $key_0;


		if(is_array($file0['error']) && isset($_FILES[$name]['error']) && is_array($_FILES[$name]['error'])){
			$i = 0; 
			foreach($_FILES[$name]['error'] as $key => $error)
			{			
				if ($error == UPLOAD_ERR_OK)
				{
					
					
					$tmp_name = $_FILES[$name]["tmp_name"][$key];
					$filename = basename($_FILES[$name]["name"][$key]); 
					$new_filename = $renamer($subfijo) . '.' . pathinfo($_FILES[$name]["name"][$key], PATHINFO_EXTENSION);
					$this->filenames[$i] = [ $filename, $new_filename ];
					move_uploaded_file($tmp_name, $this->location. DIRECTORY_SEPARATOR . $new_filename);
					$i++;				
				}else
					$this->erroneous[] = $_FILES[$name]['name'][$key];
			}
		
		}else{
			
			if($input_name != NULL && isset($_FILES[$input_name]['error'])){
				if ($_FILES[$input_name]['error'] == UPLOAD_ERR_OK)
				{
					$tmp_name = $_FILES[$input_name]['tmp_name'];
					$filename =  basename($_FILES[$input_name]['name']);
					$new_filename = $renamer($subfijo) . '.' . pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
					$this->filenames[] = [ $filename, $new_filename ];
					move_uploaded_file($tmp_name, $this->location. DIRECTORY_SEPARATOR. $new_filename);		
				}else
					$this->erroneous[] = $_FILES[$input_name]['name'];
			}
			else
				if($input_name == NULL){
					foreach($_FILES as $_name => $file){
						if ($file['error'] == UPLOAD_ERR_OK)
						{
							$tmp_name = $file['tmp_name'];
							$filename =  basename($file['name']);
							$new_filename = $renamer($subfijo) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
							$this->filenames[] = [ $filename, $new_filename ];
							move_uploaded_file($tmp_name, $this->location. DIRECTORY_SEPARATOR. $new_filename);		
						}else
							$this->erroneous[] = $file['name'];
					}
				}else if ($input_name[strlen($input_name)-1] == self::WILDCARD){
					$starts_with = substr($input_name, 0, strlen($input_name)-1);
					
					foreach($_FILES as $_name => $file){
						if(substr($_name, 0, strlen($_name)-1) != $starts_with)
							continue;
						
						if ($file['error'] == UPLOAD_ERR_OK)
						{
							$tmp_name = $file['tmp_name'];
							$filename =  basename($file['name']);
							$new_filename = $renamer($subfijo) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
							$this->filenames[] = [ $filename, $new_filename ];
							move_uploaded_file($tmp_name, $this->location. DIRECTORY_SEPARATOR. $new_filename);		
						}else
							$this->erroneous[] = $file['name'];
					}
				}
		
		}
		
		return $this;
    }	

	
	
}	