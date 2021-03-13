<?php


namespace App\Traits;


trait AutoFillable
{
    /**
     * Assign given values to current or given object.
     *
     * @param $values
     * @param bool $object
     * @return $this
     */
    public function setProperties($values, $object = false)
    {
        if ($object) {
            foreach ($values as $key => $value) {
                $object->{$key} = $value;
            }
        } else {
            foreach ($values as $key => $value) {
                $this->{$key} = $value;
            }
        }

        return $object ?: $this;
    }
}
