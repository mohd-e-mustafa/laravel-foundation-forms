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
     * An array containing the options of the currently open form groups.
     *
     * @var array
     */
    protected $groupOptions = [];

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
        $this->groupStack[]     = $name;
        $this->groupOptions[]   = $options;

        if ($this->hasErrors($name)) {
            // If the form element with the given name has any errors,
            // apply the 'has-error' class to the group.
            $labelOptions = $this->appendClassToOptions('error', $labelOptions);
        }

        // If a label is given, we set it up here. Otherwise, we will just
        // set it to an empty string.
        $label = $label ? $this->label($name, $label, $labelOptions) : '';

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
        // Get the last added name from the groupStack and
        // remove it from the array.
        $name = array_pop($this->groupStack);

        // Get the last added options to the groupOptions
        // This way we can check if error blocks were enabled
        $options = array_pop($this->groupOptions);

        // Check to see if we are to include the formatted help block
        if ($this->hasErrors($name)) {
            // Get the formatted errors for this form group.
            $errors = $this->getFormattedErrors($name);
        }

        // Append the errors to the group and close it out.
        return $errors . '</div>';
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
            '<small class="error">:message</span>'
        );
    }

}
