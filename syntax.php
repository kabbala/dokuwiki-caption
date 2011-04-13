<?php
/**
 * DokuWiki Plugin caption (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Till Biskup <till@till-biskup>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_caption extends DokuWiki_Syntax_Plugin {

    /**
     * Array containing the types of environment supported by the plugin
     */
	private $_types = array('figure','table');

	private $_type = '';
	private $_incaption = false;

	private $_fignum = 1;
	private $_tabnum = 1;

    public function getType() {
        return 'container';
    }

    public function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled', 'container', 'protected');
    }

    public function getPType() {
//        return 'stack';
        return 'block';
    }

    public function getSort() {
        return 400;
    }


    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<figure>(?=.*</figure>)',$mode,'plugin_caption');
        $this->Lexer->addEntryPattern('<table>(?=.*</table>)',$mode,'plugin_caption');
        $this->Lexer->addPattern('<caption>(?=.*</caption>)','plugin_caption');
        $this->Lexer->addPattern('</caption>','plugin_caption');
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('</figure>','plugin_caption');
        $this->Lexer->addExitPattern('</table>','plugin_caption');
    }

    public function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_ENTER :
            $match = substr($match,1,-1);
          	return array($state, $match);
          case DOKU_LEXER_MATCHED :    return array($state, $match);
          case DOKU_LEXER_UNMATCHED :  return array($state, $match);
          case DOKU_LEXER_EXIT :
            $match = substr($match,1,-1);
          	return array($state, $match);
        }
        return array();
    }

    public function render($mode, &$renderer, $data) {
        if ($mode == 'xhtml') {

            list($state,$match) = $data;
            
            switch ($state) {
                case DOKU_LEXER_ENTER :
					if (in_array($match,$this->_types)) {
						$this->_type = $match;
						switch ($this->_type) {
							case figure :
		    	                $renderer->doc .= '<div class="figure">';
		        	            break;
                    		case table :
		    	                $renderer->doc .= '<div class="table">';
		                    	break;
						}
					}
                    break;

                case DOKU_LEXER_MATCHED :
                	// return the dokuwiki markup within the caption tags
                	if (!$this->_incaption) {
                		$this->_incaption = true;
	                    $renderer->doc .= '<div class="caption">';
						switch ($this->_type) {
							case figure :
		    	                $renderer->doc .= '<span class="captionno">';
		    	                if ($this->getConf('abbrev')) {
		    	                	$renderer->doc .= $this->getLang('figureabbrev');
		    	                } else {
		    	                	$renderer->doc .= $this->getLang('figurelong');
		    	                }
		    	                $renderer->doc .= ' ' . $this->_fignum . ':</span>';
		    	                $renderer->doc .= ' <span class="captiontext">';
		        	            break;
                    		case table :
		    	                $renderer->doc .= '<span class="captionno">';
		    	                if ($this->getConf('abbrev')) {
		    	                	$renderer->doc .= $this->getLang('tableabbrev');
		    	                } else {
		    	                	$renderer->doc .= $this->getLang('tablelong');
		    	                }
		    	                $renderer->doc .= ' ' . $this->_tabnum . ':</span>';
		    	                $renderer->doc .= ' <span class="captiontext">';
		                    	break;
						}
                	} else {
                		$this->_incaption = false;
        	            $renderer->doc .= '</span></div>';
                	}
                    break;

                case DOKU_LEXER_UNMATCHED :
                	// return the dokuwiki markup within the figure tags
                    $renderer->doc .= $renderer->_xmlEntities($match);
                    break;

                case DOKU_LEXER_EXIT :
					// increment figure/table number
					switch ($this->_type) {
						case figure :
                    		$this->_fignum++;
                    		break;
                    	case table :
                    		$this->_tabnum++;
                    		break;
					}
					$this->_type = '';
		    	    $renderer->doc .= '</div>';
                    break;
            }
            return true;
        }
        
        if ($mode == 'latex') {

            list($state,$match) = $data;
            
            switch ($state) {
                case DOKU_LEXER_ENTER :
					if (in_array($match,$this->_types)) {
						$this->_type = $match;
						switch ($this->_type) {
							case figure :
		    	                $renderer->doc .= '\begin{figure}';
		        	            break;
                    		case table :
		    	                $renderer->doc .= '\begin{table}';
		                    	break;
						}
					}
                    break;

                case DOKU_LEXER_MATCHED :
                	// return the dokuwiki markup within the caption tags
                	if (!$this->_incaption) {
                		$this->_incaption = true;
	                    $renderer->doc .= '\caption{';
                	} else {
                		$this->_incaption = false;
        	            $renderer->doc .= '}';
                	}
                    break;

                case DOKU_LEXER_UNMATCHED :
                	// return the dokuwiki markup within the figure tags
                    $renderer->doc .= $renderer->_xmlEntities($match);
                    break;

                case DOKU_LEXER_EXIT :
					switch ($this->_type) {
						case figure :
		                    $renderer->doc .= '\end{figure}';
		    	            break;
                		case table :
	    	                $renderer->doc .= '\end{table}';
	    	            	break;
					}
					$this->_type = '';
                    break;
            }
            return true;
        }

        // unsupported $mode
        return false;
    }
}

// vim:ts=4:sw=4:et:
