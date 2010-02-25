<?php

/**
 * @author Sean Burlington www.practicalweb.co.uk
 * @copyright PracticalWeb Ltd
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
class Ckan {
	private  $url = 'http://www.ckan.net/';
	private $errors = array( 
	                            '0'  =>   'Network Error?',
	                          '301'  =>   'Moved Permanently',
                              '400'  =>   'Bad Request',
                              '403'  =>   'Not Authorized',
                              '404'  =>   'Not Found',
	                          '409'  =>   'Conflict (e.g. name already exists)',
                              '500'  =>   'Internal Server Error', 
	);
	
	public function __construct($url=null){
		if ($url){
			$this->url=$url;
		}
	}
	
	private function transfer($url){

		$ch = curl_init($this->url . $url);


		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if ($info['http_code'] != 200){
			throw new CkanException($info['http_code'] . ' : ' . $this->error_codes["$info[http_code]"]);
		}
		if (!$result){
			throw new CkanException("No Result");
		}
		return json_decode($result);
	}

	public function search($keyword){
		$results = $this->transfer('api/search/package/?all_fields=1&q=' . urlencode($keyword));
		if (!$results->count){
			throw new CkanException("Search Error");
		}
		return $results;
	}

	public function getPackage($package){
		$package = $this->transfer('api/rest/package/' . urlencode($package));
		if (!$package->name){
			throw new CkanException("Package Load Error");
		}
		return $package;
	}


	public function getPackageList(){
		$list =  $this->transfer('api/rest/package/');
		if (!is_array($list)){
			throw new CkanException("Package List Error");
		}
		return $list;
	}

	public function getGroup($group){
		$group = $this->transfer('api/rest/group/' . urlencode($group) );
		if (!$group->name){
			throw new CkanException("Group Error");
		}
		return $group;
	}

	public function getGroupList(){
		$groupList = $this->transfer('api/rest/group/');
		if (!is_array($groupList)){
			throw new CkanException("Group List Error");
		}
		return $groupList;
	}
}

class CkanException extends Exception{}
