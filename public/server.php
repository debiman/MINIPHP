<?php
require('../Q/WXS.php');

function getMapUrl($url, $querystring){
    $querystring .= "&key="._::$config["mapKey"];
    $host = 'http://apis.map.qq.com';
    $sn = md5(urlencode($url.'?'.$querystring._::$config["mapSecretKey"]));
    return $host.$url.'?'.$querystring.'&sn='.$sn;
}


class wxServer extends WXS{
	//EVENT 处理入口
  	protected function E_subscribe(){
      $this->responseText('欢迎关注微信');
  	}

  	protected function E_unsubscribe(){
  		//取消关注信息
  	}

  	protected function E_SCAN(){
      	$this->responseText('二维码的EventKey：' . $this->getRequest('EventKey'));
  	}

  	protected function E_LOCATION(){
  		exit("");
      $this->responseText('位置推送：' . $this->getRequest('Latitude') . ',' . $this->getRequest('Longitude'));
	   }

  	protected function E_CLICK(){
      $this->responseText('菜单：' . $this->getRequest('EventKey'));
  	}
	
	   //常用类型处理入口
  	protected function T_text(){
      $this->responseText('文字：' . $this->getRequest('content'));
  	}

	  protected function T_image(){
      $items = array(
        wxResponse::newsItem('标题一123', '描述一', $this->getRequest('picurl'), $this->getRequest('picurl')),
        wxResponse::newsItem('标题二', '描述二', $this->getRequest('picurl'), $this->getRequest('picurl')),
      );
      $this->responseNews($items);
  	}

	  protected function T_location(){
      $query = 'location='.$this->getRequest('location_x') . "," . $this->getRequest('location_y');
      $url = '/ws/geocoder/v1/';
      $json_obj = json_decode(_::GET(getMapUrl($url,$query)),true);
      $this->responseText($json_obj["result"]["address"]);
  	}
	  
    protected function T_link(){
      $this->responseText('链接：' . $this->getRequest('url'));
  	}

	  protected function T_voice(){
      $this->responseText('语音识别结果：' . $this->getRequest('Recognition'));
  	}
  }

  $myServer = new wxServer();
  $myServer->start();








