{# profiler/requests #}
{% extends '@profiler/base.volt' %}

{% block content %}
    <h2 class="mt-4 mb-4">Requests</h2>
    <div class="table-responsive card-shadow rounded-2 mb-4">
        <table class="table table-hover table-striped-columns mb-0">
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
                    {% set color = item['statusCode'] < 400 ? 'success' : 'danger' %}
                    <td>
                        <a class="text-decoration-none" href="{{ url(['for': '_profiler-tag', 'tag': tag]) }}">{{ tag }}</a>
                    </td>
                    <td>
                        <span class="badge text-bg-{{ color }} align-text-bottom">{{ item['statusCode'] }}</span>
                    </td>
                    <td>{{ item['method'] }}</td>
                    <td>{{ item['uri'] }}</td>
                    <td>{{ item['requestTime'].format('c') }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
{% endblock %}
