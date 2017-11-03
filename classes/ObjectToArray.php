<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 3. 11. 2017
 * Time: 12:28
 */

abstract class ObjectToArray
{
    public function getArray(){
        $class = new ReflectionClass(get_class($this));

        $resultArray = array();
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $value = $property->getValue($this);

            if ($value instanceof ObjectToArray){
                $resultArray[$property->name] = $value->getArray();
            }else{
                $resultArray[$property->name] = $value;
            }
        }

        return $resultArray;
    }
}