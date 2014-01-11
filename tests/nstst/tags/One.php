<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tests\nstst\tags;

class One extends \axy\ml\tags\Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        return \implode('.', $this->args).':'.\implode('.', $this->components);
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
     * @param string $url
     * @return string
     */
    public static function handleUrl($prefix, $url)
    {
        if (\strpos($url, $prefix) === 0) {

        }
        return $url;
    }

    /**
     * @var array
     */
    private $components = [];
}
