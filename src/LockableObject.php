<?php
namespace ObjectFlow;
use ObjectFlow\GenericObject;

class LockableObject extends GenericObject {

    /**
     * 
     * @internal
     * @var bool
     */
    protected $bLocked = false;

    /**
     * 
     * @return self
     */
    protected function lock() {
        $this->bLocked = true;
        return $this;
    }

    /**
     * @param mixed $sMethod
     * @param mixed $xArgs
     * @return mixed
     */
    public function __call($sMethod, $xArgs) {
        if($this->bLocked) {
            trigger_error('Cannot call any method in a lock object', E_USER_ERROR);
        }
        return parent::__call($sMethod, $xArgs);
    }
    
}