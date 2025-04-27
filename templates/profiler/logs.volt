{# profiler/logger #}
{% extends '@profiler/profiler.volt' %}

{% block panel %}
    <h1>Logs</h1>
    {% if items is empty %}
        <div class="border text-center p-2 mb-4">none</div>
    {% else %}
        <div class="mb-2">
            {% for num, btn in buttons %}
                {% set color = num < 4 ? 'danger' : (num === 4 ? 'warning' : (num < 7 ? 'primary' : 'light')) %}
                <a href=".tr-{{ btn['name']|e }}" class="btn btn-sm btn-{{ color }} fw-semibold" data-bs-toggle="collapse" role="button" aria-expanded="true">
                    {{ btn['name']|e }}<span class="badge text-bg-dark ms-2">{{ btn['count'] }}</span>
                </a>
            {% endfor %}
        </div>
        <div class="table-responsive">
            <table class="table border">
                <thead class="table-active">
                <tr>
                    <th scope="col" style="width: 7rem">Level</th>
                    <th scope="col" style="width: 14rem">Time</th>
                    <th scope="col">Message</th>
                </tr>
                </thead>
                <tbody>
                {% for idx, item in items %}
                    <tr class="collapse show tr-{{ item['levelName']|e }}">
                        <td>
                            {% set num = item['level'] %}
                            {% set color = num < 4 ? 'danger' : (num === 4 ? 'warning' : (num < 7 ? 'primary' : 'light')) %}
                            <span class="badge text-bg-{{ color }}">{{ item['levelName']|e }}</span>
                        </td>
                        <td>{{ item['datetime'].format('c') }}</td>
                        <td>
                            <div class="block-break-all mb-2">{{ item['message'] }}</div>
                            <a class="me-2 text-decoration-none" data-bs-toggle="collapse" href="#collapseContext_{{ idx }}" role="button" aria-expanded="false">
                                context
                            </a>
                            <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseTrace_{{ idx }}" role="button" aria-expanded="false">
                                backtrace
                            </a>
                            <div class="mt-2 collapse" id="collapseContext_{{ idx }}">
                                {{ this.profilerDump.variable(item['context']) }}
                            </div>
                            <div class="mt-2 collapse" id="collapseTrace_{{ idx }}">
                                {{ profiler_dump(item['backtrace']) }}
                            </div>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    {% endif %}
{% endblock %}
