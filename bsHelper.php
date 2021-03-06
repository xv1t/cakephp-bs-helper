<?php

/**
 * Description of bsHelper
 *
 * @author xv1t
 */

App::uses('Helper', 'View');

class bsHelper extends AppHelper {
    var $helpers = array(
        'Html',
        'Form',
        'Paginator'
    );
    
    public $schema = [];

    
    var $components = [
        "panel" => [
            "contextual" => [
                "list"    => ["default", "primary", "success", "info", "warning", "danger"],
                "require" => ["default", "primary", "success", "info", "warning", "danger"],
                "default" => "default"
            ],
            "classes" => [],
            "tags" => [
                "list" => ["div"],
                "default" => "div"
            ],
            "content" => [
                "heading",
                "body",
                "footer"
            ]
        ],  
        "btn" => [
            "contextual" => [
                "list"    => ["default", "primary", "success", "info", "warning", "danger", "link", "lg", "sm", "xs"], 
                "require" => ["default", "primary", "success", "info", "warning", "danger", "link"],
                "default" => "default"
                ],
            "classes" => ["active", "disabled", "navbar-btn", "pull-right"],
            "tags" => [
                "list"    => ["button", "a", "input"],
                "default" => "button"
                ]
        ], 
        "btn-group" => [
            "contextual" => [
                "list" => ["vertical", "justified", "lg", "sm", "xs"],
                "require" => [],
                "default" => ""
            ],
            "classes" => ["pull-right", "pull-left"],
            "tags" => [
                "list" => ["div"],
                "default" => "div"
            ]
        ], 
        "btn-toolbar" => [
            "contextual" => [
                "list" => [],
                "require" => [],
                "default" => ""
            ],
            "classes" => ["pull-right", "pull-left"],
            "tags" => [
                "list" => ["div"],
                "default" => "div"
            ]
        ], 
        "pagination", 
        "form" => [
            "contextual" => [
                "list" => ["inline", "horizontal"]
            ],
            "tags" => [
                "list" => ["form"],
                "default" => "form"
            ]
        ],
        "label" => [
            "contextual" => [
                "list"    => ["default", "primary", "success", "info", "warning", "danger"],
                "require" => ["default", "primary", "success", "info", "warning", "danger"],
                "default" => "default"
            ],
            "classes" => [],
            "tags" => [
                "list" => ["span"],
                "default" => "span"
            ]
        ], 
        "alert" => [
            "contextual" => [
                "list" => ["success", "info", "warning", "danger"],
                "require" => ["success", "info", "warning", "danger"],
                "default" => "info"
            ],
            "classes" => [],
            "tags" => [
                "list" => ["div"],
                "default" => "div"
            ]
        ], 
        "table" => [
            "contextual" => [
                "list" => ["striped", "bordered", "hover", "condensed", 'responsive'],
                "require" => [],
                "default" => ""
            ],
            "classes" => ["table"],
            "tags" => [
                "list" => "table",
                "default" => "table"
            ],
            'icons' => [
                'boolean' => [
                    "true"  => "fa-check-square-o",
                    "false" => "fa-square-o"
                ]
            ]
        ],
        "progress-bar" => [
            "contextual" => [
                "list" => ["default", "success", 'info', 'warning', 'danger', "striped"],
                "require" => ["default"],
                "default" => "default"
            ],
            "classes" => ["active"],
            "tags" => [
                "list" => ["div"],
                "default" => "div"
            ]
        ], 
        "list-group-item", 
        "navbar" => [
            "contextual" => [
                "list" => ["default", "inverse", "fixed-bottom", "fixed-top", "static-top"],
                "require" => ["default", "inverse", ],
                "default" => "default"
            ],
            "classes" => [],
            "tags" => [
                "list" => ["div"],
                "default" => "div"
            ]            
        ]
        
    ];
    
