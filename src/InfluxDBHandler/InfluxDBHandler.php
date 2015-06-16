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
    private $initialised;

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
    public function __construct($username, $password, $endpoint, $db, $async = false, $level = Logger::DEBUG, $bubble = true)
    {
        $this->username = $username;
        $this->password = $password;
        $this->endpoint = $endpoint;
        $this->db = $db;
        $this->async = $async;
        $this->url = rtrim($endpoint, "/")."/db/".$db."/series"; //TODO: Form URL from endpoint and db.
        $this->authUrl = $this->url."?u=".$username."&p=".$password;
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


        $columns = ['message'];
        $columns = array_merge($columns, array_keys($record['context']));
        $points = [$record['message']];
        $points = array_merge($points, array_values($record['context']));

        $guzzle = new Client();
        $log_object = [[
            'name' => $record['channel'],
            'columns' => $columns,
            'points' => [ $points ]
        ]];

        if(!$this->async) {
            $response = $guzzle->post($this->url, [
                'query' => [
                    'u' => $this->username,
                    'p' => $this->password
                ],
                'body' => json_encode($log_object)
            ]);
        }

    }


}

