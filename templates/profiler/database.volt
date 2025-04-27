{# profiler/database #}
{% extends '@profiler/profiler.volt' %}

{% block panel %}
    <h1>Database</h1>
    <div class="row gx-3 mb-4">
        <div class="col-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title text-info-emphasis text-center">Queries</div>
                    <div class="card-text text-light-emphasis text-center fs-5">{{ queriesCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title text-info-emphasis text-center">Time</div>
                    <div class="card-text text-light-emphasis text-center fs-5">{{ queriesTime }}&nbsp;ms</div>
                </div>
            </div>
        </div>
    </div>
    {% for connId, conn in connections %}
        <h2>{{ connId }}. {{ conn['type'] }}</h2>
        <div class="table-responsive mb-4">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Time</th>
                    <th scope="col">Query</th>
                </tr>
                </thead>
                <tbody>
                {% for idx, item in conn['queries'] %}
                    <tr>
                        <th scope="row">{{ idx + 1 }}</th>
                        <td>{{ item['query'].getTotalElapsedMilliseconds() }}&nbsp;ms</td>
                        <td>
                            <div><code>{{ item['query'].getSqlStatement()|e }}</code></div>
                            <div class="mt-2">{{ profiler_dump(item['query'].getSqlVariables()) }}</div>
                            <div class="mt-2">
                                <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseBacktrace_{{ connId }}_{{ idx }}" role="button" aria-expanded="false">
                                    backtrace
                                </a>
                                <div class="collapse mt-2" id="collapseBacktrace_{{ connId }}_{{ idx }}">
                                    {{ profiler_dump(item['backtrace']) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    {% endfor %}
{% endblock %}
