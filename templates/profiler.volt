{# profiler #}
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
{{ partial('@profiler/partials/head') }}
<body>
{{ partial('@profiler/partials/header.nav', ['_tag': _tag, '_panel': _panel]) }}
<div class="container gx-4">
    <div class="row gx-4">
        <div class="col">
            {% set color = _meta['statusCode'] < 400 ? 'success' : (_meta['statusCode'] < 500 ? 'warning' : 'danger') %}
            <div class="alert alert-{{ color }}">
                <div class="fs-5">
                    <span class="badge me-2 text-bg-{{ color }}">{{ _meta['method'] }}</span>
                    <span>{{ _meta['uri']|e }}</span>
                </div>
                <div class="row mt-2">
                    <div class="col-auto"><span class="fw-semibold">Status&nbsp;code:</span> {{ _meta['statusCode'] }}</div>
                    <div class="col-auto"><span class="fw-semibold">Route:</span> {{ _meta['route']|e }}</div>
                    <div class="col-auto"><span class="fw-semibold">Time:</span> {{ _meta['requestTime'].format('c') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row gx-4">
        <div class="col-sm-12 col-md-auto mb-4" style="min-width: 14rem">
            {{ partial('@profiler/partials/collectors.nav', ['_tag': _tag, '_panel': _panel]) }}
        </div>
        <div class="col-sm-12 col-md">
            {% block panel %}{% endblock %}
        </div>
    </div>
</div>
{{ partial('@profiler/partials/footer') }}
{% block js %}{% endblock %}
</body>
</html>