    public function el($component, $opts = [], $content = null){

        if ( is_string($opts) && empty($content)){
            $opts = [$opts];
        }
        
        if (empty($content) && !empty($opts['content'])){
            $content = $opts['content'];
            unset($opts['content']);
        }
        
        extract( $this->parse_options($opts) );
        extract( $this->extract_contextual(
            $arguments, 
            $this->components[$component]["contextual"]["list"],
            $this->components[$component]["classes"],
            $this->components[$component]["tags"]["list"]
        ) );
        
        /*
         * Check default
         */
        
        if (!empty($this->components[$component]["contextual"]["require"])
                && !empty($this->components[$component]["contextual"]["default"])
                )
        {   
            $check_default = array_intersect($contextual, $this->components[$component]["contextual"]["require"]);

            if (empty($check_default)){
                $contextual[] = $this->components[$component]["contextual"]["default"];
            }
        }
        
        $contextual_classes = $this->generate_contextual_classes($component, $contextual);        
        $result_classes = array_merge($contextual_classes, $classes);
        
        if (empty($tags)){
            $tags = [ $this->components[$component]["tags"]["default"] ];
        }
        
        if ($content){
            
            if ($content && is_array($content)){
                $content = join($content);
            }
            
            $string[] = $content;
        } else {
            if ($strings){
                $content = $strings[0];
            }
        }
        
        $tagName = $tags[0];
        if (empty($options['class'])){
            $options['class'] = $result_classes;
        } else {
            if (is_array($options['class'])){
                $options['class'] = array_merge($result_classes, $options['class']);
            } 
            if (is_string($options['class'])){
                $result_classes[]=$options['class'];
                $options['class'] = $result_classes;
            } 
                           
        }
        
        /*
         * Custom options
         */
        switch ($component) {
            case "btn":
                if ($tagName == "a"){
                    $options['role'] = "button";
                }
                
                if (empty($options['type'])){
                    $options['type'] = "button";
                }
                
                if ($tagName == "input"){
                    $options['type'] = "submit";
                    $options['role'] = "button";  
                    $options['value'] = $content;
                    $content = null;
                    
                }

                break;
            case "btn-group":
                $options['role'] = "group";
                break;
            case "panel": 
                /*
                 * include elements
                 */
                $heading = $title = $body_after = $body = $body_before = $footer = null;
                /*
                 * Heading
                 */
                if (!empty( $options[ "heading" ])){
                    
                    $title_options = [
                        "class" => "panel-title"
                    ];
                    
                    if (is_string( $options[ "heading" ] ) && !$this->str_with_tags($heading) ){
                        $title = $options[ "heading" ];
                    }
                    
                    if (                       isset($options["heading"]['title']) 
                            &&             is_string($options["heading"]['title']) 
                            && !$this->str_with_tags($options["heading"]['title']) ){                        
                        
                        $title = $this->tag("h3",
                                $options["heading"]['title'] . "",
                                $title_options);
                    }
                    $toolbar = null;
                    
                    
                    
                    if (!empty($options['heading']['toolbar'])){
                        
                        $toolbar_content = $this->content($options['heading']['toolbar']);
                        $toolbar_size = "margin-top: 2px; margin-right: 3px;";
                        
                        if (isset($options['heading']['toolbar_size'])){
                            $toolbar_size = $options['heading']['toolbar_size'];
                        } else {
                            
                            if (strpos($toolbar_content, "-xs") !== false){
                                $toolbar_size = "margin-top: 7px; margin-right: 7px;";
                            }
                            
                            if (strpos($toolbar_content, "-sm") !== false){
                                $toolbar_size = "margin-top: 4px; margin-right: 4px;";
                            }
                        }
                        
                        $toolbar = $this->btn_toolbar(['pull-right',
                            "style" => $toolbar_size
                        ], $toolbar_content);
                    }
                    
                    $heading =$toolbar .  $this->tag("div", $title, [
                        "class" => "panel-heading",
                        
                    ]);
                    
                    
                    unset($options['heading']);
                }
                
                if (isset($options['body'])){
                    if (is_string($options['body'])){
                        $body = $options['body'];
                    }
                    
                    $body_options = [
                        "class" => "panel-body"
                    ];
                    $body = $this->tag("div", $body, $body_options);
                    
                    unset($options['body']);
                }
                
                if (isset($options['footer'])){
                    if (is_string($options['footer'])){
                        $footer = $options['footer'];
                    }
                    
                    $footer_options = [
                        "class" => "panel-footer"
                    ];
                    $footer = $this->tag("div", $footer, $footer_options);
                    
                    
                    unset($options['footer']);
                }
                if (isset($options['body_after'])){
                    
                    $body_after = $options['body_after'];
                    
                    unset($options['body_after']);
                }
                
                if (isset($options['body_before'])){
                    
                    $body_before = $options['body_before'];
                    
                    unset($options['body_before']);
                }
                
                $content = join([$heading, $body_before, $body, $body_after, $footer]);
                break;
                
            case "navbar":
                
                $collapse_uid = uniqid();
                
                    $conainer_fluid =
                        $navbar_header = 
                        $navbar_brand = 
                        $navbar_brand_image = 
                        $navbar_toggle =
                    $navbar_collapse = '';
                
                $content = '';
                
                if (isset($options['header'])){
                    
                    if (!empty($options['header']['toggle'])){
                        $navbar_toggle = $this->tag('button', [
                            $this->span("Toggle navigation", ["class" => "sr-only"]),
                            $this->span("", ["class" => "icon-bar"]),
                            $this->span("", ["class" => "icon-bar"]),
                            $this->span("", ["class" => "icon-bar"]),
                        ], [
                            'class' => 'navbar-toggle collapsed',
                            'type'  => "button",                            
                            'data-toggle' =>"collapse", 
                            'data-target' => "#" .$collapse_uid,
                            'aria-expanded' => "false",
                        ]);
                    }
                    
                    if (!empty($options['header']['image'])){                        
                        $navbar_brand_image = $this->tag('a', $options['header']['image'], [
                            'class' => 'navbar-brand',
                            'href' => '#'
                        ]);                                                           
                    }
                    
                    if (!empty($options['header']['brand'])){
                        $navbar_brand = $this->tag('a', $options['header']['brand'], [
                            'class' => 'navbar-brand',
                            'href' => '#'
                        ]);
                    }
                    
                    unset($options['header']);
                }
                
                if (isset($options['collapse']) && is_array( $options['collapse'] )){
                    foreach ( $options['collapse'] as $ix => $elem ){
                        
                        if (is_int($ix) && is_string($elem)) {
                            //Custom string code
                            $navbar_collapse .= $elem;
                            continue;
                        }
                        
                        if (is_int($ix) && is_array($elem)) {
                            switch ($elem['type']) {
                                case 'text':
                                    $content = $elem["content"];
                                    unset($elem['content']);

                                    $elem = $this->addClass($elem, "navbar-text");

                                    $navbar_collapse .= $this->tag("p", $content, $elem);
                                    break;

                                case 'button':
                                    $navbar_collapse .= $this->btn( array_merge($elem, ["navbar-btn"]));
                                    break;

                                case 'nav':
                                    $nav_content = '';
                                    foreach ($elem['items'] as $item_arg0 => $item_arg1){

                                        $active = $disabled = false;

                                        //custom html code
                                        if (is_int($item_arg0) && is_string($item_arg1)){
                                            $nav_content .= $this->tag('li',  $item_arg1);
                                            continue; //next item
                                        }

                                        if (is_string($item_arg1)){
                                            $item_arg1 = [
                                                'href' => $item_arg1];
                                        }

                                        if ( is_array($item_arg1 )){
                                            $active = in_array("active", $item_arg1);
                                            $disabled = in_array("disabled", $item_arg1);
                                        }

                                        if ( !empty($item_arg1['children']) ){
                                            //dropdown
                                            $item_children_content = '';
                                            foreach ( $item_arg1['children'] as $child_arg1 => $child_arg2 ){

                                                if ($child_arg2 == '-'){
                                                   $item_children_content .= $this->li('', [
                                                       'role' => 'separator',
                                                       'class' => 'divider'
                                                   ]);
                                                } else {     
                                                    $child_disabled = false;
                                                    if (is_array($child_arg2)){
                                                        $child_disabled = in_array('disabled', $child_arg2);
                                                    }

                                                    if (is_string($child_arg2)){
                                                        $child_arg2 = [
                                                            'href' => $child_arg2];
                                                    }
                                                    
                                                    if (!isset($child_arg2['href'])){
                                                        $child_arg2['href'] = '#';
                                                    }

                                                    $item_children_content .= $this->li( $this->a(
                                                            $child_arg1, 
                                                            $child_arg2), [
                                                        "class" =>
                                                            $child_disabled ? 'disabled' : null,
                                                        ]);

                                                }
                                            }
                                            $nav_content .= $this->li([
                                                $this->a(  $item_arg0 .' <span class="caret">', [
                                                    'href' =>"#" ,
                                                    'class' => 'dropdown-toggle',
                                                    'data-toggle'=>"dropdown" ,
                                                    'role'=>"button" ,
                                                    'aria-haspopup' => "true",
                                                ]),
                                                $this->ul( $item_children_content, [
                                                    "class" => 'dropdown-menu'
                                                ] )
                                            ], [
                                                'class' => [
                                                    'dropdown',
                                                    $disabled ? 'disabled' : null,
                                                    $active ? 'active' : null,
                                                    ]
                                            ]);

                                        } else {
                                            $nav_content .= $this->li( $this->a(
                                                $item_arg0, $item_arg1),
                                                    [
                                                        //li options
                                                        'class' => [
                                                            $disabled ? 'disabled' : null,
                                                            $active ? 'active' : null,
                                                        ]
                                                    ]);
                                        }
                                    }

                                    $navbar_collapse .= $this->ul($nav_content, [
                                        'class' => [
                                            'nav', 
                                            'navbar-nav',
                                            in_array('right', $elem) ? 'navbar-right' : null
                                            ]
                                    ] );

                                    break;

                                default:
                                    break;
                              }
                        }
                    }
                    
                    unset($options['collapse']);
                }
                
                //Change global body options to for main layout
                $bodyOptions = $this->_View->getVar('bodyOptions');
                if (empty($bodyOptions)){
                    $bodyOptions = ["class" => []];
                }
                
                if ( $this->hasClass($options, 'navbar-fixed-top') ){
                    $bodyOptions = $this->addClass($bodyOptions, 'body-navbar-fixed-top');
                }
                
                if ( $this->hasClass($options, 'navbar-fixed-bottom') ){
                    $bodyOptions = $this->addClass($bodyOptions, 'body-navbar-fixed-bottom');
                }
                
                $this->_View->set(compact('bodyOptions'));
                
                $content = $this->div([
                    $this->div([
                        $navbar_toggle,
                        $navbar_brand_image,
                        $navbar_brand
                    ], [
                        "class" => 'navbar-header'
                    ]), //navbar-headet
                    $this->div([
                        $navbar_collapse
                    ], [
                        'class' => 'collapse navbar-collapse',
                        'id' => $collapse_uid
                    ])
                ], [ 
                    'class' => 'container-fluid'
                ]);
                break;

            default:
                break;
        }       
        
        $options['escape'] = false;
        
        /*
         * icon
         */
        
        if (!empty($options['icon'])){
            if (!$this->str_with_tags($options['icon'] )){
                $icon_options = [];
                $icon_options['aria-hidden'] = "true";
                $icon_options['class'] = $options['icon'];
                $options['icon'] = $this->tag("i", '', $icon_options);
            } 
            
            $content = $options['icon'] . " " . $content;
            
            unset($options['icon']);
        }
        
        return $this->tag($tagName, $content, $options);
    }
    
