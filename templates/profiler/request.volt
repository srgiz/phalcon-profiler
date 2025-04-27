{# profiler/request #}
{% extends '@profiler/profiler.volt' %}

{% block panel %}
    {% macro table(list) %}
        {% if list is empty %}
            <div class="border text-center p-2 mb-4">none</div>
        {% else %}
            <div class="table-responsive mb-4">
                <table class="table table-hover border mb-0">
                    <thead class="table-active">
                    <tr>
                        <th scope="col" style="width: 20%">Header</th>
                        <th scope="col">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for name, value in list %}
                        <tr>
                            <td class="text-light-emphasis">{{ name|e }}</td>
                            <td class="block-break-all">{{ profiler_dump(value) }}</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        {% endif %}
    {% endmacro %}

    <h2>GET</h2>
    {{ table(query) }}

    <h2>POST</h2>
    {{ table(post) }}

    <h2>Request headers</h2>
    {{ table(requestHeaders) }}

    <h2>Response headers</h2>
    {{ table(responseHeaders) }}
{% endblock %}
