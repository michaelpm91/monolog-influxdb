<?php namespace InfluxDBHandler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use GuzzleHttp\Client;

/**
 * Class InfluxDBHandler
 * @package kidshenlong\InfluxDBHandler
 */
class InfluxDBHandler extends AbstractProcessingHandler
{

    /**
     * @var
     */
    private $intialised;

    /**
     * @var int
     */
    private $username;

    /**
     * @var bool
     */
    private $password;

    /**
     * @var
     */
    private $endpoint;

    /**
     * @var
     */
    private $db;

    /**
     * @var string
     */
    private $url;


    /**
     * @param int $level
     * @param bool $bubble
     */
    public function __construct($username, $password, $endpoint, $db, $level = Logger::DEBUG, $bubble = true)
    {
        $this->username = $username;
        $this->password = $password;
        $this->endpoint = $endpoint;
        $this->db = $db;
        $this->url = ""; //TODO: Form URL from endpoint and db.
        parent::__construct($level, $bubble);
    }

    /**
     *
     */
    private function initialise() {
        return true; //TODO: Write this method.
    }

    /**
     * @param array $record
     */
    protected function write(array $record)
    {

        if (!$this->initialised) {
            $this->initialise();
        }

        $guzzle = new Guzzle();
        $log_object = [[
            'name' => 'sys_log',
            'columns' => [ //TODO: Columns should a param not hardcoded
                "channel",
                "level",
                "formatted"
            ],
            'points' => [[ //TODO: Points should a param not hardcoded
                $record['channel'],
                $record['level'],
                $record['formatted']
            ]]
        ]];
        $response = $guzzle->post($this->url, [
            'query' => [
                'u' => $this->username,
                'p' => $this->password
            ],
            'body' => json_encode($log_object)
        ]);


    }

}