    public function content($content){
        if (is_array($content)){
            return join($content);
        }
        
        if (is_string($content)){
            return $content;
        }
    }
    
    public function a($content = null, $options = []){
        return $this->tag('a', $content, $options);
    }
    
    public function ul($content = null, $options = []){
        return $this->tag( __FUNCTION__, $content, $options);
    }
    
    public function li($content = null, $options = []){
        return $this->tag('li', $content, $options);
    }
    
    public function div($content = null, $options = []){
        return $this->tag('div', $content, $options);
    }
    
    public function dl($array = [], $options = []){
        $content =  '';
        foreach ($array as $dt => $dd){
            if ($dd)
            {
                $content .= $this->tag('dt', $dt) . $this->tag('dd', $dd);            
            }
        }        
        return $this->tag('dl', $content, $options);
    }
    public function dl_horizontal($array = [], $options = []){        
        $options = $this->addClass($options, 'dl-horizontal');
        return $this->dl($array, $options);
    }
    
    public function span($content = null, $options = []){
        return $this->tag('span', $content, $options);
    }
    
    public function caret(){
        return $this->tag('span', '', [
            'class' => 'caret'
        ]);
    }
    
    public function btn_dropup(string $title, array $items = [], array $options = []){
        $options['dropup'] = true;
        return $this->btn_dropdown($title, $items, $options);
    }
    
    public function btn_dropdown(string $title, array $items = [], array $options = []){
        $btn_html = $items_html = $split_btn_html = '';
        list($options, $btn_options) = $this->extract_options($options, 'btn');
        list($options, $ul_options) = $this->extract_options($options, 'ul');
        
        if (empty($options['tagName'])){
            $options['tagName'] = 'div';
        }
        
        
        if (empty($btn_options['id'])){
            $btn_options['id'] = uniqid();
        }
        
        $ul_options = $this->addClass($ul_options, 'dropdown-menu');
        $ul_options['aria-labelledby'] = $btn_options['id'];
        
        if (isset($options['dropup']) && $options['dropup'] === true){
            $options = $this->addClass($options, 'dropup');
        } else {
            $options = $this->addClass($options, 'dropdown');
        }
        
        foreach ($items as $one => $two){
            $item_options = $li_options = [];
            
            if (is_array($two)){
                $item_options = $two;
            }
            
            if (is_string($one)){
                $item_options['header'] = $one;
            }
            
            if (is_string($two)){
                $item_options['href'] = $two;
            }
            
            list( $item_options, $item_header ) =
                    $this->extract_options($item_options, 'header');
            
            list( $item_options, $li_options ) =
                    $this->extract_options($item_options, 'li');
            
            if ($two === '-'){
                $item_header = '';
                $li_options['class'] = 'divider';
                $li_options['role'] = 'separator';
            }
            
            $items_html .= $this->li([
                $this->a($item_header, $item_options)
            ], $li_options);
            
        }
        
        list($options, $split) = $this->extract_options($options, 'split');
        
        if ($split === true){
            $options = $this->addClass($options, 'btn-group');
            
            $caret_btn_options = [
                    'data-toggle'=>"dropdown",
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'false',
                    'class' => empty( $btn_options['class']) ? [] : $btn_options['class']
                ];
            
            $caret_btn_options = $this->addClass($caret_btn_options, 'dropdown-toggle');
            
            return $this->tag($options['tagName'], [
                $this->btn($btn_options, $title),
                $this->btn($caret_btn_options,
                    $this->caret() . "\n" . $this->span('Toggle Dropdown', [
                        'class' => 'sr-only'
                    ])),
                $this->ul($items_html, $ul_options)
            ], $options);
            
        } else {      
            $btn_options['data-toggle']="dropdown";
            $btn_options['aria']['haspopup'] = 'true';
            $btn_options['aria']['expanded'] = 'false';            
            
            return $this->tag($options['tagName'], [
                $this->btn($btn_options, $title . "\n" . $this->caret()),
                $this->ul($items_html, $ul_options)
            ], $options);
        }
        
    }
    
    public function tag($tagName, $content = null, $options = []){
        $options['escape'] = false;
        
        if (is_array($content)){
            $content = join($content);
        }
        
        if (isset($options['data']) && is_array($options['data']) ){
            foreach ($options['data'] as $key => $value){
                if (empty($options[ 'data-' . $key  ]))
                $options[ 'data-' . $key ] = $value;
            }
            unset($options['data']);
        }
        
        if (isset($options['aria']) && is_array($options['aria']) ){
            foreach ($options['aria'] as $key => $value){
                if (empty('aria-' . $key))
                $options[ 'aria-' . $key ] = $value;
            }
            unset($options['aria']);
        }
        
        
        return $this->Html->tag($tagName, $content, $options);
    }
    
    public function btn_toolbar($options = [], $content = null){
        return $this->el("btn-toolbar", $options, $content);
        //$options = $this->addClass($options, "btn-toolbar") ;  
        //return $this->tag("div", $content, $options);
    }
    
    public function hasClass($options, $className){
        if (is_array($options) && empty($options['class'])){
            return false;
        }
        
        $classes_string = '';
        
        if (is_string($options)){
            $classes_string = $options;
        }        
        
        if (is_array($options['class']) ){
            $classes_string = ' ' . join(' ', $options['class']) . ' ';
        }
        
        if (is_string($options['class'])){
            $classes_string = ' ' . $options['class'] . ' ';
        }
        
        return strpos($classes_string, $className) !== false;
    }
    
    public function removeClass($options = [], $classes = null, $key = 'class'){
        if (empty($options[ $key ])){
            return $options;
        }
        
        if (empty($classes)){
            return $options;
        }
        
        if (is_string($classes)){
            $classes = $this->array_classes(['class' => $classes]);
        }
        
        if (empty($classes)){
            return $options;
        }
        
        $options_classess = $this->array_classes($options);
        
        foreach ($classes as $class){
            $pos = array_search($class, $options_classess);
            if ($pos !== false){
                unset($options_classess[$pos]);
            }
        }
        $options['class'] = join(' ', $options_classess);
        return $options;
        
    }
    
