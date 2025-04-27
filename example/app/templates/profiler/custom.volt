{# profiler/custom.volt #}
{% extends '@profiler/profiler.volt' %}

{% block panel %}
    <h1>Custom</h1>

    Message: {{ message|e }}
{% endblock %}
