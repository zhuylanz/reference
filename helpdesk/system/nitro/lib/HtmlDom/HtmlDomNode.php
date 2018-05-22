<?php
class HtmlDomNode {
    public $tagName;
    public $children;
    public $parent;
    public $root;
    public $detached;
    public $htmlNode;
    public $headNode;
    public $bodyNode;
    public $isVoid;

    private $attributes;
    private $comments;
    private $innerText;
    private $doctype = '';
    private $containingElement;

    public static $self_closing_tags = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');
    public static $head_tags = array('meta', 'link', 'script', 'noscript', 'style', 'template', 'title', 'base');
    public static $optional_closing_tags = array('html', 'head', 'body', 'p', 'dt', 'dd', 'li', 'option', 'thead', 'th', 'tbody', 'tr', 'td', 'tfoot', 'colgroup');
    public static $optional_closing_tags_map = array(
        'p' => array('address', 'article', 'aside', 'blockquote', 'div', 'dl', 'fieldset', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'main', 'nav', 'ol', 'p', 'pre', 'section', 'table', 'ul'),
        'dt' => array('dt', 'dd'),
        'dd' => array('dd', 'dt'),
        'li' => array('li'),
        'rb' => array('rb', 'rt', 'rtc', 'rp'),
        'rt' => array('rb', 'rt', 'rtc', 'rp'),
        'rp' => array('rb', 'rt', 'rtc', 'rp'),
        'rtc' => array('rb', 'rtc', 'rp'),
        'optgroup' => array('optgroup'),
        'option' => array('option', 'optgroup'),
        'thead' => array('tbody', 'tfoot'),
        'tbody' => array('tbody', 'tfoot'),
        'tfoot' => array('tbody'),
        'tr' => array('tr'),
        'td' => array('tr', 'td', 'th'),
        'th' => array('td', 'th'),
    );

    public function __construct($tagName, $parent = NULL, $root = NULL) {
        $this->tagName = strtolower($tagName);
        $this->parent = $parent;

        $this->htmlNode = NULL;
        $this->headNode = NULL;
        $this->bodyNode = NULL;

        $this->isVoid = false;
        $this->detached = true;
        $this->attributes = new SplObjectStorage();
        $this->children = new SplObjectStorage();
        $this->comments = new SplObjectStorage();

        $this->innerText = '';

        if ($root) {
            $this->root = $root;
        } else {
            $this->root = $this;

            if (!$this->tagName) {//This is important for the cases where somebody creates a new node from outside of this class like this `new HtmlDomNode('div')` and he then wants to append this to an existing DOM for example
                $this->htmlNode = new HtmlDomNode('html', $this, $this);
                $this->headNode = new HtmlDomNode('head', $this->htmlNode, $this);
                $this->bodyNode = new HtmlDomNode('body', $this->htmlNode, $this);
            }
        }

    }

    public function getDoctype() {
        return $this->root->doctype;
    }

    public function addComment(&$comment) {
        $this->comments->attach(new HtmlDomComment($comment));
        $this->innerText .= '{comment_content}';
    }

