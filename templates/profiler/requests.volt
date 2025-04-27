{# profiler/requests #}
{% extends '@profiler/page.volt' %}

{% block content %}
    <h1>Requests</h1>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Tag</th>
                <th scope="col">Status</th>
                <th scope="col">Method</th>
                <th scope="col">Uri</th>
                <th scope="col">Time</th>
            </tr>
            </thead>
            <tbody>
            {% for tag, item in requests %}
                <tr>
                    {% set color = item['statusCode'] < 400 ? 'success' : (item['statusCode'] < 500 ? 'warning' : 'danger') %}
                    <th scope="row">
                        <a class="text-decoration-none" href="{{ url(['for': '_profiler-tag', 'tag': tag]) }}">{{ tag }}</a>
                    </th>
                    <td>
                        <span class="badge text-bg-{{ color }}">{{ item['statusCode'] }}</span>
                    </td>
                    <td>{{ item['method'] }}</td>
                    <td>{{ item['uri']|e }}</td>
                    <td>{{ item['requestTime'].format('c') }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
{% endblock %}
