<?php 

	if( !version_compare(PHP_VERSION,'7.0.0','ge') ) die('当前php版本太低');

	//加载通用方法
	include('./baseClass.php');


	$a = new Base();
	$data = $a->getRandomUser();


	//获取年份数组
	$dateYear = array_column($data,'birth');
	foreach( $dateYear as $v){
		$year[] = date('Y', strtotime($v) );
	}
	$yearArray = array_unique($year);


	//php7 扩展的api
	$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");


	//数据查询
    $query = new MongoDB\Driver\Query([]);
	$cursor = $manager->executeQuery('test.mdkj', $query);

    // dump( $cursor->toArray() );
	$dbData = [];

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
		$result = $manager->executeBulkWrite('test.mdkj', $bulk, $writeConcern);

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

	$res = $manager->executeCommand( 'test',$cmd );

	echo "不同省份的用户分组信息<br>";
	dump( $res->toArray() );



	echo "<br>-----------------------------------------------<br>";


	// $param1 = [
	// 	'aggregate' => 'mdkj',
	// 	'pipeline' => [
	// 		[
	// 			// '$group' => [
	// 			// 	'_id' => '$birth',
	// 			// 	'sum' => ['$sum' => 1]
	// 			// ],

 //                '$match' => [
 //                    '$birth' => [ '$gt'=> '1']
 //                ]
	// 		]
	// 	],
	// 	'cursor' => new stdClass,
	// ];

	// $cmd2 = new MongoDB\Driver\Command($param1);

	// $res2 = $manager->executeCommand( 'test',$cmd2 );

	echo "不同年龄的用户分组信息<br>";
	// dump( $res2->toArray() );	



	foreach( $yearArray as $y){

		$startDate = $y . "-01-01";
		$endDate = $y . "-12-12";

		$filter = ['birth' => ['$lte' => $endDate , '$gte' => $startDate ]];
		$options = [
		   'projection' => ['_id' => 0]
		];


	    $query = new MongoDB\Driver\Query( $filter ,$options );
		$cursor = $manager->executeQuery('test.mdkj', $query);

		$res = [];
		foreach ($cursor as $document) {
		    $res[] = $document;
		}
		echo "{$y} 年的用户分组信息<br>";
		dump($res);

	}




 ?>