{# profiler/exception #}
{% extends '@profiler/profiler.volt' %}

{% block panel %}
    <h1>Exception</h1>
    {% if trace is empty %}
        <div class="border text-center p-2">none</div>
    {% else %}
        <table class="table border">
            <thead class="table-active">
            <tr>
                <th scope="col" class="block-break-all text-light-emphasis">
                    <code class="text-body">
                        <span class="text-light-emphasis fs-5">{{ class|e }}</span>
                        <span class="d-block fw-normal">{{ file|e }}:{{ line|e }}</span>
                    </code>
                </th>
            </tr>
            </thead>
            <tbody>
            {% for item in trace %}
                <tr>
                    <td class="block-break-all">
                        {% if item['function'] is defined %}
                            <code class="text-body">
                                {% if item['class'] is defined %}<span class="text-info">{{ item['class']|e }}</span>::{% endif %}<span class="text-warning">{{ item['function']|e }}</span>
                            </code>
                        {% endif %}

                        {% if item['file'] is defined %}
                            <code class="d-block text-body">
                                {{ item['file']|e }}{% if item['line'] is defined %}:{{ item['line']|e }}{% endif %}
                            </code>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    {% endif %}
{% endblock %}
