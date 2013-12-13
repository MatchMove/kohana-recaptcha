<?php defined('SYSPATH') or die('No direct script access.');

require_once RECAPTCHA_MOD_PATH . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'recaptcha'
    . DIRECTORY_SEPARATOR . 'recaptchalib.php';

class Kohana_Recaptcha {
    
    const UNIT_COUNT = 'count';
    const UNIT_TIME = 'time';
    
    protected $_config;
    protected $_error = NULL;
    
    protected static $_instance = null;
    
    public static function instance()
    {
        if (!empty(self::$_instance))
        {
            return self::$_instance;
        }
        
        return self::$_instance = new Recaptcha();
    }
    
    public function __construct($config = NULL)
    {
        $this->_config= Kohana::$config->load('recaptcha')->as_array();
        
        if (is_array($config))
        {
            $this->_config = Arr::overwrite($this->_config, $config);
        }
        return $this;
    }
    
    public function check($answers, $fields=array('recaptcha_challenge_field', 'recaptcha_response_field'))
    {
        if (empty($answers[$fields[0]]) || empty($answers[$fields[1]]))
        {
            return false;
        }
        
        $recaptcha_resp = recaptcha_check_answer(
            $this->_config['private_key'],
            Request::$client_ip,
            $answers[$fields[0]],
            $answers[$fields[1]]
        );

        $this->_error=$recaptcha_resp->error;
        return $recaptcha_resp->is_valid;
    }
    
    public function error()
    {
        return $this->_error;
    }
    
    public function html()
    {
        return recaptcha_get_html($this->_config['public_key'], $this->_error, Arr::get($this->_config, 'https', true));
    }
}
