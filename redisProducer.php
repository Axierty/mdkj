<?php 
	
	include('./baseClass.php');

	define('REDIS_KEY', 'queue_2018');


	$base = new Base();

	$data = $base->getRandomUser(100);


	$redis = new Redis();
	$redis->connect('118.25.103.12',6379);
	$redis->auth('mima');

	$redisLength = $redis->lLen( REDIS_KEY );

	if( !$redisLength ){
		foreach( $data as $v){

			$redis->rpush( REDIS_KEY, json_encode($v));
		}
		die("生产者已经装载完毕，刷新查看队列数据");
	}else{
		echo "当前队列长度为{$redisLength}";
	}


 ?>