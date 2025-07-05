{# profiler/events #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    {% if meta is not defined or meta['arePrioritiesEnabled'] is empty %}
        <div class="alert alert-danger">
            Priorities are not enabled

            <div class="mt-2">
                <a data-bs-toggle="collapse" href="#collapseBacktrace" role="button" aria-expanded="false">
                    example di
                </a>

<pre class="collapse mt-2 mb-0 text-body" id="collapseBacktrace"><code><span class="text-secondary"># services.yaml</span>
<span class="text-code">eventsManager</span>:
  <span class="text-code">calls</span>:
    - <span class="text-code">method</span>: enablePriorities
      <span class="text-code">arguments</span>:
        - { <span class="text-code">type</span>: parameter, <span class="text-code">value</span>: true }</code></pre>

            </div>
        </div>
    {% endif %}

    <div class="card-shadow mb-4">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th scope="col">Priority</th>
                <th scope="col">
                    <div class="d-flex justify-content-between">
                        <span>Listener</span>
                        {% if listeners is defined %}
                            <label class="text-muted small" role="button">
                                <input class="form-check-input form-check-input-no-bg me-1" type="checkbox" data-bs-target=".multi-collapse" data-bs-toggle="collapse" aria-expanded="true" checked>
                                Show profiler events
                            </label>
                        {% endif %}
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            {% if listeners is not defined %}
                <tr>
                    <td colspan="2">none</td>
                </tr>
            {% else %}
                {% for event, eventListeners in listeners %}
                    {% set rowCollapse = true %}
                    {% for data in eventListeners %}
                        {% if !str_starts_with(data['source'], 'Srgiz\Phalcon\WebProfiler\\') %}
                            {% set rowCollapse = false %}
                        {% endif %}
                    {% endfor %}
                    <tr class="collapse show transition-none{{ rowCollapse ? ' multi-collapse' : '' }}">
                        <th colspan="2">
                            <code style="color: initial">{{ event }}</code>
                        </th>
                    </tr>
                    {% for data in eventListeners %}
                        <tr class="collapse show transition-none{{ str_starts_with(data['source'], 'Srgiz\Phalcon\WebProfiler\\') ? ' multi-collapse' : '' }}">
                            <td>{{ data['priority'] }}</td>
                            <td>
                                <code class="text-body">
                                    <span class="text-code">{{ data['source'] }}</span>{% if data['method'] is not empty %}::<span class="text-warning-emphasis">{{ data['method'] }}</span>{% endif %}
                                </code>
                            </td>
                        </tr>
                    {% endfor %}
                {% endfor %}
            {% endif %}
            </tbody>
        </table>
    </div>
{% endblock %}
