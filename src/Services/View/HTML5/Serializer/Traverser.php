<?php

namespace Eventjuicer\Services\View\HTML5\Serializer;

/**
 * Traverser for walking a DOM tree.
 *
 * This is a concrete traverser designed to convert a DOM tree into an
 * HTML5 document. It is not intended to be a generic DOMTreeWalker
 * implementation.
 *
 * @see http://www.w3.org/TR/2012/CR-html5-20121217/syntax.html#serializing-html-fragments
 */

use Masterminds\HTML5\Serializer\Traverser as VendorTraverser;

class Traverser extends VendorTraverser
{


    private function preparse()
    {
        return !empty($this->options["preparse"]);
    }
    
     private function dispatcher()
    {
        return isset($this->options["dispatcher"]) && is_object($this->options["dispatcher"]) ? $this->options["dispatcher"] : null;
    }


    private function parentObject()
    {
        return isset($this->options["parentObject"]) && is_object($this->options["parentObject"]) ? $this->options["parentObject"] : null;
    }


    public function node($node)
    {

        // A listing of types is at http://php.net/manual/en/dom.constants.php
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:

                if(strpos($node->nodeName, "data-")!==false)
                {
                   
                 
                        if($this->preparse() && !$this->dispatcher()->cachable($node))
                        {
                            $this->rules->element($node);
                        }
                        else
                        {
                            fwrite($this->out, "<!-- START: ".$node->nodeName." -->");

                             try{

                                
                                fwrite($this->out, $this->dispatcher()->domElement(
                                    str_replace("data-", "", $node->nodeName), 
                                    $node, 
                                    $this->parentObject()
                                ));
                               

                            }
                            catch(\Exception $e)
                            {
                                fwrite($this->out, "<!-- parse error -->");
                            }

                            fwrite($this->out, "<!-- END: ".$node->nodeName." -->");
                        }
                  
                }
                else
                {
                    $this->rules->element($node);
                }
                
                break;
            case XML_TEXT_NODE:
                $this->rules->text($node);
                break;
            case XML_CDATA_SECTION_NODE:
                $this->rules->cdata($node);
                break;
            case XML_PI_NODE:
                $this->rules->processorInstruction($node);
                break;
            case XML_COMMENT_NODE:
                $this->rules->comment($node);
                break;
            // Currently we don't support embedding DTDs.
            default:
                //print '<!-- Skipped -->';
                break;
        }
    }

  
}
