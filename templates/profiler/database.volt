{# profiler/database #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <h2 class="mb-3">Database</h2>
    <div class="row gx-3">
        <div class="col-auto mb-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div>
                            <div class="card-title fw-medium text-muted">Queries</div>
                            <div class="card-text text-light-emphasis fs-5">{{ meta['count'] }}</div>
                        </div>
                        <div class="ms-3">
                            <div class="icon-box bg-info-subtle text-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto mb-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div>
                            <div class="card-title fw-medium text-muted">Query time</div>
                            <div class="card-text text-light-emphasis fs-5">{{ '%.3F'|format(time) }}&nbsp;ms</div>
                        </div>
                        <div class="ms-3">
                            <div class="icon-box bg-primary-subtle text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-hourglass-split" viewBox="0 0 16 16">
                                    <path d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48zm1 0v3.17c2.134.181 3 1.48 3 1.48a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {% for connId, conn in connections %}
        <h3 class="mb-3">{{ connId }}. {{ conn['type'] }}</h3>
        <div class="table-responsive card-shadow mb-4">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th scope="col" style="width: 2rem">#</th>
                    <th scope="col" style="width: 8rem">Time</th>
                    <th scope="col">Query</th>
                </tr>
                </thead>
                <tbody>
                {% for idx, item in conn['queries'] %}
                    <tr>
                        <td>{{ idx + 1 }}</td>
                        <td>{{ '%.3F'|format(item['query'].getTotalElapsedMilliseconds()) }}&nbsp;ms</td>
                        <td>
                            {% autoescape false %}
                                <div><code>{{ item['query'].getSqlStatement()|e }}</code></div>
                                {% if item['query'].getSqlVariables() is not empty %}
                                    <div class="mt-2">{{ profiler_dump(item['query'].getSqlVariables()) }}</div>
                                {% endif %}
                                <div class="mt-2">
                                    <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseBacktrace_{{ connId }}_{{ idx }}" role="button" aria-expanded="false">
                                        backtrace
                                    </a>
                                    <div class="collapse mt-2" id="collapseBacktrace_{{ connId }}_{{ idx }}">
                                        {{ profiler_dump(item['backtrace']) }}
                                    </div>
                                </div>
                            {% endautoescape %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    {% endfor %}
{% endblock %}
