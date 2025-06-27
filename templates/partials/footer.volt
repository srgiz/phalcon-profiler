{# partials/footer #}
{% autoescape false %}
    {{ this.profilerAssets.outputInlineFile('@profiler/templates/assets/bootstrap.bundle.min.5.3.7.js') }}
    {{ this.profilerAssets.outputInlineFile('@profiler/templates/assets/bootstrap-color-mode.js') }}
{% endautoescape %}
