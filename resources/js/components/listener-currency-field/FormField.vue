<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <div class="flex flex-wrap items-stretch w-full relative">
        <div class="flex -mr-px">
          <span
            class="flex items-center leading-normal rounded rounded-r-none border border-r-0 border-60 px-3 whitespace-no-wrap bg-30 text-80 text-sm font-bold"
          >
            {{ field.currency }}
          </span>
        </div>

        <input
          class="flex-shrink flex-grow flex-auto leading-normal w-px flex-1 rounded-l-none form-control form-input form-input-bordered"
          :id="field.attribute"
          :dusk="field.attribute"
          v-model="value"
          v-bind="extraAttributes"
          :disabled="isReadonly"
        />
      </div>
    </template>
  </default-field>
</template>

<style lang="scss">
@-webkit-keyframes rotating {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.rotating {
  animation: rotating 2s linear infinite;
}
</style>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";
import _ from "lodash";

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ["resourceName", "resourceId", "field"],

  created() {
    Nova.$on(this.field.listensTo, this.messageReceived);
  },

  data: () => ({
    calculating: false,
    field_values: {},
  }),

  methods: {
    messageReceived(message) {
      this.field_values[message.field_name] = message.value;
      this.calculateValue();
    },

    calculateValue: _.debounce(function() {
      this.calculating = true;

      Nova.request()
        .post(
          `/codebykyle/calculated-field/calculate/${this.resourceName}/${this.field.attribute}`,
          {
            editing: true,
            editMode: this.editMode,
            values: this.field_values,
            resource_id: this.resourceId,
          }
        )
        .then((response) => {
          this.value = response.data.value;
          this.calculating = false;
        })
        .catch(() => {
          this.calculating = false;
        });
    }, 500),

    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || "";
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.value || "");
    },

    /**
     * Update the field's internal value.
     */
    handleChange(value) {
      this.value = value;
    },
  },

  computed: {
    defaultAttributes() {
      return {
        type: "number",
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
    editMode() {
      if(this.$route.name === 'edit') {
        return 'update'
      }
      if(this.$route.name === 'edit-attached') {
        return 'update-attached'
      }
      return this.$route.name
    }
  },
};
</script>
