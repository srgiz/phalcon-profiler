{# profiler/volt #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    {% if activeRenderPaths is empty %}
        {{ partial('@profiler/partials/noevents.card', ['title': 'Volt is not used or event manager is missing', 'service': 'volt']) }}
    {% else %}
        <div class="card-shadow mb-4">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th scope="col">Active render paths</th>
                </tr>
                </thead>
                <tbody>
                {% for idx, item in activeRenderPaths %}
                    <tr>
                        <td>
                            <code class="d-block mb-1">{{ item['path'] }}</code>
                            <a data-bs-toggle="collapse" href="#collapseTrace_{{ idx }}" role="button" aria-expanded="false">
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
                </tbody>
            </table>
        </div>
    {% endif %}
{% endblock %}
