<?php

/**
 * return html admin form
 *
 * Class HelperBuildFormAdminNo2Pay4
 */
class HelperBuildFormAdminNo2Pay4 {

    protected $_html = array();

    /**
     * @param array $fields['label_title'=>'...', 'label_help'=>'...', 'type'=>'text/', 'name'=>'...', 'value'=>'...', 'placeholder'=>'...', 'class'=>array('....'), 'id'=>'...']
     * @return string html
     */
    public function input($fields = array(), $block = true){
        $this->emptyHtml();
        $this->addHtml('<input');

        if(array_key_exists('type', $fields)){
            if($fields['type'] == 'text' || $fields['type'] == 'password' || $fields['type'] == 'email' || $fields['type'] == 'hidden'){
                $this->addHtml('type="'.$fields['type'].'"');

                if(array_key_exists('name', $fields)){
                    $this->addHtml('name="'.$fields['name'].'"');
                }

                if(array_key_exists('value', $fields)){
                    $this->addHtml('value="'.$fields['value'].'"');
                }

                if(array_key_exists('id', $fields)){
                    $this->addHtml('id="'.$fields['id'].'"');
                }

                if(array_key_exists('placeholder', $fields)){
                    $this->addHtml('placeholder="'.$fields['placeholder'].'"');
                }

                $class = array('form-control');

                if(array_key_exists('class', $fields)){
                    $class = array_merge($class, $fields['class']);
                }

                $this->addHtml('class="'.implode(' ', $class).'"');
            }
        }

        $this->addHtml('/>');

        $fields['content'] = $this->renderHtml(' ');

        if(array_key_exists('type', $fields)){
            if($fields['type'] == 'hidden'){
                return $fields['content'];
            }
        }
        if($block){
            return $this->field($fields);
        }

        return $fields['content'];
    }

    /**
     * @param array $fields['label_title'=>'...', 'label_help'=>'...', 'name'=>'...', 'value'=>'array|string', 'multiple'=>true, 'size'=>'...',  'class'=>array('....'), 'id'=>'...', 'fields'=>array(array('value'=>'...','name'=>'...','attr'=>'...')),'option_name'=>'...', 'option_value'=>'...', 'option_attr'=>'...']
     * @return string
     */
    public function select($fields = array()){
        $this->emptyHtml();
        $this->addHtml('<select');

        $multiple = '';
        if(array_key_exists('multiple', $fields) && $fields['multiple'] == true){
            $multiple = '[]';
        }

        if(array_key_exists('name', $fields)){
            $this->addHtml('name="'.$fields['name'].$multiple.'"');
        }

        if(array_key_exists('id', $fields)){
            $this->addHtml('id="'.$fields['id'].'"');
        }

        $class = array('form-control');
        if(array_key_exists('class', $fields)){
            $class = array_merge($class, $fields['class']);
        }

        $this->addHtml('class="'.implode(' ', $class).'"');

        if(array_key_exists('multiple', $fields) && $fields['multiple'] == true){
            $this->addHtml('multiple="multiple"');
        }

        if(array_key_exists('size', $fields)){
            $this->addHtml('size="' . $fields['size'] .'"');
        }

        $this->addHtml('>');

        $this->selectOption($fields);

        $this->addHtml('</select>');
        $fields['content'] = $this->renderHtml(' ');

        return $this->field($fields);
    }

    /**
     * @param $fields ['value'=>'...','fields'=>array(array('value'=>'...','name'=>'...','attr'=>'...')),'option_name'=>'...', 'option_value'=>'...', 'option_attr'=>'...']
     * @return $this
     */
    public function selectOption($fields){

        $name = 'name';
        if(array_key_exists('option_name', $fields)){
            $name = $fields['option_name'];
        }

        $value = 'value';
        if(array_key_exists('option_value', $fields)){
            $value = $fields['option_value'];
        }

        $attr = 'attr';
        if(array_key_exists('option_attr', $fields)){
            $attr = $fields['option_attr'];
        }

        $selected = '';
        if(array_key_exists('value', $fields)){
            $selected = $fields['value'];
        }

        $isArray = is_array($selected);

        if(array_key_exists('options', $fields)){
            foreach($fields['options'] as $option){
                $this->addHtml('<option');
                $this->addHtml('value="'.$option[$value].'"');

                if($isArray){
                    if(in_array($option[$value], $selected)){
                        $this->addHtml('selected="selected"');
                    }
                }else{
                    if($option[$value] == $selected){
                        $this->addHtml('selected="selected"');
                    }
                }

                if(array_key_exists($attr, $option)){
                    $this->addHtml($attr.'="'.$option[$attr].'"');
                }

                $this->addHtml('>');
                $this->addHtml($option[$name]);
                $this->addHtml('</option>');
            }
        }
        return $this;
    }

    /**
     * @param array $fields['label_title'=>'...', 'label_help'=>'...', 'content'=>'...', 'id'=>'...']
     * @return string
     */
    public function field($fields = array()){

        $this->emptyHtml();
        $this->addHtml('<div class="form-group">');
        // check and add lael title
        if(array_key_exists('label_title', $fields)){
            $id = '';
            if(array_key_exists('id', $fields)){
                $id = $fields['id'];
            }

            $this->addHtml('<label class="col-sm-2 control-label" for="'.$id.'">');
            // check and add description
            if(array_key_exists('label_help', $fields)){
                $this->addHtml('<span data-toggle="tooltip" title="'.$fields['label_help'].'">');
                $this->addHtml($fields['label_title']);
                $this->addHtml('</span>');
            }else{
                $this->addHtml($fields['label_title']);
            }
            $this->addHtml('</label>');
        }

        // check and add content
        if(array_key_exists('content', $fields)){
            $this->addHtml('<div class="col-sm-10">');
            $this->addHtml($fields['content']);
            $this->addHtml('</div>');
        }

        $this->addHtml('</div>');

        return $this->renderHtml();
    }

    public function emptyHtml(){
        $this->_html = array();
    }

    public function addHtml($html){
        $this->_html[] = $html;
    }

    public function renderHtml($glue = ''){
        return implode($glue, $this->_html);
    }
}