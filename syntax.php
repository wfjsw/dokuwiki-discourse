<?php
/**
 * Plugin Discourse: Inserts an iframe element to include the specified url
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 */
 // must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_discourse extends DokuWiki_Syntax_Plugin {

    function getType() { return 'substition'; }
    function getSort() { return 305; }
    function connectTo($mode) { $this->Lexer->addSpecialPattern('{{discourse>[0-9]+}}',$mode,'plugin_discourse'); }

    function handle($match, $state, $pos, Doku_Handler $handler){
        $topicId = intval(substr($match, 12, -2));

        // set defaults
        $opts = array(
            'topicId'  => $topicId,
        );

        return $opts;
    }

    function render($mode, Doku_Renderer $R, $data) {
        if($mode != 'xhtml') return false;
        
        if (!$this->getConf('discourse_host'))
            return false;

        $R->doc .= "<div id='discourse-comments'></div>";

        $R->doc .= "<script type=\"text/javascript\">
            window.DiscourseEmbed = { discourseUrl: '".$this->getConf('discourse_host')."',
                                topicId: ".json_encode($data["topicId"])." };

            (function() {
                var d = document.createElement('script'); d.type = 'text/javascript'; d.async = true;
                d.src = window.DiscourseEmbed.discourseUrl + 'javascripts/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(d);
            })();

            window.addEventListener('message', function () {
                jQuery('#discourse-embed-frame').attr('allowtransparency', 'true');
            }, false);
            </script>";

        return true;
    }
}
