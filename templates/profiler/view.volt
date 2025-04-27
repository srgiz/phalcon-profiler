{# profiler/view #}
{% extends '@profiler/profiler.volt' %}

{% block panel %}
    <h1>View</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-active">
            <tr>
                <th scope="col">Active render paths</th>
            </tr>
            </thead>
            <tbody>
            {% for idx, item in activeRenderPaths %}
                <tr>
                    <td>
                        <code class="d-block mb-1">{{ item['path']|e }}</code>
                        <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseTrace_{{ idx }}" role="button" aria-expanded="false">
                            backtrace
                        </a>
                        <div class="collapse mt-2" id="collapseTrace_{{ idx }}">
                            {{ profiler_dump(item['backtrace']) }}
                        </div>
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
{% endblock %}