    private function array_classes(array $options = []){
        $classes = [];
        if (empty($options['class'])){
            return $classes;
        }
        
        $classes_string = '';
        
        if (is_array($options['class'])){
            $classes_string = join(' ', $options['class']);
        }
                
        
        if (is_string($options['class'])){
            $classes_string = $options['class'];
        }    
        
        if (!$classes_string){
            return $classes;
        }
        
        $parse1 = explode(' ', $classes_string);
        foreach ($parse1 as $word){
            if ($word){
                $classes[] = $word;
            }
        }
        
        return $classes;
    }
    
    public function addClass($options = [], $classes = null, $key = 'class'){
        if (empty($options[$key])){
            $options[$key] = $classes;
            return $options;
        }
        
        if (is_string($options[$key])){
            $options[$key] = $this->str_classes($options[$key]);
        }
        
        if (is_string( $classes )){
            $classes = $this->str_classes($classes);
        }
        $options[$key] = array_merge($options[$key], $classes);
        return $options;
    }
    
    public function strong($content){
        return $this->tag('strong', $content);
    }
    
    /**
     * "btn btn-default btn-primary" => ["btn", "btn-default", "btn-primary"]
     * @param type $string
     */
    public function str_classes($string){
        
        if (is_array( $string )){
            return $string;
        }
        
        $tmp = explode(" ", $string);
        $classes = [];
        foreach ($tmp as $tmp2){
            if ($tmp2){
                $classes[] = $tmp2;
            }
        }
        
        return $classes;
    }
    
    public function btn_group($options = [], $content = null){
        return $this->el("btn-group", $options, $content);
        //$options = $this->addClass($options, "btn-group") ;        
        //return $this->tag("div", $content, $options);
    }
    
    /*
     * Separate array options: strings, and hashes
     */
    private function parse_options($array = []){
        $arguments = $options = [];
        foreach ($array as $key => $value){
            if (is_int($key)){
                $arguments[] = $value;
            } else {
                $options[$key] = $value;
            }
        }
        return compact('arguments', 'options');
    }
    
    public function row($elements = [], $options = []){
        $row_content = '';
        $row_options = [
            "class" => "row"
        ];
        
        $col_count = [
            "lg" => 4,
            "md" => 3,
            "sm" => 2,
            "xs" => 1
        ];
        
        $devices = ["lg", "md", "sm", "xs"];
        $col_size = [];
        $col_classes = [];
        foreach ($devices as $device){                   
            
            if (!empty($options[$device])){
                $col_count[$device] = $options[$device];
            }
            
            $col_size[$device] = (int) 12/$col_count[$device];
            $col_classes[$device] = "col-" . $device . "-" . $col_size[$device];     
        }
        
        foreach ($elements as $element){
            
            if (is_string($element)){
                $row_content .= $this->div($element, [
                    "class" => $col_classes
                ]);
            }
            if (is_array($element) && isset($element['content'])){
                /*
                 * manual set of element col: size, offset, push
                 */
                $class = [];// $col_classes;
                foreach ($devices as $device){
                    //size
                    if (!empty( $element[$device] )){
                        $class[] = "col-" . $device . '-' .$element[$device];
                    } else {
                        $class[] = "col-" . $device . '-' .$col_size[$device];
                    }
                    
                    //offset
                    if (!empty( $element["offset"][$device] )){
                        $class[] = "col-" . $device . '-offset-' .$element["offset"][$device];
                    }
                    
                    //ordering, pull
                    if (!empty( $element["pull"][$device] )){
                        $class[] = "col-" . $device . '-pull-' .$element["pull"][$device];
                    }
                    
                    //ordering, push
                    if (!empty( $element["push"][$device] )){
                        $class[] = "col-" . $device . '-push-' .$element["push"][$device];
                    }
                }//devices calculation
                
                $row_content .= $this->div($element['content'], [
                    "class" => $class
                ]);
                
                //clearfix col
                if (!empty($element['clearfix']) && is_array($element['clearfix'])){
                    $clearfix_classes = ['clearfix'];
                    foreach ($element['clearfix'] as $cle){
                        $clearfix_classes[] = "visible-$cle-block";
                    }
                    $row_content .= $this->div("", [
                        'class' => $clearfix_classes
                    ]);
                }
            }
        }
        
        
        return $this->div($row_content, $row_options);
    }
    
    private function extract_contextual($array, $match, $class_arg = [], $tags_match = []){
        $contextual = $strings = $classes = $tags = [];
        
        foreach ($array as $test){
            $used = false;
            foreach ($match as $eq){
                if ($eq == $test){
                    $contextual[] = $eq;
                    $used = true;
                    break;
                }
            }
            if ($used){
                continue;
            }
            foreach ($class_arg as $class){
                if ( $class == $test ){
                    $classes[] = $class;
                    $used = true;
                    break;
                }
            }
            
            foreach ($tags_match as $tag){
                if ( $tag == $test ){
                    $tags[] = $tag;
                    $used = true;
                    break;
                }
            }
            
            if ($used){
                continue;
            }
            $strings[] = $test;
        }
        return compact('contextual', 'strings', "classes", "tags");
    }
    
    public function btn_submit($opts = [], $content = null){

        if (is_string($opts)){
            $opts = [$opts];
        }
        
        $opts["type"] = "submit";
        return $this->btn($opts,  $content);
    }
    
    public function btn($opts = [], $content = null){
        return $this->el("btn", $opts, $content);
    }
    
    public function alert($opts = [], $content = null){
        return $this->el("alert", $opts, $content);
    }
    
    public function icon($class, $options = [], $content = ''){
        
        //auto detect icon source: FontAwesome, GlyphIcon
        $tagName = "i";
        if (strpos($class, "fa-") !== false && strpos($class, "fa ") !== 0 ){
            $class = "fa " . $class;
        }
        if (strpos($class, "glyphicon-") !== false && strpos($class, "glyphicon ") !== 0 ){
            $class = "glyphicon " . $class;
            $tagName = "span";
        }
        
        if (!empty($options['tagName'])){
            $tagName = $options['tagName'];
            unset($options['tagName']);
        }
        
        $options = $this->addClass($options, $class);
        $options['aria-hidden'] = "true";
        return $this->tag($tagName, $content, $options);
    }
    
    public function icontext($content, $class = "fa-star", $options = []){
        $icon_align = 'left';
        if (!empty($options['icon_align'])){
            $icon_align = $options['icon_align'];
            unset( $options['icon_align'] );
        }
        switch ($icon_align) {
            case 'left':
                return $this->icon($class, $options) . ' ' . $content;

                break;
            
            case 'right':
                return $content . ' ' . $this->icon($class, $options);

                break;

            default:
                break;
        }
    }
    
    public function label($opts = [], $content = null){
        return $this->el("label", $opts, $content);
    }
    
    public function panel($opts = [], $content = null){
        return $this->el("panel", $opts, $content);
    }
    
