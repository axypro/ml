<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\nstst\tags;

use axy\ml\tags\Base;

class One extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        return implode('.', $this->args).':'.implode('.', $this->components);
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $this->components[] = $this->getNextComponent();
        $this->components[] = $this->getNextComponent();
        $last = $this->getLastComponent();
        $this->components[] = $last;
        if (empty($last)) {
            $this->errors[] = 'not enough data';
        }
    }

    /**
     * @param string $prefix
     * @param object $params
     * @return string
     */
    public static function handleUrl($prefix, $params)
    {
        if (strpos($params->url, $prefix) === 0) {
            $params->url = 'http://example.loc/'.substr($params->url, 1);
        }
    }

    /**
     * @var array
     */
    private $components = [];
}
