{# profiler/custom.volt #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    Message: {{ message|e }}
{% endblock %}
