{# profiler/routing #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <h2 class="mb-3">{{ _panel }}</h2>
    <div class="card-shadow mb-4">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Pattern</th>
            </tr>
            </thead>
            <tbody>
            {% if routes is empty %}
                <tr>
                    <td colspan="3">none</td>
                </tr>
            {% else %}
                {% set hl = true %}
                {% for idx, route in routes %}
                    <tr class="{{ hl and match ? 'table-success' : '' }}">
                        <td>{{ route['id'] }}</td>
                        <td>{{ route['name'] }}</td>
                        <td>{{ route['pattern'] }}</td>
                    </tr>
                    {% set hl = false %}
                {% endfor %}
            {% endif %}
            </tbody>
        </table>
    </div>
{% endblock %}
