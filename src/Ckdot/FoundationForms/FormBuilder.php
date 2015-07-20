<?php

namespace Ckdot\FoundationForms;

use Illuminate\Html\FormBuilder as IlluminateFormBuilder;

class FormBuilder extends IlluminateFormBuilder
{

    /**
     * An array containing the currently opened form groups.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * Stores for each group if a label is present.
     * So we know if the label tag needs to get closed again when the group is closed.
     *
     * @var bool[]
     */
    protected $hasLabelStack = [];

    /**
     * An array containing the options of the currently open form groups.
     *
     * @var array
     */
    protected $groupOptions = [];

    /**
     * Create a form label element.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function label($name = null, $value = null, $options = array())
    {
        $this->labels[] = $name;

        $options = $this->html->attributes($options);

        $value = e($this->formatLabel($name, $value));

        $for = $name ? 'for="' . $name . '"' : '';
        return '<label '.$for.$options.'>'.$value.'</label>';
    }

    /**
     * Open a new form group.
     *
     * @param  string $name
     * @param  mixed  $label
     * @param  array  $options
     * @param  array  $labelOptions
     *
     * @return string
     */
    public function openGroup(
        $name,
        $label = null,
        $options = [],
        $labelOptions = []
    ) {
        // Append the name of the group to the groupStack.
        $this->groupStack[] = $name;
        $this->hasLabelStack[] = $label ? true : false;

        $options = $this->appendClassToOptions('columns', $options);

        if ($this->hasErrors($name)) {
            // If the form element with the given name has any errors,
            // apply the 'has-error' class to the group.
            $labelOptions = $this->appendClassToOptions('error', $labelOptions);
        }

        // If a label is given, we set it up here. Otherwise, we will just
        // set it to an empty string.
        $label = $label ? $this->label(null, $label, $labelOptions) : '';
        $label = str_replace('</label>', '', $label);

        $attributes = [];
        foreach ($options as $key => $value) {
            if (!in_array($key, ['errorBlock'])) {
                $attributes[$key] = $value;
            }
        }

        return '<div' . $this->html->attributes($attributes) . '>' . $label;
    }

    /**
     * Close out the last opened form group.
     *
     * @return string
     */
    public function closeGroup()
    {
        $name       = array_pop($this->groupStack);
        $hasLabel   = array_pop($this->hasLabelStack);

        $errors = $this->getFormattedErrors($name);

        // Append the errors to the group and close it out.
        $html = $errors . '</div>';

        if ($hasLabel) {
            $html = '</label>' . $html;
        }
        return $html;
    }

    /**
     * Append the given class to the given options array.
     *
     * @param  string $class
     * @param  array  $options
     *
     * @return array
     */
    private function appendClassToOptions($class, array $options = [])
    {
        // If a 'class' is already specified, append the 'form-control'
        // class to it. Otherwise, set the 'class' to 'form-control'.
        $options['class'] = isset($options['class']) ? $options['class'] . ' '
            : '';
        $options['class'] .= $class;

        return $options;
    }

    /**
     * Determine whether the form element with the given name
     * has any validation errors.
     *
     * @param  string $name
     *
     * @return bool
     */
    private function hasErrors($name)
    {
        if (is_null($this->session) || !$this->session->has('errors')) {
            // If the session is not set, or the session doesn't contain
            // any errors, the form element does not have any errors
            // applied to it.
            return false;
        }

        // Get the errors from the session.
        $errors = $this->session->get('errors');

        // Check if the errors contain the form element with the given name.
        // This leverages Laravel's transformKey method to handle the
        // formatting of the form element's name.
        return $errors->has($this->transformKey($name));
    }

    /**
     * Get the formatted errors for the form element with the given name.
     *
     * @param  string $name
     *
     * @return string
     */
    private function getFormattedErrors($name)
    {
        if (!$this->hasErrors($name)) {
            // If the form element does not have any errors, return
            // an emptry string.
            return '';
        }

        // Get the errors from the session.
        $errors = $this->session->get('errors');

        // Return the formatted error message, if the form element has any.
        return $errors->first(
            $this->transformKey($name),
            '<small class="error">:message</small>'
        );
    }

}
