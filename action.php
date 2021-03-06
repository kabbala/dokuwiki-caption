<?php
/**
 * DokuWiki Plugin caption (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Till Biskup <till@till-biskup>
 */
 
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'action.php');

class action_plugin_caption extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook("TOOLBAR_DEFINE", "AFTER", $this, "insert_button", array ());
    }

    /**
    * Inserts a toolbar button
    */
    public function insert_button(&$event, $param) {
        $event->data[] = array (
            'type' => 'picker',
            'title' => $this->getLang('picker'),
            'icon' => '../../plugins/caption/images/picker.png',
            'class' => 'captionpicker',
            'list' => array(
                array(
                     'type' => 'insert',
                     'title' => $this->getLang('figure'),
                     'icon' => '../../plugins/caption/images/fig.png',
                     'insert' => '<figure label>\n{{:img |title}}\n<caption>caption</caption>\n</figure>',
                ),
                array(
                     'type' => 'insert',
                     'title' => $this->getLang('table'),
                     'icon' => '../../plugins/caption/images/tab.png',
                     'insert' => '<table label>\n<caption>caption</caption>\n| table |\n</table>',
                ),
                array(
                     'type' => 'insert',
                     'title' => $this->getLang('reference'),
                     'icon' => '../../plugins/caption/images/ref.png',
                     'insert' => '{{ref>label}}',
                )
            )
        );
    }
}