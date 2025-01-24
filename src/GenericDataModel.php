<?php
namespace ObjectFlow;
use ObjectFlow\LockableObject;
use ObjectFlow\Readers\InstantiableObjectReader;
use ObjectFlow\Readers\InstatiableObjectMethodReader;

class GenericDataModel extends LockableObject {

    public function __construct() {
        foreach(InstantiableObjectReader::readAllPropertysWithFlowBusinessRule($this) as $oProperty) {
            $this->set($oProperty->getName(), $oProperty->getDefaultValue());
        }
        // $this->lock();
    }

    protected function __callSet($sSetter, $xArguments) {
        if(method_exists($this, $sSetter)) {
            return call_user_func([$this, $sSetter], $xArguments);
        }
        $sProperty = InstatiableObjectMethodReader::extractPropertyNameFromGetterSetter($sSetter);
        $bPropertyWithSameName = false;
        foreach(InstantiableObjectReader::readAllPropertysWithFlowBusinessRule($this) as $oProperty) {
            $sDualPrefix = substr($oProperty->getName(), 0, $iPrefixModifier = 2);
            $iPrefixModifier = $sDualPrefix !== 'fn' ? 1 : $iPrefixModifier;
            $sPropertyName = lcfirst(substr($oProperty->getName(), $iPrefixModifier));
            $bPropertyWithSameName = $sProperty === $sPropertyName;
            if($bPropertyWithSameName) {
                $sProperty = $oProperty->getName();
                break;
            }
        }
        if(!$bPropertyWithSameName) {
            return trigger_error(strtr('Property with default name {sProperty} does not exists.', [ '{sProperty}' => $sProperty]), E_USER_ERROR);
        }
        return $this->set($sProperty, ...$xArguments);
    }

    protected function __callGet($sGetter) {
        if(method_exists($this, $sGetter)) {
            return call_user_func([$this, $sGetter]);
        }
        $sProperty = InstatiableObjectMethodReader::extractPropertyNameFromGetterSetter($sGetter);
        $bPropertyWithSameName = false;
        foreach(InstantiableObjectReader::readAllPropertysWithFlowBusinessRule($this) as $oProperty) {
            $sDualPrefix = substr($oProperty->getName(), $iPrefixModifier = 2);
            $iPrefixModifier = $sDualPrefix !== 'fn' ? 1 : $iPrefixModifier;
            $sPropertyName = lcfirst(substr($oProperty->getName(), $iPrefixModifier));
            $bPropertyWithSameName = $sProperty === $sPropertyName;
            if($bPropertyWithSameName) {
                $sProperty = $oProperty->getName();
                break;
            }
        }
        if(!$bPropertyWithSameName) {
            return trigger_error(strtr('Property with default name {sProperty} does not exists.', [ '{sProperty}' => $sProperty]), E_USER_ERROR);
        }
        return $this->get($sProperty);
    }
    
    public function __call($sMethod, $xArgs) {
        if(InstatiableObjectMethodReader::isMethodGetterOrSetter($sMethod)) {
            $this->bLocked = false;
            $xReturn = parent::__call($sMethod, $xArgs);
            $this->bLocked = true;
            return $xReturn;
        }
        return parent::__call($sMethod, $xArgs);
    }

}