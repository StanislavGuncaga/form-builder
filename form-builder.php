<?php

/**
 * Form builder class based on bootstrap
 *
 * @version 0.0.1
 */
class bs_form_element
{
    /**
     * @var
     */
    var $template;
    /**
     * @var
     */
    var $replacements;

    /**
     * @param $template
     * @param $replacements
     */
    public function __construct($template, $replacements) {
        $this->template     = $template;
        $this->replacements = $replacements;
    }

    /**
     * @return mixed
     */
    public function get_template() {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function set_template($template) {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function get_replacements() {
        return $this->replacements;
    }

    /**
     * @return bool
     */
    public function has_name() {
        return isset($this->replacements['{attributes}']['name']);
    }

    /**
     * @return mixed
     */
    public function get_name() {
        return $this->replacements['{attributes}']['name'];
    }

    /**
     * @param mixed $replacements
     */
    public function set_replacements($replacements) {
        $this->replacements = $replacements;
    }

    /**
     * @param $attributes
     *
     * @return string
     */
    private function build_group($attributes) {
        foreach ($attributes as $attribute => &$value) {
            if (is_object($value)) {
                $value = $value->build();
            } else {
                $value = $attribute . '="' . $value . '"';
            }
        }

        return implode(' ', $attributes);
    }

    /**
     * @return string
     */
    public function build() {
        $template     = self::get_template();
        $replacements = self::get_replacements();

        foreach ($replacements as &$replacement) {
            if (is_object($replacement)) {
                $replacement = $replacement->build();
            } elseif (is_array($replacement)) {
                $replacement = self::build_group($replacement);
            }
        }

        return strtr($template, $replacements);
    }
}

/**
 * Class bs_form_builder
 */
class bs_form_builder
{
    /**
     * @var array
     */
    var $form_groups;
    /**
     * @var array
     */
    var $settings;
    /**
     * @var
     */
    var $validations;
    /**
     * @var
     */
    var $inputs;

    /**
     * @param mixed $current_input_name
     */
    private function set_input($input) {
        if (is_object($input) AND $input->has_name()) {
            $this->inputs[$input->get_name()] = $input;
        }
    }

    /**
     * @return mixed
     */
    private function get_input($name) {
        return $this->inputs[$name];
    }

    /**
     * @return mixed
     */
    private function get_last_input() {
        return end($this->inputs);
    }

    /**
     * @param mixed $validations
     */
    public function set_validations($input_name, $type, $data) {
        $this->validations[$input_name][$type] = $data;
    }

    /**
     * @return mixed
     */
    public function get_validations() {
        return $this->validations;
    }

    /**
     * Set action attribute in form tag.
     *
     * @since 0.0.1
     *
     * $var string $value path to php file
     *
     * @return $this
     */

    public function action($value) {
        self::set_setting(__FUNCTION__, $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function country($value) {
        self::set_setting(__FUNCTION__, $value);

        return $this;
    }

    /**
     * Set form method
     *
     * @since 0.0.1
     *
     * @param string $value Accepts 'post', 'get'. Default 'post'
     *
     * @return $this
     */
    public function method($value) {
        self::set_setting(__FUNCTION__, $value);

        return $this;
    }

    /**
     * Set form title
     *
     * @since 0.0.1
     *
     * @param string $value Default empty
     *
     * @return $this
     */
    public function name($value) {
        self::set_setting(__FUNCTION__, $value);

        return $this;
    }

    /**
     * Set columns width
     *
     * @since 0.0.1
     *
     * @param string $label           Width of label column. Excepts bootstrap classes from 'col-md-1' to 'col-md-12'.
     *                                Default 'col-md-4'
     * @param string $input_container Width of input column. Excepts bootstrap classes from 'col-md-1' to 'col-md-12'.
     *                                Default 'col-md-4'
     *
     * @return $this
     */
    public function column_width($label, $input_container) {
        self::set_setting('label_width', $label);
        self::set_setting('input_container_width', $input_container);

        return $this;
    }

    /**
     * Set default settings of form builder
     *
     * @since 0.0.1
     */

    public function id($value) {
        self::set_setting(__FUNCTION__, $value);

        return $this;
    }

    /**
     *
     */
    private function default_settings() {
        self::action('');
        self::method('post');
        self::name('');
        self::column_width('col-md-4', 'col-md-4');
        self::id(rand());
        self::country('SK');
    }

    /**
     * Get current settings
     *
     * @since 0.0.1
     *
     * @param string $setting Setting name
     *
     * @return mixed
     */
    public function get_setting($setting) {
        return $this->settings[$setting];
    }

    /**
     * Update setting. If not exist create new
     *
     * @since 0.0.1
     *
     * @param mixed $settings
     */
    public function set_setting($setting, $value) {
        $this->settings[$setting] = $value;
    }

    /**
     * bs_form_builder constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        self::default_settings();
    }

    /**
     * bs_form_builder destructor.
     *
     * @since 0.0.1
     */
    public function __destruct() {
    }

    /**
     * @return array
     */
    public function get_form_groups() {
        return $this->form_groups;
    }

    /**
     * @param $form_group
     */
    public function set_form_group($form_group) {
        $this->form_groups[] = $form_group;
    }

    /**
     * @param $label
     *
     * @return bs_form_element
     */
    private function label($label) {
        $label_width  = self::get_setting('label_width');
        $template     = '<label class="' . $label_width . ' control-label">{label}</label>';
        $replacements = array('{label}' => $label);

        return new bs_form_element($template, $replacements);
    }

    /**
     * @param $glyphicon
     *
     * @return bs_form_element|string
     */
    private function input_group_addon($glyphicon) {
        $template     = '<span class="input-group-addon"><i class="glyphicon {glyphicon}"></i></span>';
        $replacements = array('{glyphicon}' => $glyphicon);

        if (empty($glyphicon)) {
            $input_group_addon = '';
        } else {
            $input_group_addon = new bs_form_element($template, $replacements);
        }

        return $input_group_addon;
    }

    /**
     * @param $label
     * @param $input_group_addon
     * @param $inputs
     *
     * @return bs_form_element
     */
    private function form_group($label, $input_group_addon, $inputs) {
        if (is_array($inputs)) {
            foreach ($inputs as $input) {
                self::set_input($input);
            }
        } else {
            self::set_input($inputs);
        }

        $input_container_width = self::get_setting('input_container_width');
        $template              = '<div class="form-group">{label}<div class="' . $input_container_width . ' inputGroupContainer"><div class="input-group">{input_group_addon}{input}</div></div></div>';
        $replacements          = array(
            '{label}'             => $label,
            '{input_group_addon}' => $input_group_addon,
            '{input}'             => $inputs
        );

        return new bs_form_element($template, $replacements);
    }

    /**
     * @param $label
     * @param $glyphicon
     * @param $attributes
     *
     * @return $this
     */
    public function text($label, $glyphicon, $attributes) {
        $template     = '<input type="text" {attributes} >';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control', ' ')
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    public function hr() {
        $template     = '<hr class="divider">';
        $replacements = array();

        $hr = new bs_form_element($template, $replacements);

        self::set_form_group($hr);

        return $this;
    }

    public function title($title, $glyphicon = '', $possition = 'left') {
        if (!empty($glyphicon)) {
            if ($possition == 'left') {
                $margin = 'right';
            } else {
                $margin = 'left';
            }
            $glyphicon = '<span class="glyphicon ' . $glyphicon . '" style="float: ' . $possition . ';margin-' . $margin . ':10px"></span>';
        }
        $template     = '<legend>' . $glyphicon . $title . '</legend>';
        $replacements = array();

        $title = new bs_form_element($template, $replacements);

        self::set_form_group($title);

        return $this;
    }

    public function subtitle($title, $glyphicon = '', $possition = 'left') {
        if (!empty($glyphicon)) {
            if ($possition == 'left') {
                $margin = 'right';
            } else {
                $margin = 'left';
            }
            $glyphicon = '<span class="glyphicon ' . $glyphicon . '" style="float: ' . $possition . ';margin-' . $margin . ':10px"></span>';
        }
        $template     = '<legend style="font-size: 130%">' . $glyphicon . $title . '</legend>';
        $replacements = array();

        $title = new bs_form_element($template, $replacements);

        self::set_form_group($title);

        return $this;
    }

    public function description($title) {
        $template     = '<p>' . $title . '</p>';
        $replacements = array();

        $title = new bs_form_element($template, $replacements);

        self::set_form_group($title);

        return $this;
    }

    /**
     * @param $label
     * @param $glyphicon
     * @param $attributes
     *
     * @return $this
     */
    public function date($label, $glyphicon, $attributes) {
        $template     = '<input type="date" {attributes} >';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control', ' ')
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * @param $label
     * @param $glyphicon
     * @param $attributes
     *
     * @return $this
     */
    public function color($label, $glyphicon, $attributes) {
        $template     = '<input type="color" {attributes} >';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control', ' ')
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * @param $label
     * @param $glyphicon
     * @param $attributes
     *
     * @return $this
     */
    public function password($label, $glyphicon, $attributes) {
        $template     = '<input type="password" {attributes} >';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control', ' ')
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    public function number($label, $glyphicon, $attributes) {
        $template     = '<input type="number" {attributes} >';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control', ' ')
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * Registre new form group with submit button
     *
     * @since 0.0.1
     *
     * @param string $title     Submit button title
     * @param string $glyphicon Glyphicon placed on the right of the button. Source http://glyphicons.com/
     * @param string $style     Bootstrap button style. Accepts 'btn-success', 'btn-warning', other custom classes
     *
     * @return $this
     */
    public function submit($title, $glyphicon, $style) {
        $template     = '<button type="submit" class="btn {style}" >{title} <span class="glyphicon {glyphicon}" style="margin-left: 5px"></span></button>';
        $replacements = array(
            '{title}'     => $title,
            '{glyphicon}' => $glyphicon,
            '{style}'     => $style,
        );

        $label = self::label('');

        $input_group_addon = self::input_group_addon('');
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * Register new form group with textarea
     *
     * @since 0.0.1
     *
     * @param $label
     * @param $glyphicon
     * @param $attributes
     * @param $content
     *
     * @return $this
     */
    public function textarea($label, $glyphicon, $attributes, $content = '') {
        $template     = '<textarea class="form-control" {attributes}>{content}</textarea>';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control', ' '),
            '{content}'    => $content
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * Create new option
     *
     * @since 0.0.1
     *
     * @param $attributes
     * @param $title
     *
     * @return bs_form_element
     */
    private function option($attributes, $title) {
        $template     = '<option {attributes}>{title}</option>';
        $attributes   = array_change_key_case($attributes);
        $replacements = array(
            '{attributes}' => $attributes,
            '{title}'      => $title
        );

        return new bs_form_element($template, $replacements);
    }

    /**
     * Register new form group with select
     *
     * @since 0.0.1
     *
     * @param string $label     Label of input
     * @param string $glyphicon Input icon
     * @param array $attributes Input attributes like name, class, id...
     * @param array $options    Asociative array of option values and titles array('value' => 'title')
     * @param string $selected  value of selected option
     *
     * @return $this
     */
    public function select($label, $glyphicon, $attributes, $options, $selected) {
        $template   = '<select {attributes} >{options}</select>';
        $attributes = array_change_key_case($attributes);
        foreach ($options as $value => &$option) {
            $option_attributes = array(
                'value' => $value
            );

            if ($value == $selected) {
                $option_attributes['selected'] = 'selected';
            }

            $option = self::option($option_attributes, $option);
        }

        $replacements = array(
            '{attributes}' => self::merge_attributes($attributes, 'class', 'form-control selectpicker', ' '),
            '{options}'    => $options
        );

        $label = self::label($label);

        $input_group_addon = self::input_group_addon($glyphicon);
        $input             = new bs_form_element($template, $replacements);

        $form_group = self::form_group($label, $input_group_addon, $input);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * Register new form group with checkboxes. Validation can not be aplyed on checkbox inputs.
     *
     * @since 0.0.1
     *
     * @param string $label       Label of group
     * @param array $check_boxes  Array of multiple checkboxes in one group. Each check box array consist from
     *                            attributes array and checkbox title. F.e.: array(array(array('name'=>'checkbox_1',
     *                            'value' => 1),'Check box one'),array(array('name'=>'checkbox_2', 'value' => 2),'Check
     *                            box two'))
     */
    public function checkbox($label, $check_boxes) {
        $template = '<div class="checkbox"><label style="padding-right: 80px"><input type="checkbox" {attributes} >{title}</label></div>';
        foreach ($check_boxes as &$check_box) {

            $replacements = array(
                '{attributes}' => array_change_key_case($check_box[1]),
                '{title}'      => $check_box[0]
            );

            $check_box = new bs_form_element($template, $replacements);
        }

        $label = self::label($label);

        $input_group_addon = self::input_group_addon('');

        $form_group = self::form_group($label, $input_group_addon, $check_boxes);
        self::set_form_group($form_group);

        return $this;
    }

    /**
     * Merge attribute with constant values. If no such attribute is existing, new ano is created.
     *
     * @since 0.0.1
     *
     * @param array $attributes All attributes
     * @param string $attribute Attribute to be merged
     * @param string $value     Merging value
     * @param string $delimiter Value delimiter
     *
     * @return array
     */
    private function merge_attributes($attributes, $attribute, $value, $delimiter) {
        if (empty($attributes[$attribute])) {
            $attributes[$attribute] = $value;
        } else {
            $attributes[$attribute] .= $delimiter . $value;
        }

        return $attributes;
    }

    /**
     * Form opening
     *
     * @since 0.0.1
     *
     * @return string
     */

    private function open() {
        $action = self::get_setting('action');
        $method = self::get_setting('method');

        $id = self::get_setting('id');

        $open = '<div class="container"><form class="well form-horizontal" action="' . $action . '" method="' . $method . '" id="' . $id . '"><fieldset>';

        return $open;
    }

    /**
     * Form closure
     *
     * @since 0.0.1
     *
     * @return string
     */

    private function close() {
        return '</fieldset></form></div>';
    }

    /**
     * Build all form groups
     *
     * @since 0.0.1
     *
     * @return string
     */
    private function render_form() {
        $form_groups = self::get_form_groups();

        foreach ($form_groups as &$form_group) {
            $form_group = $form_group->build();
        }

        return self::open() . implode("\n", $form_groups) . self::close();
    }

    /**
     * @return string
     */
    private function render_javascript() {
        $inputs_validations = self::get_validations();

        $fields = array();
        foreach ($inputs_validations as $input_name => $validations) {

            $validators = array();
            foreach ($validations as $validation => $values) {

                $data = array();
                foreach ($values as $key => $value) {
                    if (is_string($value)) {
                        $data[] = $key . ': "' . $value . '"';
                    } else {
                        $data[] = $key . ': ' . $value;
                    }
                }
                $validators[] = $validation . ': {' . implode(", ", $data) . '}';
            }
            $fields[] = $input_name . ': {validators: {' . implode(',', $validators) . '}}';
        }

        return '<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" ></script >
    <script src = "http://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.4.5/js/bootstrapvalidator.min.js" ></script >
        <script> $(document) . ready(function () {
            $("#' . self::get_setting('id') . '") . bootstrapValidator({
        feedbackIcons: {
                valid:
                "glyphicon glyphicon-ok",
            invalid: "glyphicon glyphicon-remove",
            validating: "glyphicon glyphicon-refresh"
        }, fields: {
                ' . implode(', ', $fields) . '}})
        .on("success.form.bv", function (e) {
                $("#success_message") . slideDown({
                opacity: "show"
            }, "slow")
            $("#contact_form") . data("bootstrapValidator") . resetForm();


            e . preventDefault();


            var $form = $(e . target);


            var bv = $form . data("bootstrapValidator");


            $.post($form . attr("action"), $form . serialize(), function (result) {
                console . log(result);
            }, "json");
        });
});</script > ';
    }

    /**
     * Render whole form
     */
    public function render() {
        echo self::render_form();
        echo self::render_javascript();
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function required($message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'This field is required . ';
        }
        self::set_validations($last_input_name, 'notEmpty', array('message' => $message));

        return $this;
    }

    /**
     * @param        $min
     * @param        $max
     * @param string $message
     *
     * @return $this
     */
    public function string_length($min, $max, $message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'Numeric values are out of required range from ' . $min . ' to ' . $max . ' . ';
        }
        self::set_validations($last_input_name, 'stringLength', array(
            'min' => $min, 'max' => $max, 'message' => $message));

        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function numeric($message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'Please fill in number . ';
        }
        self::set_validations($last_input_name, 'numeric', array('message' => $message));

        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function email_address($message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'Please fill in email address. ';
        }
        self::set_validations($last_input_name, 'emailAddress', array('message' => $message));

        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function phone($message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'Please fill in valid phone number. ';
        }
        self::set_validations($last_input_name, 'phone', array(
            'country' => self::get_setting('country'), 'message' => $message));

        return $this;
    }

    public function uri($message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'Please fill in valid URI address (e.g. http://google.com).';
        }
        self::set_validations($last_input_name, 'uri', array('message' => $message));

        return $this;
    }

    public function between($min, $max, $message = '') {
        $last_input_name = self::get_last_input()
                               ->get_name();

        if (empty($message)) {
            $message = 'The number must be between ' . $min . ' and ' . $max;
        }
        self::set_validations($last_input_name, 'between', array(
            'min' => $min, 'max' => $max, 'message' => $message));

        return $this;
    }

    public function choice($input_name, $min, $max, $message = '') {
        if (empty($message)) {
            $message = 'Please choose ' . $min . ' - ' . $max . ' options.';
        }
        self::set_validations('\'' . $input_name . '\'', 'choice', array(
            'min' => $min, 'max' => $max, 'message' => $message));

        return $this;
    }
}

/**
 * @param $data
 */
function p($data) {
    echo ' < pre>';
    print_r($data);
    echo ' </pre > ';
}