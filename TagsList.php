<?php
/**
 * @package axy\ml
 */

namespace axy\ml;

use axy\ml\helpers\Config;

/**
 * Access to available tags
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class TagsList
{
    /**
     * Constructor
     *
     * @param array $tags [optional]
     *        the list of custom tags
     */
    public function __construct(array $custom = [])
    {
        $this->default = Config::getTags();
        $this->custom = $custom;
    }

    /**
     * Get parameters for create a tag instance
     *
     * @param string $name
     * @return array (classname, options, name) or NULL
     */
    public function getParams($name)
    {
        if (!\array_key_exists($name, $this->cache)) {
            $this->cache[$name] = $this->createParams($name);
        }
        return $this->cache[$name];
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function createParams($name)
    {
        if (\array_key_exists($name, $this->custom)) {
            $custom = $this->custom[$name];
            if ($custom === null) {
                return null;
            }
            $custom = $this->normalize($custom);
        } else {
            $custom = null;
        }
        if (\array_key_exists($name, $this->default)) {
            $default = $this->normalize($this->default[$name]);
        } else {
            $default = null;
        }
        if ($custom && $default) {
            $result = [
                'classname' => $custom['classname'] ?: $default['classname'],
                'options' => \array_replace($default['options'], $custom['options']),
            ];
        } elseif ($custom) {
            $result = $custom;
        } elseif ($default) {
            $result = $default;
        } else {
            return null;
        }
        if (empty($result['classname'])) {
            return null;
        }
        $result['name'] = $name;
        $first = $result['classname'][0];
        if ($first === '=') {
            $alias = $this->getParams(\substr($result['classname'], 1));
            if (!$alias) {
                return null;
            }
            if (!empty($result['options'])) {
                $alias['options'] = \array_replace($alias['options'], $result['options']);
            }
            return $alias;
        } elseif ($first === '\\') {
            $result['classname'] = \substr($result['classname'], 1);
        } else {
            $result['classname'] = __NAMESPACE__.'\tags\\'.$result['classname'];
        }
        if (!\class_exists($result['classname'], true)) {
            return null;
        }
        return $result;
    }

    /**
     * @param mixed $params
     * @return array
     */
    private function normalize($params)
    {
        if (!\is_array($params)) {
            return [
                'classname' => $params,
                'options' => [],
            ];
        }
        return [
            'classname' => empty($params['classname']) ? null : $params['classname'],
            'options' => empty($params['options']) ? null : $params['options'],
        ];
    }

    /**
     * @var array
     */
    private $default;

    /**
     * @var array
     */
    private $custom;

    /**
     * @var array
     */
    private $cache = [];
}