    public function setAttribute($name, $value, $attrWrapperChar = false, $override = true) {
        if (empty($name)) {
            return;
        }

        if ($this->isVoid) {
            switch ($this->tagName) {
            case 'html':
                $this->root->htmlNode->setAttribute($name, $value, $attrWrapperChar, $override);
                return;
            case 'head':
                $this->root->headNode->setAttribute($name, $value, $attrWrapperChar, $override);
                return;
            case 'body':
                $this->root->bodyNode->setAttribute($name, $value, $attrWrapperChar, $override);
                return;
            }
        }

        if ($attrWrapperChar === false) {
            if (strpos($value, '"') === false) {
                $attrWrapperChar = '"';
            } else if (strpos($value, "'") === false) {
                $attrWrapperChar = "'";
            } else {
                $attrWrapperChar = '';
            }
        }

        $name = strtolower($name);
        $attr = $this->getAttribute($name);
        if (!$attr || $override) {
            if ($attr) {
                $this->attributes->detach($attr);
            }

            $attr = new DomNodeAttribute($name, $value, $attrWrapperChar);
            $this->attributes->attach($attr);
        }
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getComments() {
        return $this->comments;
    }

    public function getAttribute($name) {
        foreach ($this->attributes as $attr) {
            if ($attr->name == $name) return $attr;
        }
        return NULL;
    }

    public function remove($node = null) {
        if (!$node) {
            $this->parent->remove($this);
        } else {
            foreach ($this->children as $k=>$child) {
                if ($node == $child) {
                    $text_parts = explode('{child_node_content}', $this->innerText);
                    $combined_parts = array_splice($text_parts, $k, 2);
                    array_splice($text_parts, $k, 0, implode('', $combined_parts));
                    $this->innerText = implode('{child_node_content}', $text_parts);
                    break;
                }
            }
            $this->children->detach($node);
            $node->detached = true;
            $node->parent = NULL;
            $node->root = NULL;
        }
    }

    public function appendChild(&$node) {
        $this->children->attach($node);
        $node->parent = $this;
        $node->root = $this->root;
        $this->innerText .= '{child_node_content}';
        $node->detached = false;
    }

    public function after(&$node) {
        //TODO: fix for self-closing tags
        $detached_nodes = new SplObjectStorage();
        $start_detaching = false;
        foreach ($this->parent->children as $child) {
            if ($start_detaching) {
                $detached_nodes->attach($child);
                $child->remove();
            }
            if ($child == $this) $start_detaching = true;
        }

        $this->parent->appendChild($node);

        foreach ($detached_nodes as $node) {
            $this->parent->appendChild($node);
        }
    }

    public function before(&$node) {
        //TODO: fix for self-closing tags
        $detached_nodes = new SplObjectStorage();
        $start_detaching = false;
        foreach ($this->parent->children as $child) {
            if ($child == $this) $start_detaching = true;
            if ($start_detaching) {
                $detached_nodes->attach($child);
                $child->remove();
            }
        }

        $this->parent->appendChild($node);

        foreach ($detached_nodes as $node) {
            $this->parent->appendChild($node);
        }
    }

    public function html($html) {
        foreach ($this->children as $child) {
            $child->remove();
        }

        $this->parseDom(new StringIterator($html));
    }

    public function find($selector, &$matches = null, &$selectorObject = null, $innerCall = false) {
        if (!$matches) {
            $matches = new SplObjectStorage();
        }

        if (empty($selector)) return $matches;

        if (!$selectorObject) {
            $selectorObject = new HtmlDomSelector($selector);
        }

        if (!$this->isVoid && $selectorObject->test($this)) {
            $matches->attach($this);
        }

        foreach ($this->children as $child) {
            $child->find($selector, $matches, $selectorObject, true);
        }

        if (!$innerCall) {
            if ($matches->count() == 1) {
                $matches->rewind();
                return $matches->current();
            }

            return $matches;
        }
    }

    public function isSelfClosing() {
        return in_array($this->tagName, self::$self_closing_tags);
    }

    public function getInnerText() {
        $texts = explode('{child_node_content}', str_replace('{comment_content}', '', $this->innerText));
        foreach ($this->children as $k => $child) {
            if (isset($texts[$k])) {
                $texts[$k] .= $child->getInnerText();
            }
        }

        return implode('', $texts);
    }

    public function dumpDom($lvl = 0) {
        foreach ($this->children as $child) {
            $tagHeader = str_repeat('| ', $lvl/2) . $child->tagName;

            foreach ($child->getAttributes() as $attr) {
                if ($attr->value !== false) {
                    $tagHeader .= ' ' . $attr->name . '=' . $attr->wrapperChar . $attr->value . $attr->wrapperChar;
                } else {
                    $tagHeader .= ' ' . $attr->name;
                }
            }

            if (!$child->isVoid) {
                echo $tagHeader . "\n";
            } else {
                echo $tagHeader . " (VOID)\n";
            }

            $child->dumpDom($lvl+2);
        }
    }

    public function getHtml($include_comments = false, $minify_level = 0) {
        if (!empty($this->tagName) && !$this->isVoid) {
            $tagHeader = '<' . $this->tagName;
            foreach ($this->attributes as $attr) {
                if ($attr->value !== false) {
                    $tagHeader .= ' ' . $attr->name . '=' . $attr->wrapperChar . $attr->value . $attr->wrapperChar;
                } else {
                    $tagHeader .= ' ' . $attr->name;
                }
            }
            if ($this->isSelfClosing()) return $tagHeader . '/>';

            $tagHeader .= '>';
        }

        $tmp_html = $this->innerText;

        if ($include_comments) {
            $texts = explode('{comment_content}', $tmp_html);
            foreach ($this->comments as $k => $comment) {
                if (isset($texts[$k])) {
                    $texts[$k] .= $comment->text;
                }
            }
            $tmp_html = implode('', $texts);
        } else {
            $tmp_html = str_replace('{comment_content}', '', $tmp_html);
        }


        if ($minify_level && !in_array($this->tagName, array("script", "style", "pre"))) {
            $tmp_html = str_replace(array("\n", "\r", "\t", "\f"), " ", $tmp_html);
            $tmp_html = preg_replace("/\s+/", " ", $tmp_html);
        }

        $texts = explode('{child_node_content}', $tmp_html);
        foreach ($this->children as $k => $child) {
            if (isset($texts[$k])) {
                $texts[$k] .= $child->getHtml($include_comments, $minify_level);
            }
        }

        $tmp_html = implode('', $texts);

        if ($minify_level === 2 && !in_array($this->tagName, array("script", "style", "pre"))) {
            $tmp_html = preg_replace('/([^\s])\s+$/', '$1', $tmp_html);
        }

        if (!empty($this->tagName)) {
            if (!$this->isVoid) {
                return $tagHeader . $tmp_html . '</' . $this->tagName . '>';
            } else {
                return $tmp_html;
            }
        } else {
            return $this->root->doctype . $tmp_html;
        }
    }

    public function parseDom(&$iterator) {
        if ($this->isSelfClosing()) return;

        $inTag = false;
        $inTagHeader = false;
        $tagNameRead = false;

        $tagName = '';
        $buffer = '';
        $attributeName = '';
        $readingAttrValue = false;
        $attrValueWrapperChar = '';
        $inComment = false;

        $nextNode = null;

        $inScriptString = false;
        $inScriptComment = false;
        $inScriptRegex = false;
        $inSpecialScriptContext = false;
        $scriptQuoteChar = '';
        $scriptCommentType = 'oneline';

        $this->containingElement = $this;

        if ($iterator->key() > 0) {
            $iterator->next();//Move to the next character because foreach does not call next() when starting iteration and the the node will not be able to read correctly. It will read the last > from the tag.
        } 

        foreach ($iterator as $char) {
            $nextChar = $iterator->peek();
            if ($this->tagName != 'script' && $this->tagName != 'style') {
                if ($char == '<' && ($nextChar == '!' || $nextChar == '?')) {//This may look like we are going to miss some precious chars, but take a look at the last line of this foreach block :) All should be good
                    if ($nextChar == '!' && $iterator->peek(3) == "!--") {
                        $this->parseComment($iterator);
                        continue;
                    } else if (strtolower($iterator->peek(4)) == '!doc' || strtolower($iterator->peek(4) == '?xml')) {
                        foreach ($iterator as $subchar) {//read untill the closing >
                            //$this->innerText .= $subchar;//Don't touch this!
                            $this->root->doctype .= $subchar;//Don't touch this!
                            if ($subchar == '>') break;
                        }
                        $buffer = '';
                        continue;
                    } else if ($iterator->peek(8) == "![CDATA[") {
                        $this->parseCdata($iterator);
                        continue;
                    }
                }
            }

            if ($this->tagName == 'script' || $this->tagName == 'style') {
                if ($char == '<') {
                    if ($this->tagName == 'script' && strtolower($iterator->peek(7)) == '/script' && !$inScriptString) {
                        //TODO: maybe an $iterator->consume statement will increase things up a bit
                        foreach ($iterator as $subchar) {//read untill the closing > to handle cases like </script wtf>
                            if ($subchar == '>') return;
                        }
                    }

                    if ($this->tagName == 'style' && strtolower($iterator->peek(6)) == '/style') {
                        //TODO: maybe an $iterator->consume statement will increase things up a bit
                        foreach ($iterator as $subchar) {//read untill the closing > to handle cases like </style wtf>
                            if ($subchar == '>') return;
                        }
                    }
                } else if ($this->tagName == 'script') {
                    if (($char == '"' || $char == "'")) {
                        if (!$inScriptString && !$inScriptComment && !$inScriptRegex) {
                            $inScriptString = true;
                            $scriptQuoteChar = $char;
                        } else if ($char == $scriptQuoteChar) {
                            $inScriptString = false;
                            $scriptQuoteChar = '';
                        }
                    } else if ($char == '\\' && $inScriptString) {
                        if ($iterator->peek() == $scriptQuoteChar) {
                            $char .= $scriptQuoteChar;// Append to the char so this can be included in the innerText
                            $iterator->consume(1);
                        }
                    } else if ($char == '/' && !$inScriptString) {
                        $nextChar = $iterator->peek();
                        switch ($nextChar) {
                            case '/':
                                $char .= '/';// Append to the char so this can be included in the innerText
                                $iterator->consume(1);
                                $inScriptComment = true;
                                $scriptCommentType = 'oneline';
                                break;
                            case '*':
                                $char .= '*';// Append to the char so this can be included in the innerText
                                $iterator->consume(1);
                                $inScriptComment = true;
                                $scriptCommentType = 'multiline';
                                break;
                            default:
                                if ($inScriptRegex) {
                                    $inScriptRegex = false;
                                } else if ($inSpecialScriptContext) {
                                    $inScriptRegex = true;
                                }
                        }
                    } else if ($char == "\n" && $inScriptComment && $scriptCommentType == 'oneline') {
                        $inScriptComment = false;
                    } else if ($char == "*" && $inScriptComment && $scriptCommentType == 'multiline') {
                        if ($iterator->peek() == '/') {
                            $inScriptComment = false;
                        }
                    }

                    if ($char == '(' || $char == '=' || $char == ',' || $char == ';') {
                        $inSpecialScriptContext = true;
                    } else if (!$this->isSpaceChar($char)) {
                        $inSpecialScriptContext = false;
                    }
                }
                $this->innerText .= $char;
                continue;
            }

            if ($char == '<' && $iterator->peek() == '!') {
                if ($iterator->peek(3) == "!--") {
                    $this->parseComment($iterator);
                    continue;
                } else if ($iterator->peek(8) == "![CDATA[") {
                    $this->parseCdata($iterator);
                }
            }

            $buffer .= $char;

            $nextChar = $iterator->peek();
            if ($char == '<' && !$inTagHeader && $nextChar != ' ' && $nextChar != '<') {
                $inTagHeader = true;
                $tagNameRead = false;
                $tagName = '';
                $buffer = '';
                continue;
            }

            if ($inTagHeader) {
                if (!$tagNameRead && ($this->isSpaceChar($char) || $char == '>')) {
                    $tagName = rtrim($buffer, $char);

                    if (strtolower($tagName) == '/' . $this->tagName) {//the closing part of the current tag has been found
                        if ($char != '>') {
                            foreach ($iterator as $subchar) {//read untill the closing > to handle cases like </span wtf>
                                if ($subchar == '>') break;
                            }
                        }
                        return;
                    }

                    if ($tagName[0] == "/") {
                        $parent = $this->parent;
                        $tagNameLower = strtolower(substr($tagName, 1));
                        while ($parent) {
                            if ($tagNameLower == $parent->tagName) {
                                $iterator->consume(-(strlen($tagName) + 2));//This way when we return to the parseDom of the parent we will be able to read it's closing tag properly
                                return;
                            }
                            $parent = $parent->parent;
                        }

                        if ($char != '>') {
                            foreach ($iterator as $subchar) {//read untill the closing > to handle cases like </span wtf>
                                if ($subchar == '>') break;
                            }
                        }

                        $inTagHeader = false;
                        $tagNameRead = false;
                        $tagName = '';
                        $buffer = '';
                        continue;
                    }

                    if (empty($tagName) || $tagName[0] == '/' || is_numeric($tagName)) {
                        $this->innerText .= '<' . $buffer;
                        $inTagHeader = false;
                        $nextNode = null;
                        $tagName = '';
                        $tagNameRead = false;
                        $buffer = '';
                        continue;
                    } else {
                        $tagNameRead = true;
                        $tagNameTrimmed = strtolower(trim($tagName, '/'));//trim is to handle tags like this <br/>, because in this case the detected tag will be br/ not br

                        $nextNode = new HtmlDomNode($tagNameTrimmed, $this, $this->root);

                        switch ($tagNameTrimmed) {
                        case 'html':
                            if (!$this->root->bodyNode->detached) {//This may need to check the htmlNode object instead of body - so feel free to experiment if needed
                                $nextNode->isVoid = true;
                            } else {
                                $this->containingElement = $this->root;
                                $nextNode = $this->root->htmlNode;
                            }
                            break;
                        case 'head':
                            if (!$this->root->bodyNode->detached) {//This may need to check the headNode object instead of body - so feel free to experiment if needed
                                $nextNode->isVoid = true;
                            } else {
                                $this->containingElement = $this->root->htmlNode;
                                $nextNode = $this->root->headNode;

                                if ($this->root->htmlNode->detached) {
                                    $this->root->appendChild($this->root->htmlNode);
                                }
                            }
                            break;
                        case 'body':
                            if (!$this->root->bodyNode->detached) {
                                $nextNode->isVoid = true;
                            } else {
                                $this->containingElement = $this->root->htmlNode;
                                $nextNode = $this->root->bodyNode;

                                if ($this->root->htmlNode->detached) {
                                    $this->root->appendChild($this->root->htmlNode);
                                }

                                if ($this->root->headNode->detached) {
                                    $this->root->htmlNode->appendChild($this->root->headNode);
                                }
                            }
                            break;
                        default:
                            if ($this->root->bodyNode->detached) {
                                if (in_array($tagNameTrimmed, self::$head_tags)) {
                                    $this->containingElement = $this->root->headNode;

                                    if ($this->root->htmlNode->detached) {
                                        $this->root->appendChild($this->root->htmlNode);
                                    }

                                    if ($this->root->headNode->detached) {
                                        $this->root->htmlNode->appendChild($this->root->headNode);
                                    }
                                } else {
                                    $this->containingElement = $this->root->bodyNode;

                                    if ($this->root->htmlNode->detached) {
                                        $this->root->appendChild($this->root->htmlNode);
                                    }

                                    if ($this->root->headNode->detached) {
                                        $this->root->htmlNode->appendChild($this->root->headNode);
                                    }

                                    if ($this->root->bodyNode->detached) {
                                        $this->root->htmlNode->appendChild($this->root->bodyNode);
                                    }
                                }
                            }
                            break;
                        }

                        $readingAttrValue = false;
                    }
                    $buffer = '';
                }

                if ($char == '>' && (!$readingAttrValue || $attrValueWrapperChar == '')) {
                    if ($nextNode) {
                        if ($attrValueWrapperChar == '') {
                            if ($readingAttrValue) {
                                $attrValue = rtrim($buffer, $char);
                                if ($nextNode->isSelfClosing() && substr($attrValue, -1) == '/') {
                                    $attrValue = substr($attrValue, 0, -1);
                                }
                                $nextNode->setAttribute($attributeName, $attrValue, $attrValueWrapperChar, false);
                                $readingAttrValue = false;
                                $attrValueWrapperChar = '';
                            } else {
                                $attributeName = trim($buffer, " >");
                                if ($attributeName && $attributeName != '/') {
                                    $nextNode->setAttribute($attributeName, false, $attrValueWrapperChar, false);
                                }
                            }
                            $buffer = '';
                        }

                        while ($nextNode) {
                            if (isset(self::$optional_closing_tags_map[$this->tagName]) && in_array($nextNode->tagName, self::$optional_closing_tags_map[$this->tagName])) return $nextNode;

                            if ($nextNode->detached) {
                                $this->containingElement->appendChild($nextNode);
                            }

                            $nextNode = $nextNode->parseDom($iterator);
                        }
                    }

                    $inTagHeader = false;
                    $nextNode = null;
                    $tagName = '';
                    $tagNameRead = false;
                    continue;
                }

                if ($tagNameRead) {
                    $nextChar = $iterator->peek();
                    if (($nextChar == '=' || $this->isSpaceChar($nextChar)) && !$readingAttrValue) {
                        $attributeName = trim($buffer);
                        $readingAttrValue = true;
                        $attrValueWrapperChar = '';
                        $iterator->consume(1);//consume the next "=" or space char so we can start reading the value
                        $buffer = '';
                        $passedThroughEquals = $nextChar == '=';

                        $x = 1;
                        while(null !== ($str = $iterator->peek($x))) {
                            $trimmed_str = trim($str);
                            if ($trimmed_str !== '') {
                                if ($trimmed_str == "'" || $trimmed_str == '"') {
                                    $iterator->consume(max($x-1, 0));//consume the spaces before ['"] but leave the quotes, so we can properly detect them on the next run
                                } else if ($trimmed_str == '=') {
                                    $passedThroughEquals = true;
                                    $iterator->consume($x);
                                    $x=1;
                                    continue;
                                } else {
                                    if (!$passedThroughEquals) {
                                        $iterator->consume(max($x-2, 0));//if there were spaces after the = and the next non space char and this char is not ['"] then the attribute does not have a value and this is probably another attribute
                                        $nextNode->setAttribute($attributeName, false, '', false);
                                        $readingAttrValue = false;
                                        $attrValueWrapperChar = '';
                                    }
                                }
                                break;
                            }
                            $x++;
                        }
                        continue;
                    }

                    if ($readingAttrValue) {
                        if ($char == '\'' || $char == '"') {
                            if ($buffer == $char && !$attrValueWrapperChar) {
                                $attrValueWrapperChar = $char;
                                $buffer = '';
                            } else if($attrValueWrapperChar == $char) {
                                $nextNode->setAttribute($attributeName, rtrim($buffer, $char), $attrValueWrapperChar, false);
                                $readingAttrValue = false;
                                $attrValueWrapperChar = '';
                                $buffer = '';
                            }
                        }

                        if ($this->isSpaceChar($char) && $attrValueWrapperChar == '') {
                            $nextNode->setAttribute($attributeName, rtrim($buffer, $char), $attrValueWrapperChar, false);
                            $readingAttrValue = false;
                            $buffer = '';
                        }
                    }
                }
            }

            if (!$inTagHeader) $this->innerText .= $char;
        }
    }

    private function parseComment($iterator) {
        $comment = '';
        foreach ($iterator as $subchar) {
            $comment .= $subchar;
            if ($subchar == '-'){
                $isCommentEnd = false;

                if ($iterator->peek(2) == '->') {
                    $iterator->consume(2);
                    $isCommentEnd = true;
                } else if ($iterator->peek(3) == '-!>') {
                    $iterator->consume(3);
                    $isCommentEnd = true;
                }

                if ($isCommentEnd) {
                    $comment .= '->';
                    $this->containingElement->addComment($comment);
                    break;
                }
            }
        }
    }

    private function parseCdata($iterator) {
        $cdata = '';
        foreach ($iterator as $subchar) {
            $cdata .= $subchar;
            if ($subchar == ']'){
                $isCdataEnd = false;

                if ($iterator->peek(2) == ']>') {
                    $iterator->consume(2);
                    $isCdataEnd = true;
                }

                if ($isCdataEnd) {
                    $cdata .= ']>';
                    $this->innerText .= $cdata;
                    break;
                }
            }
        }
    }

    private function isSpaceChar($char) {
        return $char == ' ' || $char == "\t" || $char == "\r" || $char == "\n" || $char == "\f";
    }
}
