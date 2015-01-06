<?php
/**
 * @package axy\ml
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */

namespace axy\ml\tags;

use axy\callbacks\Callback;

/**
 * Tag [CODE]
 *
 * @example [code:php echo 2 + 2;]
 */
class Code extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $params = $this->params;
        if ($params->block) {
            $block = $this->context->block;
            $block->split = true;
            $block->create = false;
        }
        if ($params->html !== null) {
            return $params->html;
        }
        $html = '';
        $aeClass = false;
        if (($params->attr !== null) && ($params->lang !== null)) {
            $val = $this->escape($params->lang);
            if (($params->attr === 'class') && ($params->css !== null) && (!$params->block)) {
                $val .= ' '.$this->escape($params->css);
                $aeClass = true;
            }
            $aLang = ' '.$params->attr.'="'.$val.'"';
        } else {
            $aLang = '';
        }
        if (($params->css !== null) && (!$aeClass)) {
            $aCss = ' class="'.$this->escape($params->css).'"';
        } else {
            $aCss = '';
        }
        if ($params->block) {
            $html .= '<pre'.$aCss.'><code'.$aLang.'>'.$params->source."\n</code></pre>";
        } else {
            $html .= '<code'.$aLang.$aCss.'>'.$params->source.'</code>';
        }
        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return $this->params->plain;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse()
    {
        $params = $this->params;
        if ($this->options['lang']) {
            $params->lang = $this->options['lang'];
        } else {
            $params->lang = $this->getArg(0, $this->options['default_lang']);
        }
        if (!$this->params->block) {
            $this->value = rtrim($this->value);
        }
        $params->source = $this->escape($this->value);
        $params->plain = $this->value;
        $params->css = $params->block ? $this->options['css_block'] : $this->options['css_inline'];
        $this->params->attr = $this->options['attr_lang'];
        if ($this->options['handler']) {
            Callback::call($this->options['handler'], [$this->params]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparse()
    {
        $this->params = (object)[
            'block' => (strpos($this->value, "\n") !== false),
            'lang' => null,
            'source' => null,
            'plain' => null,
            'css' => null,
            'attr' => null,
            'html' => null,
        ];
        parent::preparse();
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'handler' => null,
        'css_block' => null,
        'css_inline' => null,
        'attr_lang' => 'rel',
        'lang' => null,
        'default_lang' => null,
    ];

    /**
     * @var object
     */
    private $params;
}
