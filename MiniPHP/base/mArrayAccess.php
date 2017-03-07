<?php

class mArrayAccess implements ArrayAccess{
    public $item = array();
    public function offsetExists($key) {
        return isset($this->item[$key]);
    }
    public function __set($key, $value) {
        $this->item[$key] = $value;
    }

    public function __get($key) {
        return $this->item[$key];
    }

    public function offsetSet($key, $value) {
        $this->item[$key] = $value;
    }

    public function offsetGet($key) {
        return $this->item[$key];
    }

    public function offsetUnset($key) {
        unset($this->item[$key]);
    }
}
?>