    public function __call($method, $args){
        
        $method_parse = explode('_', $method);
        
        /*
         * multiple words
         */
        $multiple_methods = ['btn_group', 'btn_toolbar', 'btn_submit'];
        foreach ( $multiple_methods as $mume ){
            if (strpos($method, $mume) === 0){
                $method_parse = array_merge( [$mume], explode("_", substr($method, strlen($mume) + 1)) );
                //debug(compact('method', 'mume', 'method_parse'));
                break;
            }
        }
        
        $component_name = array_shift($method_parse);
        if (method_exists($this, $component_name)){
            
            
            switch ($component_name) {
                case "form":
                    debug(compact('args', 'method_parse'));
                    return $this->form($args[0], array_merge($method_parse,  $args[1]));

                    break;

                default:
                    break;
            }
            if ($args && is_string($args[0])){
                
                $args[0] = $args;
            }
            
            return $this->$component_name( 
                array_merge($method_parse, $args[0]), //argumant1
                empty($args[1]) ? null : $args[1] //argument 2 - content
                );
           // debug(compact('method'));
        } else {
            $error = "Error: Component $component_name not exists";
            debug(compact('method', 'args'));
        }
        
        
    }
    
    private function str_with_tags($string){
        if (strpos($string, "<") !== false){
            if (strpos($string, ">") !== false){
                return true;
            }
            
        }
        
        return false;
    }
    
    private function generate_contextual_classes($component_name, $contextual = []){
        $classes = [$component_name];
        foreach ($contextual as $word){
            $classes[] = $component_name . "-" . $word;
        }
        return $classes;
    }
    
    public function loadModel($modelName){
        if (empty($this->schema[$modelName] )){
            
            $this->$modelName = ClassRegistry::init($modelName);
            $this->schema[$modelName] = $this->$modelName->schema();
        }
        
        if (empty($this->schema[$modelName] )){
            //debug("Load model $modelName IS FAILEDDDDD!!!!");
            return false;
        }
        return $this->$modelName;
    }
    
    public function fieldFullName($string){
        if (strpos($string, '.') !== false){
            list($modelName, $fielName) = explode('.', $string);
        } else {
            $fielName = $string;
            $modelName = $this->Form->defaultModel;
        }
                
        $this->loadModel($modelName);
        if ( !empty($this->schema[ $modelName ][$fielName]) ){
            return $modelName . "." . $fielName;
        }
        return false;
    }
    
    public function fieldComment($fullFieldName){
        
    }
    
    public function fieldInfo($fullFieldName){
        
        if (strpos($fullFieldName, ".") === false){
            $modelName = $this->Form->defaultModel;
            $fieldName = $fullFieldName;
        } else {        
            list($modelName, $fieldName) = explode('.', $fullFieldName);
        }
        
        if ( empty($this->schema[ $modelName ][$fieldName]) ){
            $this->loadModel($modelName);
        }
        
        if (!empty($this->schema[ $modelName ][$fieldName])){
            
            if (!isset($this->schema[ $modelName ][$fieldName]['comment'])){
                $this->schema[ $modelName ][$fieldName]['comment'] = null;
            }
            
            if (!isset($this->schema[ $modelName ][$fieldName]['name'])){
                $this->schema[ $modelName ][$fieldName]['name'] = $fieldName;
            }
            
            if (!isset($this->schema[ $modelName ][$fieldName]['model'])){
                $this->schema[ $modelName ][$fieldName]['model'] = $modelName;
            }
            
            return $this->schema[ $modelName ][$fieldName];
        }
        
        /*
         * Example field info
         * 
                'type' => 'string',
                'null' => true,
                'default' => null,
                'length' => (int) 512,
                'collate' => 'utf8_general_ci',
                'comment' => 'List models as one string',
                'charset' => 'utf8'
         */
        
        return false;
    }
    
    public function progressbar($val = 35, array $options = []){
        $bars = [];
        
        $content = '';
        
        if (is_integer($val)){
            $bars[] = [ 35 ];
        }
        
        if (is_array($val)){
            $bars = $val;
        }
        
        foreach ($bars as $bar) {
            $bar_value = array_shift($bar);
            $bar['style'] = "width: $bar_value%";
            $bar['role'] = 'progressbar';            
            $bar['aria-valuenow'] = $bar_value;
            
            if (!isset($bar['aria-valuemin']))
            {
                $bar['aria-valuemin'] = 0;            
            }
            
            if (!isset($bar['aria-valuemax'])){
                $bar['aria-valuemax'] = 100;
            }
            
            $bar_content = $this->span("$bar_value%", [
                'class' => 'sr-only'
            ]);
            
            if (!empty($bar['label'])){
                if (is_bool($bar['label'])){
                    $bar_content = "$bar_value%";
                }
                
                if (is_string($bar['label'])){
                    $bar_content = str_replace('{{VALUE}}', $bar_value, $bar['label']);
                }
                
                unset($bar['label']);
            }
            
            $content .= $this->el('progress-bar', $bar, $bar_content);
            
        }
        
        $options = $this->addClass($options, 'progress');
        
        return $this->div($content, $options);
    }
    
    public function primaryKey($modelName){
        $this->loadModel($modelName);
        foreach ($this->schema[$modelName] as $fieldName => $fo){
            if ( !empty($fo['key']) && $fo['key'] == 'primary' ){
                return $fieldName;
            }
        }
        return false;
    }
    
    public function form($modelName, $options = []){
        $this->loadModel($modelName);
          
        $fieldsets = $hidden_fields = $btn = $header = [];
        if (array_key_exists('fieldset', $options)){
            $fieldsets = $options['fieldset'];
            unset($options['fieldset']);
        }
        
        if (array_key_exists('header', $options)){
            $header = $options['header'];
            unset($options['header']);
        }
        
        if (array_key_exists('btn', $options)){
            $btn = $options['btn'];
            unset($options['btn']);
        }
        
        if (!empty($options['hidden'])){
            $hidden_fields = $options['hidden'];
            unset($options['hidde']);
        }
        
        //contextual
        foreach ($this->components["form"]["contextual"]["list"] as $tm1){
            if ( ($key = array_search($tm1, $options)) !== false ){
                $options = $this->addClass($options, "form-" . $tm1);
                unset($options[$key]);
            }
        }
   
             
        if ($header){
            if (!$this->str_with_tags($header)){
                $form_html .= $this->tag('h2', $header);
            } else {
                $form_html .= $header;
            }
            
        }
        
        
        
        //simple `fields` set
        if (empty($options['fieldset']) && !empty($options['fields'])){
            
            $fieldsets = [
                [ 'fields' => $options['fields'] ]
            ];
            
            unset($options['fields']);
        }
        
        $form_html = $this->Form->create($modelName, $options);   
        //hidden, key fields        
        /*
         * find primary kye
         */
        $primaryKey = $this->primaryKey($modelName);
        $form_html .= $this->Form->hidden($primaryKey);
        
        //custom hidden fields
        if ($hidden_fields){
            foreach ($hidden_fields as $one => $two){
                if (is_int($one) && is_string($two)){
                    $form_html .= $this->Form->hidden($two);
                }
                
                if (is_string($one) && is_array($two)){
                    $form_html .= $this->Form->hidden($one, $two);
                }
            }
        }
                
        //fieldsets
        foreach ($fieldsets as $fieldset){
            $fieldset_html = '';
            if (!empty($fieldset['legend'])){
                $fieldset_html .= $this->tag('legend', $fieldset['legend']);
            }

            if (!empty($fieldset['fields'])){
                //make row
                $row = [];
                $row_options = [];
                
                if (!empty($fieldset['row'])){
                    $row_options = $fieldset['row'];
                    unset($fieldset['row']);
                }
                
                foreach ($fieldset['fields'] as $one => $two){
                    if (is_int($one) && is_string($two)){
                        
                        if ($this->str_with_tags($two)){
                            //real html block
                            $row[] = $two;
                        } else {
                            //simple field by only name
                            $row[] = $this->input($two);
                        }
                    }
                    if (is_string($one) && is_array($two)){
                        $row[] = $this->input($one, $two);
                    }
                }
                
                $fieldset_html .= $this->row($row, $row_options);
            }
            
            $form_html .= $this->tag('fieldset', $fieldset_html, [
                //fieldset options
            ]);
        }
        
        //btn
        if ($btn !== false){
            if (empty($btn)){
                $btn = [$this->btn_submit_primary("input")];
            }

            if (!empty($btn) && is_string($btn)){
                if ($this->str_with_tags($btn)){
                    $btn = [$btn];
                } else {
                    $btn = [$this->btn_submit_primary("input", $btn)];
                }
            }

            //toolbar
            $form_html .= $this->btn_group(["pull-right"], $btn);           
        }

        
        $form_html .= $this->Form->end();
        
        return $form_html;
    }
    
