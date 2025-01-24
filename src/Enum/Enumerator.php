<?php
namespace ObjectFlow\Enum;
use ObjectFlow\Readers\InstantiableObjectReader;

abstract class Enumerator {

    public static function getValues() {
        return array_map(function($xValue) {
            return $xValue;
        }, InstantiableObjectReader::readConstants(static::class));
    }

    public static function getDescriptions() {
        return array_filter(array_map(function($sName) {
            if($sName && $sName[0] === '_') {
                return;
            }
            $sDescriptionName = sprintf('_%s', $sName);
            return InstantiableObjectReader::readConstant($sDescriptionName, static::class) 
                ?: $sName;
        }, array_keys(InstantiableObjectReader::readConstants(static::class))));
    }

    public static function getDescriptionsValues() {
        $aDescriptionsValues = [];
        foreach(InstantiableObjectReader::readConstants(static::class) as $sName => $xValue) {
            if($sName && $sName[0] === '_') {
                return;
            }
            $sDescriptionName = sprintf('_%s', $sName);
            $sDescriptionName = InstantiableObjectReader::readConstant($sDescriptionName, static::class) 
                ?: $sName;
            $aDescriptionsValues[$sDescriptionName] = $xValue;
        }
        return $aDescriptionsValues;
    }

    public static function getDescriptionByValue($xValue) {
        foreach(self::getDescriptionsValues() as $sDescription => $xFilterValue) {
            if($xFilterValue === $xValue) {
                return $sDescription;
            }
        }
    }

    public static function getValueByDescription($sDescription) {
        foreach(self::getDescriptionsValues() as $sFilterDescription => $xFilterValue) {
            if($sDescription === $sFilterDescription) {
                return $xFilterValue;
            }
        }
    }

}