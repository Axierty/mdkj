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

	public function getRandomUser( $num = 10)
	{

		$length = count($this->province);
		$timestamp = time();
		$user = [];

		for( $i=0; $i<$num;$i++){

			$user[] = [
				'phone' => '1'.mt_rand(10000,99999).mt_rand(10000,99999),
				'birth' =>  date('Y-m-d',$timestamp - mt_rand(40000000,900000000)),  //简单城市随机年月日
				'province' => $this->province[ mt_rand(0,$length - 1) ] 
			];

		}
		return $user;

	}


	/**
	 * 获取时间段数组
	 * @param  [type]  $date [description]
	 * @param  integer $num  [description]
	 * @return [type]        [description]
	 */
	public function getDateArea( $date=[] , $num = 2)
	{
		if( $date ){

			rsort($date);

			$max = $date[0];
			$min = $date[ count($date)-1 ];

			$length = $max - $min;
			//计算区间
			$areaNum = ceil( $length / $num );
			$dateArea = [];

			for( $i=0; $i<$areaNum;$i++){
				$dateArea[] = [
					$max - $i*$num,
					$max - ($i+1)*$num
				];
			}

			return $dateArea;


		}else{
			return [];
		}

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