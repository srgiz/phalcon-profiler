{# profiler/custom.volt #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <h1>Custom</h1>

    Message: {{ message|e }}
{% endblock %}
