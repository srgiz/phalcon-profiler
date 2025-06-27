{# profiler/volt #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <h2 class="mb-3">{{ _panel }}</h2>
    <div class="card-shadow mb-4">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th scope="col">Active render paths</th>
            </tr>
            </thead>
            <tbody>
            {% if activeRenderPaths is empty %}
                <tr>
                    <td>none</td>
                </tr>
            {% else %}
                {% for idx, item in activeRenderPaths %}
                    <tr>
                        <td>
                            <code class="d-block mb-1">{{ item['path'] }}</code>
                            <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseTrace_{{ idx }}" role="button" aria-expanded="false">
                                backtrace
                            </a>
                            <div class="collapse mt-2" id="collapseTrace_{{ idx }}">
                                {% autoescape false %}
                                    {{ profiler_dump(item['backtrace']) }}
                                {% endautoescape %}
                            </div>
                        </td>
                    </tr>
                {% endfor %}
            {% endif %}
            </tbody>
        </table>
    </div>
{% endblock %}
