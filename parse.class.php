<?php
	/**
		* Parse Friends parse.class.php, Unfriend Deactive Account
		*
		* See list friends with Facebook API, @link https://developers.facebook.com/docs/graph-api/reference/user/friends/
		*
		* Requirements:
		*
		* OpenSSL functions installed and PHP version >= 5.0
		* or
		* Mcrypt functions installed.
		*
		* For PHP under version 7 it is recommendable you to install within your project
		* "PHP 5.x support for random_bytes() and random_int()",
		* @link https://github.com/paragonie/random_compat
		*
		* Usage:
		*
		* @author Huy Nguyen <nmhuy12@gmail.com> 2013-2016
		* @version 1.2.0
		*
		* @license The MIT License (MIT)
		* @link http://opensource.org/licenses/MIT
	*/

	header('Content-Type: text/html; charset=utf-8');
	ini_set('max_execution_time', 3000);

	class ParseFriends
	{
		function __construct($c_user, $xs, $access_token)
		{
			$this->data = new ArrayObject();
			$this->data->setFlags(ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);
			$this->data->c_user = $c_user;
			$this->data->xs = $xs;
			$this->data->access_token = $access_token;
		}

		/**
			* Unfriend Facebook User (Deactived)
			* Return @string
		*/

		public function Parse()
		{
			$Page = $this->cURL('https://www.facebook.com/', $this->data);
			preg_match("/list:\[(.*?)\]/", $Page, $ListFriend);
			preg_match_all("/\"(.*?)\"/", $ListFriend[1], $ListID);

			$TempList = Array();
			$Num = 0;
			for ($i = 0; $i < count($ListID[1]); $i++)
			{ 
				$ListID[1][$i] = substr($ListID[1][$i], 0, strlen($ListID[1][$i])-2);
				if (!isset($TempList[$ListID[1][$i]]) && ($this->Search($ListID[1][$i]) == false))
				{
					$Num++;
					print_r('['.$ListID[1][$i].'] => Facebook User, Unfriended! ['.$Num.']');
					$Unfriended = $this->Unfriended($ListID[1][$i]);
					$TempList[$ListID[1][$i]] = false;
					print_r('<br>');
				}
				else if (!isset($TempList[$ListID[1][$i]]))
				{
					print_r('['.$ListID[1][$i].'] => '.$this->Search($ListID[1][$i]));
					$TempList[$ListID[1][$i]] = false;
					print_r('<br>');
				}
				ob_flush();
				flush();
			}
		}

		/**
			* @constructor
			* @return boolean
		**/

		protected function Search($uid)
		{
			$graph = json_decode($this->cURL('https://graph.facebook.com/v2.8/'.$uid.'/?fields=name&access_token='.$this->data->access_token));
			if (!isset($graph->name)) return false; else return $graph->name;
		}

		/**
	    	* @return none
		*/

		protected function Unfriended($uid)
		{
			return json_decode($this->cURL('https://graph.facebook.com/me/friends?method=DELETE&uid='.$uid.'&access_token='.$this->data->access_token));
		}

		/**
			* @param string $url
			* @param array $cookie
			* @param string $PostFields
			* @return string
		*/

		protected function cURL($url, $cookie = false, $PostFields = false){
			$c = curl_init();
			$opts = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FRESH_CONNECT => true,
				CURLOPT_FOLLOWLOCATION => true
			);
			if($PostFields){
				$opts[CURLOPT_POST] = true;
				$opts[CURLOPT_POSTFIELDS] = $PostFields;
			}
			if($cookie){
				$opts[CURLOPT_COOKIE] = 'c_user='.$cookie->c_user.'; xs='.$cookie->xs;
				$opts[CURLOPT_COOKIEJAR] = md5(json_encode(is_array($cookie)?$cookie:array('cookie' => $cookie)));
				$opts[CURLOPT_COOKIEFILE] = md5(json_encode(is_array($cookie)?$cookie:array('cookie' => $cookie)));
			}
			curl_setopt_array($c, $opts);
			$data = curl_exec($c);
			curl_close($c);
			return $data;
		}
	}
?>
