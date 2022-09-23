<?php

class Attr {
    public function __construct(string $name, string $value) {
        $this->name = $name;
        $this->value = str_replace('"', '&quot;', $value);
    }

    public function print() {
        switch($this->name) {
        case 'checked':
            print(' checked');
            break;
        case 'selected':
            print(' selected');
            break;
        default:
            print(' ');
            print($this->name);
            print('="');
            print($this->value);
            print('"');
        }
    }
}

function attr($k, $v) {
    return new Attr($k, $v);
}

class Element {
    public function __construct($body) {
        $this->body = $body;
    }
    public function print() {
        print($this->body);
    }
}

class Entitied extends Element {
    public function __construct($body) {
        $this->body = $body;
    }
    public function print() {
        print(htmlentities($this->body));
    }
}

class Tag extends Element {

    public function __construct(string $tag, array $attrs=[], $children=[]) {
        $this->tag = $tag;
        $this->attrs = $attrs;
        if(is_array($children)) {
            $this->children = $children;
        } else {
            $this->children = [$children];     
        }
    }

    public function add_child(Element $child) {
        $this->children[] = $child;
    }

    public function add_children(array $more_children) {
        $this->children = array_merge($this->children, $more_children);
    }
    
    public function print_open() {
        print("<{$this->tag}");
        foreach($this->attrs as &$attr) {
            $attr->print();
        }
        print(">");
    }

    public function print_children() {
        foreach($this->children as &$c) {
            $c->print();
        }
    }

    public function print_close() {
        print("</{$this->tag}>\n");
    }

    public function print() {
        $this->print_open();
        $this->print_children();
        switch($this->tag) {
            case 'link';
                break;
            default:
                $this->print_close();
        }

    }
}

class TagNoBody extends Tag {

    public function __construct(string $tag, array $attrs=[], array $children=[]) {
        $this->tag = $tag;
        $this->attrs = $attrs;
        $this->children = $children;
    }

    public function print_open() {
        print("<{$this->tag}");
        foreach($this->attrs as &$attr) {
            $attr->print();
        }
        print(" />");
    }
}

function text($txt) {
    return new Entitied($txt);
}

function doctype() {
    print("<!DOCTYPE html>\n");
}

function html(array $attrs=[], array $children=[]) {
    $haslang = false;
    foreach($attrs as $attr) {
        if($attr->name == 'lang') {
            $haslang = true;
            break;
        }
    }
    if(!$haslang) {
        $attrs[] = attr('lang', 'en');
    }
    return new Tag('html', $attrs, $children);
}

function css(string $href, string $rel="stylesheet", string $media="", string $title="") {
    return new Tag('link', [attr('rel', $rel), attr('href', $href), attr('media', $media), attr('title', $title)]);
}

function head(array $attrs=[], array $children=[]) {
    return new Tag('head', $attrs, $children);
}

function jscript(string $src) {
    return new Tag('script', [attr('src', $src)], []);
}

function jmodule(string $src) {
    return new Tag('script', [attr('type', 'module'), attr('src', $src)], []);
}

function body(array $attrs=[], array $children=[]) {
    return new Tag('body', $attrs, $children);
}

function h1(array $attrs=[], array $children=[]) {
    return new Tag('h1', $attrs, $children);
}

function table(array $attrs=[], array $children=[]) {
    return new Tag('table', $attrs, $children);
}

function thead(array $attrs=[], array $children=[]) {
    return new Tag('thead', $attrs, $children);
}

function tbody(array $attrs=[], array $children=[]) {
    return new Tag('tbody', $attrs, $children);
}

function tr(array $attrs=[], array $children=[]) {
    return new Tag('tr', $attrs, $children);
}

function th($contents, array $attrs=[]) {
    if(!is_object($contents)) {
        $contents = new Entitied($contents);
    }
    return new Tag('th', $attrs, $contents);
}

function td($contents, array $attrs=[]) {
    if(!is_object($contents)) {
        $contents = new Entitied($contents);
    }
    return new Tag('td', $attrs, $contents);    
}

function div(array $attrs=[], array $children=[]) {
    return new Tag('div', $attrs, $children);
}

function a(string $href, array $attrs=[], array $children=[]) {
    return new Tag('a', $attrs, $children);
}

function img(string $src, string $alt, array $attrs=[], array $children=[]) {
    $attrs[] = attr('src', $src);
    $attrs[] = attr('alt', $alt);
    return new Tag('img', $attrs, $children);
}

function nbsp() {
    return new Element('&nbsp;');
}

function iframe(string $src, array $attrs) {
    $attrs[] = attr('src', $src);
    return new Tag('iframe', $attrs);
}

?>