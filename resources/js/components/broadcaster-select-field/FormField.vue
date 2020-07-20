<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <select-control
                :id="field.name"
                :type="this.field.type"
                v-model="value"
                class="w-full form-control form-select"
                :class="errorClasses"
                :options="field.options"
                :disabled="isReadonly"
                @input="setFieldAndMessage"
            >
                <option value="" selected :disabled="!field.nullable">{{
                    __('Choose an option')
                    }}</option>
            </select-control>
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
            // const rawValue = el.target.value;
            //console.log(el);
            const rawValue = this.value;
            let parsedValue = rawValue;

            if (this.field.type === 'number') {
                parsedValue = Number(rawValue)
            }

            Nova.$emit(this.field.broadcastTo, {
                'field_name': this.field.attribute,
                'value': parsedValue
            }),

            this.value = parsedValue;
        },

        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
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
}
</script>
