<?php

namespace Codebykyle\CalculatedField;

use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Field;

class BroadcasterBooleanGroupField extends BooleanGroup
{

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'broadcaster-boolean-group-field';

    /**
     * BroadcasterField constructor.
     *
     * @param $name
     * @param null $attribute
     * @param callable|null $resolveCallback
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'broadcastTo' => 'broadcast-field-input',
        ]);
    }

}
