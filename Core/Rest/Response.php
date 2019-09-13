<?php
namespace Core\Rest;

class Response {

    public function __construct()
    {
        
        header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
        header('Content-Type: application/json');
    }

    /**
     * @var string HTTP response formats
     */
    const FORMAT_RAW = 'raw';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML = 'xml';

    /**
     * @var array the formatters that are supported by default
     */
    public $contentTypes = [
        self::FORMAT_RAW => 'text/plain;',
        self::FORMAT_HTML => 'text/html;',
        self::FORMAT_JSON => 'application/json;', // RFC 4627
        self::FORMAT_JSONP => 'application/javascript;', // RFC 4329
        self::FORMAT_XML => 'application/xml;', // RFC 2376
    ];

    private $_format = self::FORMAT_JSON;

    /**
     * @var int the HTTP status code to send with the response.
     */
    private $_statusCode = 200;

    public function getHeader($header = null){
        $headers = apache_request_headers();
        if($header){
            if(isset($headers[$header]))
                return $headers[$header];
            else 
                return null;
        } 
        return $headers;

    }

    public function json($data, $statusCode = null){
        if($statusCode)
            http_response_code($statusCode);

        echo json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}