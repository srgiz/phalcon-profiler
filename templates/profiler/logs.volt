{# profiler/logger #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <h2 class="mb-3">Logs</h2>

    {% if items is empty %}
        <div class="card{# border border-dashed#}">
            <div class="card-body text-center pt-2 pb-2">none</div>
        </div>
    {% else %}
        <div class="card card-shadow">
            <div class="card-header">
                <ul class="nav nav-underline" role="tablist">
                    {% for num, btn in buttons %}
                        {% set color = num < 4 ? 'danger' : (num === 4 ? 'warning' : (num < 7 ? 'primary' : 'secondary')) %}
                        <li class="nav-item" role="presentation">
                            <a href=".tr-{{ btn['name'] }}" class="nav-link text-{{ color }}" data-bs-toggle="collapse" role="button" aria-expanded="true">
                                {#{{ btn['name'] }}<span class="badge text-bg-dark ms-2">{{ btn['count'] }}</span>#}
                                {{ btn['name'] }}<span class="badge bg-{{ color }}-subtle text-{{ color }} ms-2">{{ btn['count'] }}</span>
                            </a>
                        </li>
                    {% endfor %}
                </ul>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th scope="col" style="width: 15rem">Time</th>
                            <th scope="col">Message</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for idx, item in items %}
                            <tr class="collapse show tr-{{ item['levelName'] }}">
                                {% set num = item['level'] %}
                                {% set color = num < 4 ? 'danger' : (num === 4 ? 'warning' : (num < 7 ? 'primary' : 'secondary')) %}
                                <td class="text-nowrap position-relative{# td-border-left td-border-left-{{ color }}#}">
                                    <div>{{ item['datetime'].format('c') }}</div>
                                    <span class="badge bg-{{ color }}-subtle text-{{ color }} mt-2">{{ item['levelName'] }}</span>
                                </td>
                                <td>
                                    {% autoescape false %}
                                        <div class="block-break-all mb-2">{{ item['message'] }}</div>
                                        <a class="me-2 text-decoration-none" data-bs-toggle="collapse" href="#collapseContext_{{ idx }}" role="button" aria-expanded="false">
                                            context
                                        </a>
                                        <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseTrace_{{ idx }}" role="button" aria-expanded="false">
                                            backtrace
                                        </a>
                                        <div class="mt-2 collapse" id="collapseContext_{{ idx }}">
                                            {# this.profilerDump.variable(item['context']) #}
                                            {{ profiler_dump(item['context']) }}
                                        </div>
                                        <div class="mt-2 collapse" id="collapseTrace_{{ idx }}">
                                            {{ profiler_dump(item['backtrace']) }}
                                        </div>
                                    {% endautoescape %}
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    {% endif %}
{% endblock %}
