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
        if (!\preg_match('/^([*]+)(:?)(.*?)$/s', $this->value, $matches)) {
            $this->lists = null;
            return '';
        }
        $level = \strlen($matches[1]);
        $delta = $level - $levellist;
        $result = '';
        if ($delta === 0) {
            $block->content = \rtrim($block->content);
            $block->ltrim = true;
            $result = '</li>'.$nl.'<li>';
        } else {
            $attr = \strtolower($matches[3]);
            $vars->levellist = $level;
            $result = '';
            $block->ltrim = true;
            if ($delta < 0) {
                $block->content = \rtrim($block->content);
                for ($i = $delta; $i <= $delta + 1; $i++) {
                    $result .= '</li>'.$nl.'</'.\array_pop($vars->lists).'>';
                }
                $result .= '</li>'.$nl.'<li>';
            } else {
                for ($i = 0; $i < $delta - 1; $i++) {
                    $vars->lists[] = 'ul';
                    $result .= '<ul>'.$nl.'<li>';
                }
                if ((empty($attr)) || ($attr === 'ul')) {
                    $vars->lists[] = 'ul';
                    $result .= '<ul>'.$nl.'<li>';
                } else {
                    $vars->lists[] = 'ol';
                    $start = (int)$attr;
                    if ($start > 1) {
                        $result .= '<ol start="'.$start.'">'.$nl.'<li>';
                    } else {
                        $result .= '<ol>'.$nl.'<li>';
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
    protected function parse()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected $args = false;
}
