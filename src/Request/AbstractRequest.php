<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use InvalidArgumentException;
use TypeError;

abstract class AbstractRequest implements RequestInterface
{
    /** @var Request */
    protected $request;

    /** @var array  */
    protected $errors = [];

    /**
     * AbstractRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $field
     * @param string $class
     * @param null $default
     * @return mixed|null
     */
    protected function getFromRequest(string $field, string $class, $default = false)
    {
        $value = null;
        try {
            $requestData = $this->request->get('data');
            if (isset($requestData[$field])) {
                $value = $requestData[$field];
            }
            if ($value === null) {
                if ($default !== false) {
                    $value = $default;
                } else {
                    throw new InvalidArgumentException('Field ' . $field . ' is required');
                }
            } else {
                $value = new $class($value);
            }
        } catch (InvalidArgumentException $e) {
            $this->errors[$field] = $e->getMessage();
        } catch (TypeError $e) {
            if (isset($this->request->get('data')[$field])) {
                $this->errors[$field] = 'Wrong type of value. Expected 
                ' . get_parent_class($class) . ', ' . gettype($this->request->get('data')[$field]) . ' given';
            } else {
                $this->errors[$field] = 'Field ' . $field . ' is required';
            }
        }
        return $value;
    }

    /**
     * @param string $field
     * @param string $collectionClass
     * @param string $itemClass
     * @param false $default
     * @return mixed|null
     */
    public function getCollectionFromRequest(
        string $field,
        string $collectionClass,
        string $itemClass,
        $default = false
    ) {
        $value = null;
        try {
            $requestData = $this->request->get('data');
            if (isset($requestData[$field])) {
                $value = $requestData[$field];
            }
            if ($value === null) {
                if ($default !== false) {
                    $value = new $collectionClass($default);
                } else {
                    throw new InvalidArgumentException('Field ' . $field . ' is required');
                }
            } else {
                $array = [];
                foreach ($value as $item) {
                    $array[] = new $itemClass($item);
                }
                $value = new $collectionClass($array);
            }
        } catch (InvalidArgumentException $e) {
            $this->errors[$field] = $e->getMessage();
        } catch (TypeError $e) {
            if (isset($this->request->get('data')[$field])) {
                $this->errors[$field] = 'Wrong type of value. Expected array of ' . $itemClass;
            } else {
                $this->errors[$field] = 'Field ' . $field . ' is required';
            }
        }
        return $value;
    }
}
