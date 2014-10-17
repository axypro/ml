<?php
/**
 * @package axy\ml
 */

namespace axy\ml\tags;

/**
 * Tag [*]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Li extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getHTML()
    {
        $context = $this->context;
        $block = $context->block;
        $vars = $context->vars;
        if (empty($vars->lists)) {
            if (($block->content !== '') && (\ltrim($block->content) !== '')) {
                return '';
            }
            $vars->lists = [];
            $block->endListeners[] = [$this, 'onEndBlock'];
            $levellist = null;
            $block->wrap = false;
            $nl = $context->options['beauty'] ? "\n" : '';
            $vars->listnl = $nl;
        } else {
            $levellist = $vars->levellist;
            $nl = $vars->listnl;
        }
        $delta = $this->level - $levellist;
        $result = '';
        $csslist = ($this->options['css'] === null) ? '' : ' class="'.$this->escape($this->options['css']).'"';
        if ($this->value !== '') {
            $cssli = ' class="'.$this->escape($this->value).'"';
        } elseif ($this->options['css_li'] !== null) {
            $cssli = ' class="'.$this->escape($this->options['css_li']).'"';
        } else {
            $cssli = null;
        }
        if ($delta === 0) {
            $block->content = \rtrim($block->content);
            $block->ltrim = true;
            $result = '</li>'.$nl.'<li'.$cssli.'>';
        } else {
            $type = $this->getArg();
            $vars->levellist = $this->level;
            $result = '';
            $block->ltrim = true;
            if ($delta < 0) {
                $block->content = \rtrim($block->content);
                for ($i = $delta; $i <= $delta + 1; $i++) {
                    $result .= '</li>'.$nl.'</'.\array_pop($vars->lists).'>';
                }
                $result .= '</li>'.$nl.'<li'.$cssli.'>';
            } else {
                for ($i = 0; $i < $delta - 1; $i++) {
                    $vars->lists[] = 'ul';
                    $result .= '<ul'.$csslist.'>'.$nl.'<li'.$cssli.'>';
                }
                if ((empty($type) && ($type !== '0')) || (\strtolower($type) === 'ul')) {
                    $vars->lists[] = 'ul';
                    $result .= '<ul'.$csslist.'>'.$nl.'<li'.$cssli.'>';
                } else {
                    $vars->lists[] = 'ol';
                    if ((string)(int)$type == $type) {
                        $start = (int)$type;
                    } else {
                        $start = 1;
                    }
                    if ($start !== 1) {
                        $result .= '<ol start="'.$start.'"'.$csslist.'>'.$nl.'<li'.$cssli.'>';
                    } else {
                        $result .= '<ol'.$csslist.'>'.$nl.'<li'.$cssli.'>';
                    }
                }
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlain()
    {
        return '';
    }

    public function onEndBlock()
    {
        $vars = $this->context->vars;
        if (!empty($vars->lists)) {
            $r = '';
            $nl = $vars->listnl;
            foreach (\array_reverse($vars->lists) as $l) {
                $r .= '</li>'.$nl.'</'.$l.'>';
            }
            $this->context->block->content .= $r;
            $vars->lists = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparse()
    {
        if (\preg_match('/^([*]+)(.*?)$/s', $this->value, $matches)) {
            $this->level = \strlen($matches[1]);
            $this->value = $matches[2];
        }
        parent::preparse();
    }

    /**
     * {@inheritdoc}
     */
    protected $options = [
        'css' => null,
        'css_li' => null,
    ];

    /**
     * @var int
     */
    protected $level = 0;
}
