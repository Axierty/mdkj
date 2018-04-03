<?php 



class Base
{

	private $province = [
		'湖北',
		'湖南',
		'安徽',
		'江西',
		'广州',
		'新疆',
		'吉林'
	];

	function getRandomUser( $num = 10)
	{

		$length = count($this->province);
		$timestamp = time();
		$user = [];

		for( $i=0; $i<$num;$i++){

			$user[] = [
				'phone' => '1'.mt_rand(10000,99999).mt_rand(10000,99999),
				'birth' =>  date('Y-m-d',$timestamp - mt_rand(10000000,100000000)),
				'province' => $this->province[ mt_rand(0,$length - 1) ] 
			];

		}
		return $user;

	}

}


/**
 * 调试函数
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function dump( $data )
{
	echo "<pre>";
	var_dump($data);
	echo "</pre>";
}
	

 ?>