<?php

class mCookie extends mArrayAccess{
    public function __construct($id=COOKIE_ID){
        if (isset($_COOKIE[$id])){
            $codeJson = mCrypt::decode($_COOKIE[$id]);
            $this->item = json_decode($codeJson,true);
            $this->_mTIME = time();
            $this->_mIP = _IP();
        }
    }
    public function save($life=COOKIE_TIME,$domain=COOKIE_DOMAIN,$id=COOKIE_ID){
        $value = json_encode($this->item);
    	setrawcookie($id, mCrypt::encode($value), time()+$life,"/", $domain);
    }
    public function remove($domain=COOKIE_DOMAIN,$id=COOKIE_ID){
        $this->item = array();
    	setrawcookie($id, "",time()-1,"/", $domain);
    }
    public function get($key){
        if (!$this->item[$key]){
            return false;
        }
        return $this->item[$key];
    }
    public function set($key,$val){
        $this->item[$key] = $val;
    }
}
?>
