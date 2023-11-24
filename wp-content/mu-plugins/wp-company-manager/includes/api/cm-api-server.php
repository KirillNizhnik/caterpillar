<?php

class CM_API_Server
{
    protected static $instance;


    /**
     * Requested method (GET/HEAD/POST/PUT/PATCH/DELETE)
     *
     * @var string
     */
    public $method = 'HEAD';
    /**
     * Request parameters
     *
     * This acts as an abstraction of the superglobals
     * (GET => $_GET, POST => $_POST)
     *
     * @var array
     */
    public $params = array( 'GET' => array(), 'POST' => array() );

    /**
     * Initializes variables and sets up WordPress hooks/actions.
     *
     * @return void
     */

    protected function __construct( )
    {
        $this->method         = $_SERVER['REQUEST_METHOD'];
        $this->params['GET']  = $_GET;
        $this->params['POST'] = $_POST;

        $input        = json_decode( file_get_contents( "php://input" ) );
        $request_data = (is_object($input)) ? get_object_vars($input) : $input;

        if( ! empty($request_data) ) {
            $this->params['POST'] = array_merge($this->params['POST'], $request_data);
        }

        add_action( 'init', array(__CLASS__, 'create_api_end_point') );
        add_action( 'template_redirect', array($this, 'route_to_resource'), 1, 9999 );
    }

    /* Static Singleton Factory Method */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public static function create_api_end_point()
    {
        add_rewrite_endpoint('api', EP_ROOT);
    }



    /**
     * Map the api parameter to the proper function
     */

    public function route_to_resource()
    {
        $route    = get_query_var( 'api' );
        $route_ary = explode('/', $route);
        $resource = array_shift( $route_ary );

        if( empty($route) || empty($resource) )
            return;

        if( ! file_exists(__DIR__.'/cm-api-'.$resource.'.php') )
            $this->bad_request();
        
        
        $resource_class = 'CM_API_'.ucwords($resource);
        //var_dump($resource_class); die();
        $resource       = new $resource_class( CM_API_Server::instance() );


        $this->route($route, $resource);
        exit;
    }


    public function route($request, $resource)
    {
        foreach($resource->routes[$this->method] as $route => $method)
        {
            if( preg_match('~^'.$route.'$~', $request, $matches) )
            {
                array_shift($matches);

                status_header( 200 );
                call_user_func_array(array($resource, $method), $matches);
            }
        }

        $this->bad_request();
    }


    public function bad_request()
    {
        status_header( 400 );
        echo json_encode(array('error' => 'Route doesn\'t exist.'));
        exit;
    }


    /**
     * API Not Found Error
     * @param  [string] $method [the method called]
     * @return [null]
     * @output json
     */
    public function not_found($method)
    {
        echo json_encode(array('status' => 'failed','error' => 'Method '. $method .' doesn\'t exist'));
        die;
    }
}


CM_API_Server::instance();
