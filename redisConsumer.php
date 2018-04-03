<?php 

	include('./baseClass.php');

	define('REDIS_KEY', 'queue_2018');

	$redis = new Redis();
	$redis->connect('118.25.103.12',6379);
	$redis->auth('mima');

	$redisLength = $redis->lLen( REDIS_KEY );

	if( $redisLength ){
		echo "消费者开始消费了<br>";

		// 右进左出
		$info = $redis->lpop( REDIS_KEY );
		if( $info ){
			$info = json_decode($info,true);
			dump($info);
		}
	}else{
		//添加阻塞响应时间 为 10秒
		$info = $redis->blPop( REDIS_KEY,REDIS_KEY,10 );
		if( $info && is_array($info) && isset($info[1]) ){
			
			echo "等待10秒内消费者开始消费了<br>";

			$info = json_decode($info[1],true);
			dump($info);

		}else{
			echo "阻塞超时，已停止消费";
		}
		
	}



 ?>