<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <input
                :id="field.name"
                :type="this.field.type"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :placeholder="field.name"
                :value="value"
                v-bind="extraAttributes"
                @input="setFieldAndMessage"
            />
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    methods: {
        setFieldAndMessage(el) {
            const rawValue = el.target.value;
            let parsedValue = rawValue;

            if (this.field.type === 'number') {
                parsedValue = Number(rawValue)
            }

            Nova.$emit(this.field.broadcastTo, {
                'field_name': this.field.attribute,
                'value': parsedValue
            })

            this.value = parsedValue;
        },

        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
            Nova.$emit(this.field.broadcastTo, {
              'field_name': this.field.attribute,
              'value': (this.field.type === 'number') ? Number(this.value): this.value,
              'init': true,
            })
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },
    },
    computed: {
      defaultAttributes() {
        return {
          type: this.field.type,
          min: this.field.min,
          max: this.field.max,
          step: this.field.step,
          pattern: this.field.pattern,
          placeholder: this.field.placeholder || this.field.name,
          class: this.errorClasses,
        };
      },
      extraAttributes() {
        const attrs = this.field.extraAttributes;

        return {
          // Leave the default attributes even though we can now specify
          // whatever attributes we like because the old number field still
          // uses the old field attributes
          ...this.defaultAttributes,
          ...attrs,
        };
      },
    }
}
</script>