    public function input($fullFieldName, $options = []){
        
        $field = $this->fieldInfo($fullFieldName);
        if ($field['comment'] && empty($options['label'])){
            $options['label'] = $field['comment'];
        }
        
        $options['div'] = [
            'class' => "form-group"
        ];
        
        $options = $this->addClass($options, "form-control");
        
        //required value
        if (isset($field['null']) && $field['null'] === false){
            $options[] = 'required';
        }
        
        
        if ($field['type'] == 'boolean'){
            $fieldLabel = $field['comment'];
            if (!$fieldLabel){
                $fieldLabel = Inflector::humanize($field['name']);
            }
            
            return $this->div(
                    $this->tag('label', $this->Form->input($fullFieldName, [
                        'label' => false,
                        'div' => false
                    ]) . ' ' . $fieldLabel )
                    , [
                        'class' => "checkbox"
                    ]);

        }
                
        if ($field['type'] == 'date' || ( isset($options['type']) && $options['type'] == 'date' )){
            $options['type'] = 'text';
            return
                str_replace('type="text"', 'type="date"',   $this->Form->input($fullFieldName, $options));
        }
        
        //debug(compact('field'));       
        
        return $this->Form->input($fullFieldName, $options);
    }

    
   public function pagination($pagination_options = array()){
        
         $pagination_content = $this->Paginator->numbers(array(
           // 'model' => $modelName,
            'tag' => 'li',
            'separator' => null,
            'currentTag' => 'a',
            'currentClass' => 'active',
            'first' => 1,
            'last' => 1,
            'ellipsis' => null,
        ));
         
         $pagination_options = $this->Html->addClass($pagination_options, 'pagination');
         
         #Write last staistic
         if (!empty($this->_View->params)){
             $model = null;
             foreach ($this->_View->params['paging'] as $paginate_model => $paginate_options){
                 $model = $paginate_model;
                 break;
             }
             $this->paging = $this->_View->params['paging'][$model];
         }
         
         return $this->tag('nav',
                 $this->tag('ul',
                         $pagination_content,
                         $pagination_options
                      )
                 );
    }
    
