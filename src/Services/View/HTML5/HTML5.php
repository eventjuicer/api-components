<?php

namespace Eventjuicer\Services\View\HTML5;

use Masterminds\HTML5 AS VendorHTML5;
use Eventjuicer\Services\View\HTML5\Serializer\Traverser;
use Masterminds\HTML5\Serializer\OutputRules;

/**
 * This class offers convenience methods for parsing and serializing HTML5.
 * It is roughly designed to mirror the \DOMDocument class that is
 * provided with most versions of PHP.
 *
 * EXPERIMENTAL. This may change or be completely replaced.
 */
class HTML5 extends VendorHTML5
{


    /**
     * Save a DOM into a given file as HTML5.
     *
     * @param mixed $dom
     *            The DOM to be serialized.
     * @param string $file
     *            The filename to be written.
     * @param array $options
     *            Configuration options when serializing the DOM. These include:
     *            - encode_entities: Text written to the output is escaped by default and not all
     *            entities are encoded. If this is set to true all entities will be encoded.
     *            Defaults to false.
     */
    public function save($dom, $file, $options = array())
    {
        $close = true;
        if (is_resource($file)) {
            $stream = $file;
            $close = false;
        } else {
            $stream = fopen($file, 'w');
        }
        $options = array_merge($this->getOptions(), $options);

        $rules = new OutputRules($stream, $options);

        $trav = new Traverser($dom, $stream, $rules, $options);

        $trav->walk();

        if ($close) {
            fclose($stream);
        }
    }

    // public function saveHTML($dom, $options = array())
    // {
    //     $stream = fopen('php://temp', 'w');
        
    //     $this->save(
    //         $dom, 
    //         $stream, 
    //         array_merge($this->getOptions(), $options)
    //         );

    //     return stream_get_contents($stream, - 1, 0);
    // }
   

}
