{# partials/header.nav #}
<nav class="bg-dark-subtle mb-3">
    <div class="container gx-4">
        <ul class="nav" style="margin: 0 -0.75rem">
            <li class="nav-item">
                {% set query = _panel is defined ? ['panel': _panel] : [] %}
                <a class="nav-link" href="{{ url(['for': '_profiler-tag', 'tag': 'last'], query) }}">Last</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url(['for': '_profiler']) }}">List</a>
            </li>
            {% if _tag is defined %}
                <li class="nav-item ms-auto">
                    <span class="nav-link text-body">{{ _tag }}</span>
                </li>
            {% endif %}
        </ul>
    </div>
</nav>
