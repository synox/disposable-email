<?php

namespace DisposableEmail;

class AutoLinkTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array('auto_link_text' => new \Twig_Filter_Method($this, 'auto_link_text', array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return "auto_link_twig_extension";
    }

    static public function auto_link_text($string)
    {

        $string = preg_replace_callback("/
            ((?<![\"'])                                     # don't look inside quotes
            (\b
            (                           # protocol or www.
                [a-z]{3,}:\/\/
            |
                www\.
            )
            (?:                         # domain
                [a-zA-Z0-9_\-]+
                (?:\.[a-zA-Z0-9_\-]+)*
            |
                localhost
            )
            (?:                         # port
                 \:[0-9]+
            )?
            (?:                         # path
                \/[a-z0-9:%_|~.-]*
                (?:\/[a-z0-9:%_|~.-]*)*
            )?
            (?:                         # attributes
                \?[a-z0-9:%_|~.=&#;-]*
            )?
            (?:                         # anchor
                \#[a-z0-9:%_|~.=&#;-]*
            )?
            )
            (?![\"']))
            /ix", function($match) {
                $url = $match[0];
                $href = $url;
                
                if (false == strpos($href, 'http')) {
                    $href = 'http://' . $href;
                }
                return '<a href="' . URI_REDIRECT_PREFIX . $href . '" rel="noreferrer">' . $url . '</a>';
            }
            , $string);


        $string = AutoLinkTwigExtension::unescape($string);

        return $string;
    } # filter()

    /**
     * unescape()
     *
     * @param string $text
     * @return string $text
     **/
    static function unescape($text) {
        global $escape_autolink_uri;

        if ( !$escape_autolink_uri )
            return $text;

        $unescape = array_reverse($escape_autolink_uri);

        return str_replace(array_keys($unescape), array_values($unescape), $text);
    } # unescape()

}
