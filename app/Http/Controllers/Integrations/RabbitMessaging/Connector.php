<?php
namespace App\Http\Controllers\Integrations\RabbitMessaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Config;

class Connector
{
	protected $options;
	protected $connection_params;
	protected $queue_name;
	protected $exchange;
	protected $exchange_type;
	protected $route_name;
	protected $connection;
	
    public function __construct(array $options = null){
		$this->options = $options;
		$this->connection_params = Config::get("queue.connections.rabbitmq");
		$this->setConnectionSettings();
		$this->queue_name = "Gothomis_RnR";
		$this->exchange = "Gothomis_Elmis";
		$this->exchange_type = "direct";
		$this->route_name = "";
	}
	
	public function  setConnectionSettings(){
		if(isset($this->options['host']))
			$this->connection_params['host'] = $this->options['host'];
		
		if(isset($this->options['username']))
			$this->connection_params['username'] = $this->options['username'];
		
		if(isset($this->options['password']))
			$this->connection_params['password'] = $this->options['password'];
		
		
		if(isset($this->options['port']))
			$this->connection_params['port'] = $this->options['port'];
		
		if(isset($this->options['vhost']))
			$this->connection_params['vhost'] = $this->options['vhost'];
	}
	
	public function openConnection(){
		$this->connection = new AMQPStreamConnection($this->connection_params['host'], $this->connection_params['port'], $this->connection_params['username'], $this->connection_params['password'],$this->connection_params['vhost']);
		
		return $this->buildConnection($this->connection->channel());
	}
	
	public function closeConnection($channel){
		$channel->close();
		$this->connection->close();
	}
	
	public function buildConnection($channel){
		try{
			$channel->queue_declare($this->queue_name, false, true, false, false);
			$channel->exchange_declare($this->exchange, $this->exchange_type,false, false, false);
			$channel->queue_bind($this->queue_name, $this->exchange);
			return $channel;
		}catch(Exception $e){
			throw new Exception($e);
		}
	}
}