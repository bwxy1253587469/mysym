<?php
namespace Symfony\Component\HttpFoundation;

class Request
{
    /**
     * Custom parameters.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $attributes;

    /**
     * Request body parameters ($_POST).
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $request;

    /**
     * Query string parameters ($_GET).
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     * @var \Symfony\Component\HttpFoundation\ServerBag
     */
    public $server;

    /**
     * Uploaded files ($_FILES).
     *
     * @var \Symfony\Component\HttpFoundation\FileBag
     */
    public $files;

    /**
     * Cookies ($_COOKIE).
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    public $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $languages;

    /**
     * @var array
     */
    protected $charsets;

    /**
     * @var array
     */
    protected $encodings;

    /**
     * @var array
     */
    protected $acceptableContentTypes;

    /**
     * @var string
     */
    protected $pathInfo;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $defaultLocale = 'en';

    /**
     * @var array
     */
    protected static $formats;

    protected static $requestFactory;

    /**
     * Constructor.
     *
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->request = new ParameterBag($request);
        $this->query = new ParameterBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new ParameterBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }

    public static function createFromGlobals()
    {
        return new Request($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
    }
    /**
     * 得到键值参数对应的值
     * @param  [string] $key     [键值]
     * @param  [mixed]  $default [默认值]
     * @param  [bool]  $deep [是否在多维数组中]
     * @return [mixed]          [description]
     */
    public function get($key, $default = null, $deep = false)
    {
        if ($this !== $result = $this->query->get($key, $this, $deep)) {
            return $result;
        }

        if ($this !== $result = $this->attributes->get($key, $this, $deep)) {
            return $result;
        }

        if ($this !== $result = $this->request->get($key, $this, $deep)) {
            return $result;
        }

        return $default;
    }
    /**
     * 得到路径
     * url                                            return
     * http://www.bbb.com/index.php/hello?name=123    hello
     * http://www.bbb.com/index.php/hello/jj?name=123 hello/jj
     * @return [type] [description]
     */
    public function getPathInfo()
    {
        if ($this->pathInfo === null) {
            $this->setPathInfo();
        }
        return $this->pathInfo;
    }
    /**
     * 设置路径
     * url                                            return
     * http://www.bbb.com/index.php/hello?name=123    hello
     * http://www.bbb.com/index.php/hello/jj?name=123 hello/jj
     * @return [type] [description]
     */
    public function setPathInfo()
    {
        $this->pathInfo = str_replace($this->server->get('SCRIPT_NAME'), '', $this->server->get('PHP_SELF'));
        return $this;
    }
    /**
     * 得到路径
     * url                                            return
     * http://www.bbb.com/index.php/hello?name=123    index.php
     * http://www.bbb.com/index.php/hello/jj?name=123 index.php
     * @return [type] [description]
     */
    public function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $this->setBaseUrl();
        }
        return $this->baseUrl;
    }
    /**
     * 得到路径
     * url                                            return
     * http://www.bbb.com/index.php/hello?name=123    index.php
     * http://www.bbb.com/index.php/hello/jj?name=123 index.php
     * @return [type] [description]
     */
    public function setBaseUrl()
    {
        $this->baseUrl = $this->server->get('SCRIPT_NAME');
        return $this;
    }
    /**
     * 得到http请求方法
     * @return [type] [description]
     */
    public function getMethod()
    {
        if ($this->method === null) {
            $this->setMethod();
        }
        return $this->method;
    }
    /**
     * 设置http请求方法
     * @return [type] [description]
     */
    public function setMethod()
    {
        $this->method = $this->server->get('REQUEST_METHOD');
        return $this;
    }
    /**
     * 得到http请求域名
     * @return [type] [description]
     */
    public function getHost()
    {
        return $this->headers->get('host');
    }
    /**
     * 得到http协议 http,https
     * @return [type] [description]
     */
    public function getScheme()
    {
        return $this->server->get('REQUEST_SCHEME');
    }
}
