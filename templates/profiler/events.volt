{# profiler/events #}
{% extends '@profiler/data.volt' %}

{% block panel %}
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