    public function table($options = [], $data = [], $advanced_options = [] ){
        // thead
        $columns = [];
        $thead = $tbody = $tfoot = $table_caption = '';
        
        $tfoot_data = [];
        if (isset($options['tfoot'])){
            $tfoot_data = $options['tfoot'];
            unset($options['tfoot']);
        }
        
        $indicator = null;
        
        if (isset($options['indicator'])){
            $indicator = $options['indicator'];
            unset($options['indicator']);
            if (!is_array($indicator)){
                $indicator = [];
            }

            
            $indicator = $this->addClass($indicator, 'indicator');
        }
                
        if (empty($advanced_options['mode'])){
            /*
             * html - full table with html and rendered values cells
             * tr  - only one first row without headers/footer with <tr>..</tr>
             * table - html structure only text data for exporting,
             */
            $advanced_options['mode'] = 'html';
        }
        
        if (!in_array($advanced_options['mode'], ['html', 'tr'])){
            $indicator = null;
        }
        
        $csv = [];
        if ($advanced_options['mode'] == 'csv'){
            $advanced_options['csv_divider'] =
              isset($advanced_options['csv_divider'])
                    ? $advanced_options['csv_divider']
                    : "\t";
            $advanced_options['csv_line_end'] =
              isset($advanced_options['csv_line_end'])
                    ? $advanced_options['csv_line_end']
                    : "\n";
        }
        
        if (!array_key_exists('boolean_icon', $advanced_options)){
            $advanced_options['boolean_icon'] = 
                    $this->components["table"]["icons"]["boolean"];
        }
        
        $first_datum = [];
        if ($data && !empty($data[0])){
            $first_datum = $data[0];
        }
        
        
        $modelName = empty($options['modelName']) ? null : $options['modelName'];
        
        $primaryKey = empty($options['primaryKey']) ? null : $options['primaryKey'];
        
        if (!$primaryKey){
            $primaryKeyName = $this->primaryKey($modelName);
            
            if ($primaryKeyName){
                $primaryKeyInfo = $this->fieldInfo($modelName . "." . $primaryKeyName);
            }
            
            if ($primaryKeyInfo){
                $primaryKey = $primaryKeyInfo['model'] . '.' . $primaryKeyInfo["name"];
            }
        }

        
        $thead_tr = '';
        
        if (!empty($indicator)){
            
            $indicator_options = [];
            $indicator_content = '';
            if (is_string($indicator)){
                $indicator_content = $indicator;
            }
            
            if (is_array($indicator)){
                if (isset($indicator['content'])){
                    $indicator_content = $indicator['content'];
                    unset($indicator['content']);
                }
            }
            
            $thead_tr .= $this->tag('th', $indicator_content, $indicator_options);
        }
        
        $csv_row = [];
        foreach ($options['fields'] as $one => $two){
            $column = [];
            if (is_int( $one) && is_string($two)){
                $column = [
                    "field" => $two
                ];
            }
            
            if (is_int($one) && is_array($two) ){
                $column = $two;
            }
            
            if (is_string($one) && is_array($two)){
                $column = $two;
                if (empty( $column["field"] )){
                    $column["field"] = $one;
                }
            }
            
            $th_options = [];
            
            
            //check full field name
            if (!isset($column['field'])){
                $column['field'] = null;
            }
            
            if ($column['field']){
                if (strpos($column['field'], ".") !== false){
                    list($column['modelName'], $column['fieldName']) =
                            explode(".", $column['field']);
                } else {
                    if ($modelName){
                        $column['modelName'] = $modelName;
                        $column['fieldName'] = $column['field'];
                    }
                }
            }
            
            if (!empty($column['modelName']) && !empty($column['fieldName']) ){
                $column['fullFieldName'] = join('.', [
                    $column['modelName'],
                    $column['fieldName']
                ]);
            }
            
            //check fields exists in first datume
            $column['exists'] =  
                    array_key_exists('modelName', $column) 
                 && array_key_exists( $column['modelName'] , $first_datum) 
                 && array_key_exists( $column['fieldName'] , $first_datum[ $column['modelName'] ]);
            
            //header
            if (empty($column['header'])){
                if ( !empty( $column['fullFieldName'] ) ){
                    $column['dbinfo'] = $this->fieldInfo($column['fullFieldName']);
                    $column['header'] = 
                        array_key_exists("dbinfo", $column) &&
                            is_array($column['dbinfo']) &&
                        array_key_exists("comment", $column['dbinfo']) &&
                            !empty( $column['dbinfo']["comment"] )
                            ? $column['dbinfo']["comment"]
                            : Inflector::humanize( $column['fieldName'] );
                }
            }
            
            //value types
            if (empty($column['type'])){
                if (!empty($column["dbinfo"]["type"])){
                   $column['type'] = $column["dbinfo"]["type"];
               } else {
                   $column['type'] = "string";
               }
            }
            
            if (!empty($column['type'])){
                switch ($column['type']) {
                    case "integer":
                        $th_options = $this->addClass($th_options, "text-right");

                        break;
                    case "boolean":
                        $th_options = $this->addClass($th_options, "text-center");
                        break;

                    default:
                        break;
                }
            }
            
            //visible column
            if (isset($column['hide']) && $column['hide'] === true){
                
            } else {
                $column['hide'] = false;
            }
            
            $th_options['data-fieldName'] = $column['fullFieldName'];
            $th_options['data-type'] = $column['type'];
            
            if ( !$column['hide'] && !empty($column['header']) ){
                
                if ($advanced_options['mode'] == "table"){
                    $th_options = [];
                    $column['header'] = strip_tags($column['header']);
                }
                
                if ($advanced_options['mode'] == "csv"){
                    //$th_options = [];
                    $csv_row[] = strip_tags($column['header']);
                    //continue;
                }
                
                $thead_tr .= $this->tag('th', $column['header'], $th_options);
            }
                    
            $columns[] = $column;
        }
        
        if ($advanced_options['mode'] == 'csv'){
            $csv[] = join($advanced_options['csv_divider'], $csv_row);
        }
        $thead = $this->tag('tr', $thead_tr);
        
        
        
        unset($options['fields']);
        
        if ($advanced_options['mode'] == 'config'){
            return compact(
                    'modelName',
                    'primaryKey',
                    'primaryKeyInfo',
                    'columns', 
                    'options', 
                    'advanced_options'
                    
                    );
        }
        
        //debug(compact('columns'));

        // tbody
     
        foreach ($data as $datum){
            $tr = '';
            
            if (!empty($indicator)){
                $tr .= $this->tag('td', '', $indicator);
            }
        
            $tr_options = [];
            $csv_row    = [];
            if (isset($datum['__tr'])){
                $tr_options = $datum['__tr'];
            }
            
            //primary key id
            if ($primaryKey){
                list($primaryModel, $primaryField) = explode('.', $primaryKey);
                if (array_key_exists($primaryModel, $datum)
                    && array_key_exists($primaryField, $datum[$modelName])
                    ){
                    $tr_options['data-id'] = $datum[$primaryModel][$primaryField];
                        }
            }
                
            foreach ($columns as $column){
               $value = null;
               $td_value = $predefined_td_content = null ;
               $td_options = [];
               $predefined_td =null;
               
               if ( !empty($column['fullFieldName']) && !empty($datum['__td'][ $column['fullFieldName'] ]) ){
                   $predefined_td = $datum['__td'][ $column['fullFieldName'] ];
                   if (is_string($predefined_td)){
                       $predefined_td_content = $predefined_td;
                   }
                   if (is_array($predefined_td)){
                       if ( !empty( $predefined_td['content'] ) ){
                           $predefined_td_content = $predefined_td['content'];
                           unset( $predefined_td['content'] );
                           $td_options = $predefined_td;
                           //$predefined_td
                       } else {
                           $td_options = $predefined_td;
                       }
                   }
               }
               
               //if ()
               
               if (
                       array_key_exists('modelName', $column) &&
                       array_key_exists('fieldName', $column) &&
                       array_key_exists($column['modelName'], $datum) &&
                       array_key_exists($column['fieldName'], $datum[ $column['modelName'] ]) 
                       ){
                   $value = $datum[ $column['modelName'] ][ $column['fieldName'] ];
               }
               
               $td_value = $value;
               
               switch ($column['type']) {
                   case "integer":
                       $td_options = $this->addClass($td_options, "text-right");

                       break;
                   case "boolean":                       
                       $td_options = $this->addClass($td_options, "text-center");
                       $td_value = $value ? "TRUE" : "FALSE";
                       if ( 
                               $advanced_options['mode'] == "html"
                               || $advanced_options['mode'] == "tr"                               
                               ){
                            
                            if ( $advanced_options['boolean_icon'] ){
                                
                                $td_value = $value 
                                        ? $this->icon($advanced_options['boolean_icon']["true"])
                                        : $this->icon($advanced_options['boolean_icon']["false"]);
                            }
                       }

                       break;

                   default:
                       break;
               }
               
               //belongsTo value
               if (!empty($column['belongsTo'])){
                   list($bModelName, $bFieldName) = explode('.', $column['belongsTo']);
                   if ( isset($datum[$bModelName]) && array_key_exists($bFieldName, $datum[$bModelName])){
                       $td_value = $datum[$bModelName][$bFieldName];
                   }
                   //if (empty($column['']))
               }
                         
               if (!$column['hide']){
                   
                   $td_render_value = $predefined_td_content ? : $td_value;
                   
                   if ($advanced_options['mode'] == "table"){
                       $td_options = [];
                       $td_render_value = strip_tags($td_render_value);
                   }
                   
                   if ($advanced_options['mode'] == "csv"){
                       $td_options = [];
                       $td_render_value = strip_tags($td_render_value);
                       $csv_row[] = $td_render_value;
                       continue;
                   }
                   $tr .= $this->tag("td", $td_render_value, $td_options);
               }
            }
            
            //one row returns, first datum!!!!
            if ( $advanced_options['mode'] == 'tr' ){
                return $this->tag('tr', $tr, $tr_options);
            }
            
            if ($advanced_options['mode'] == "table"){
                $tr_options = [];
            }
            
            if ($advanced_options['mode'] == "csv"){
                $csv[] = join($advanced_options['csv_divider'], $csv_row);
            }
            
            $tbody .= $this->tag('tr', $tr, $tr_options);
        }
        
        if ($advanced_options['mode'] == "csv"){
            return join($advanced_options['csv_line_end'], $csv);
        }
        
        //table caption
        if (isset( $options['caption'] )){
            $table_caption = $options['caption'];
            unset( $options['caption']  );
        }
        
        // tfoot
        if ($tfoot_data){
            $tfoot_tr = '';
            
            if (!empty($indicator)){
                $tfoot_tr .= $this->tag('td', '', $indicator);
            }
            
            $tfoot_tr_options = [];
            
            if (!empty($tfoot_data['options'])){
                $tfoot_tr_options = $tfoot_data['options'];
            }
            
            foreach ($columns as $column){
                $tfoot_value = '';
                $tfoot_options = [];
                if ( !empty($column['fullFieldName']) 
                        && !empty( $tfoot_data[ $column['fullFieldName'] ] ) ){                    
                    
                    
                    $tfoot_params = $tfoot_data[ $column['fullFieldName'] ];
                    
                    if (is_array($tfoot_params) ){
                        if (isset($tfoot_params['value'])){
                            $tfoot_value = $tfoot_params['value'];
                            unset($tfoot_params['value']);
                            
                            $tfoot_options = $tfoot_params;
                        }
                    } else {
                        $tfoot_value = $tfoot_params;
                    }
                    
                    
                }
                if (!$column['hide']){
                    if ($advanced_options['mode'] == "table"){
                            $tfoot_options = [];
                            $tfoot_value = strip_tags($tfoot_value);
                        }
                    
                    
                    $tfoot_tr .= $this->tag('td', $tfoot_value, $tfoot_options);
                }
            }
            
                        //one row returns, first datum!!!!
            if ( $advanced_options['mode'] == 'tfoot' ){
                return $this->tag('tr', $tfoot_tr, $tfoot_tr_options);
            }   
            
            if ($advanced_options['mode'] == "table"){
                    $tfoot_tr_options = [];
                }
            
            $tfoot .= $this->tag('tr', $tfoot_tr, $tfoot_tr_options);
        }
        
        $table_options = $options;
        
        $table_options = $this->addClass($table_options, 'table');
        //$table_options = $this->addClass($table_options, 'table-bordered');
        
        if ($advanced_options['mode'] == "table"){
            $table_options = [];
        }
        
        return $this->tag("table", [
            $thead ? $this->tag("thead", $thead, []) : null,
            $table_caption ? $this->tag('caption', $table_caption, []) : null,
            $this->tag("tbody", $tbody, []),
            $tfoot ? $this->tag("tfoot", $tfoot, []) : null,
        ], $table_options);
    }
    
