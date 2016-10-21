<?php

if (!class_exists('phpapp_Active_Form')) {

    class phpapp_Active_Form
    {
        public $model;
        public $clientValidation;

        /**
         * An empty construct since we will do factory the form
         */
        private function __construct($model)
        {

        }

        public static function generateForm($model)
        {
            $form = new phpapp_Active_Form($model);
            return $form;
        }

        public function openForm($action = '', $method = '', $htmlOptions = array())
        {
            echo phpapp_Form::createForm($action, $method, $htmlOptions);
        }

        public function endForm()
        {
            echo phpapp_Form::endForm();
        }

        public function textField($model, $attribute, $htmlOptions = array())
        {
            echo phpapp_Form::textField($this->buildFormElementName($model, $attribute), $model->$attribute, $htmlOptions);
            $this->renderError($model, $attribute);
        }

        public function passwordField($model, $attribute, $htmlOptions = array())
        {
            echo phpapp_Form::passWordField($this->buildFormElementName($model, $attribute), $model->$attribute, $htmlOptions);
            $this->renderError($model, $attribute);
        }

        public function hiddenField($model, $attribute, $htmlOptions = array())
        {
            echo phpapp_Form::hiddenField($this->buildFormElementName($model, $attribute), $model->$attribute, $htmlOptions);
        }

        public function dropDownList($model, $attribute, $data = array(), $htmlOptions = array())
        {
            $selected = $model->$attribute;
            if (!is_array($selected))
                $selected = array($selected);
            echo phpapp_Form::dropDownList($this->buildFormElementName($model, $attribute), $selected, $data, $htmlOptions);
            $this->renderError($model, $attribute);
        }

        public function checkBox($model, $attribute, $htmlOptions = array())
        {
            $checked = false;
            if (in_array($model->$attribute, array(1, true, 'on')))
                $checked = true;
            echo phpapp_Form::checkBox($this->buildFormElementName($model, $attribute), $checked, $htmlOptions);
            $this->renderError($model, $attribute);
        }

        public function textArea($model, $attribute, $htmlOptions = array())
        {
            echo phpapp_Form::textArea($this->buildFormElementName($model, $attribute), $model->$attribute, $htmlOptions);
        }

        public function countryDropDown($model, $attribute, $htmlOptions = array())
        {
            $selected = $model->$attribute;
            if (!is_array($selected))
                $selected = array($selected);

            echo phpapp_Form::countryDropdown($this->buildFormElementName($model, $attribute), $selected, $htmlOptions);
            $this->renderError($model, $attribute);
        }

        private function renderError($model, $attribute)
        {
            if ($model->has_error($attribute)) {
                echo '<span class="error_message">' . $model->get_error($attribute) . '</span>';
            }
        }

        private function buildFormElementName($model, $attribute)
        {
            $model_class_name = get_class($model);
            $frm_element_name = $model_class_name . "[$attribute]";
            return $frm_element_name;
        }

    }
}