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
                <th scope="col">Listener</th>
            </tr>
            </thead>
            <tbody>
            {% if listeners is not defined %}
                <tr>
                    <td colspan="2">none</td>
                </tr>
            {% else %}
                {% for event, eventListeners in listeners %}
                    <tr>
                        <th colspan="2">
                            <code style="color: initial">{{ event }}</code>
                        </th>
                    </tr>
                    {% for data in eventListeners %}
                        <tr>
                            <td>{{ data['priority'] }}</td>
                            <td>
                                <code>{{ data['type'] }}</code>
                            </td>
                        </tr>
                    {% endfor %}
                {% endfor %}
            {% endif %}
            </tbody>
        </table>
    </div>
{% endblock %}