    public function navbar($options = []){
        return $this->el("navbar", $options);
    }
    
    private function extract_options($options, $key){
        $other_options = [];
        if (isset($options[$key])){
            $other_options = $options[$key];
            unset($options[$key]);
        }
        return [$options, $other_options];
    }
    
    public function navtabs($tabs = [], $options = []){
        
        if (empty($options['active_index'])){
            $options['active_index'] = 0;
        }
        
        //initialize options for elements
        list($options, $nav_options) = $this->extract_options($options, 'nav');        
        $nav_options += [ 'role' => 'tablist' ];        
        $nav_options = $this->addClass($nav_options, ['nav', 'nav-tabs']);
        
        list($options, $content_options) = $this->extract_options($options, 'content');
        $content_options = $this->addClass($content_options, ['tab-content']);
        
        
        $nav_html = $content_html = '';
        $ix = 0;
        foreach ($tabs as $one => $two){
            $tab_header = $tab_content = '';
            $tab_options = $a_options = $li_options = [];
            
            if (is_string($one)){
                $tab_header = $one;
                if (is_string($two)){
                    $tab_content = $two;
                } else {
                    $tab_options = $two;
                }
            } else {
                $tab_options = $two;
            }
            
            if (isset($tab_options['content'])){
                $tab_content = $tab_options['content'];
                unset( $tab_options['content'] );
            }
            
            if (isset($tab_options['header'])){
                $tab_header = $tab_options['header'];
                unset( $tab_options['header'] );
            }
            
            if (isset($tab_options['li']) && is_array( $tab_options['li'] )){
                $li_options = $tab_options['li'];
                unset($tab_options['li']);
            }
            
            if (isset($tab_options['a']) && is_array( $tab_options['a'] )){
                $a_options = $tab_options['a'];
                unset($tab_options['a']);
            }

            $active = isset($options['active_tab']) 
                    ? $tab_header == $options['active_tab'] 
                    : $ix == $options['active_index'] ;
            
            if ($active){
                $tab_options = $this->addClass($tab_options, 'active');
                $li_options  = $this->addClass($li_options, 'active');
            }
            
            $tab_options = $this->addClass($tab_options, 'tab-pane');
            
            $tab_options += [
                'id' => empty($tab_options['id']) ?  uniqid() : $tab_options['id'] ,
                'role' => 'tabpanel'                
                ];
            
            
            $a_options += [
                'href' => '#' . $tab_options['id'],
                'aria' => [
                    'controls' => $tab_options['id']
                ],
                'role' => 'tab',
                'data' => [
                    'toggle' => 'tab'
                ]
            ];
            
            $li_options += [
                'role' => 'presentation'
            ];
            
            if (empty($tab_options['dropdown'])){            
                $nav_html .= $this->li($this->a($tab_header, $a_options), $li_options);
                $content_html .= $this->div($tab_content, $tab_options);
            } else {
                /*
                 * enumerate items
                 */
                
                $li_options = $this->addClass($li_options, 'dropdown');
                $a_options = $this->addClass($a_options, 'dropdown-toggle');
                
                $a_options['id'] = uniqid();
                $a_options['data-toggle'] = 'dropdown';
                $a_options['aria-controls'] = uniqid();
                $a_options['aria-expanded'] = 'false';
                unset($a_options['role']);
                
                $dropdown_ul_content = '';
                
                foreach ($tab_options['items'] as $item_title => $item_content){
                    if ($item_content === false){
                        continue;
                    }
                    $item_a_options = [
                        'id' => uniqid(),
                        'data-toggle' => 'tab',
                        'role' => 'tab',
                        'aria-controls' => uniqid(),
                        'aria-expanded' => 'false'
                    ];
                    
                    $item_a_options['href'] = '#' . $item_a_options['aria-controls'];
                    
                    $item_pane_options = [
                        'class' => 'tab-pane',
                        'id' => $item_a_options['aria-controls'],
                        'role' => 'tabpanel',
                        'aria-labelledby' => $item_a_options['id']
                    ];                    
                    
                    $dropdown_ul_content .= $this->li($this->a($item_title, $item_a_options));
                    $content_html .= $this->div($item_content, $item_pane_options);
                }
                
                
                
                $nav_html .= $this->li([
                        $this->a($tab_header . " "  . $this->caret(), $a_options) ,                        
                        $this->ul($dropdown_ul_content, [
                            'id' => $a_options['aria-controls'],
                            'aria-labelledby' => $a_options['id'],
                            'class' => 'dropdown-menu'
                        ]) 
                    ], $li_options);
                
            }
                        
            $ix++;
        }
        
        $options = $this->addClass($options, 'navtabs');
        
        return $this->div( [
            $this->ul($nav_html, $nav_options),
            $this->div($content_html, $content_options),
            ], $options);
    }
}
