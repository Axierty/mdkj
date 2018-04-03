<?php 

	if( !version_compare(PHP_VERSION,'7.0.0','ge') ) die('当前php版本太低');

	//加载通用方法
	include('./baseClass.php');

	//数据库名称
	define('MONGODB_DATABASE','test');
	//数据库名 + 集合名
	define('MONGODB_CNAME', 'test.mdkj');

	$base = new Base();
	$data = $base->getRandomUser(100);


	//获取年份数组
	$dateYear = array_column($data,'birth');
	foreach( $dateYear as $v){
		$year[] = date('Y', strtotime($v) );
	}
	$yearArray = array_unique($year); 
	$yearArray = $base->getDateArea($yearArray,5);  

	//php7 扩展的api
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");


	//数据查询
    $query = new MongoDB\Driver\Query([]);
	$cursor = $manager->executeQuery( MONGODB_CNAME, $query);

	$dbData = [];

	//保存句柄下面的数据
	foreach ($cursor as $document) {
	    $dbData[] = $document;
	}

	if( empty($dbData) ){

		//数据插入
	    $bulk = new MongoDB\Driver\BulkWrite();

	    foreach( $data as $v ){
	    	$bulk->insert($v);
	    }

		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $manager->executeBulkWrite( MONGODB_CNAME, $bulk, $writeConcern);

		dump($result);
		die("初始数据写入成功,请重新刷新页面");
		
	}

	$param1 = [
		'aggregate' => 'mdkj',
		'pipeline' => [
			[
				'$group' => ['_id' => '$province','sum' => ['$sum' => 1]]
			]
		],
		'cursor' => new stdClass,
	];

	$cmd = new MongoDB\Driver\Command($param1);

	$res = $manager->executeCommand( MONGODB_DATABASE,$cmd );

	echo "不同省份的用户分组信息<br>";
	dump( $res->toArray() );



	echo "<br>-------------------- 以下是按照年份分组（使用间隔为5年）---------------------------<br><br>";



	echo "不同年龄的用户分组信息<br>";

	foreach( $yearArray as $y){
		if( is_array($y) && count($y) == 2 ){
			$startDate = $y[1] . "-01-01";
			$endDate = $y[0] . "-12-12";
		}else{
			$startDate = $y . "-01-01";
			$endDate = $y . "-12-12";
		}


		$filter = ['birth' => ['$lte' => $endDate , '$gte' => $startDate ]];
		$options = [
		   'projection' => ['_id' => 0]
		];


	    $query = new MongoDB\Driver\Query( $filter ,$options );
		$cursor = $manager->executeQuery(MONGODB_CNAME, $query);

		$res = [];
		foreach ($cursor as $document) {
		    $res[] = $document;
		}
		echo "{$y[1]} 至 {$y[0]} 年的用户分组信息<br> 包含个数为 ".count($res)."<br><br>";
		dump($res);

	}




 ?>