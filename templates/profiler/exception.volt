{# profiler/exception #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <h2 class="mb-3">Exception</h2>
    {% if trace is empty %}
        <div class="card{# border border-dashed#}">
            <div class="card-body text-center pt-2 pb-2">none</div>
        </div>
    {% else %}
        <div class="alert alert-danger">
            {{ message }}
        </div>
        <table class="table table-hover card-shadow">
            <thead>
            <tr>
                <th scope="col" class="block-break-all text-light-emphasis">
                    <code class="text-body">
                        <span class="text-light-emphasis fs-5">{{ class }}</span>
                        <span class="d-block fw-normal">{{ file }}:{{ line }}</span>
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
                                {% if item['class'] is defined %}<span class="text-info">{{ item['class'] }}</span>::{% endif %}<span class="text-warning">{{ item['function'] }}</span>
                            </code>
                        {% endif %}

                        {% if item['file'] is defined %}
                            <code class="d-block text-body">
                                {{ item['file'] }}{% if item['line'] is defined %}:{{ item['line'] }}{% endif %}
                            </code>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    {% endif %}
{% endblock %}
