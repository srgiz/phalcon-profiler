{# data #}
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
{{ partial('@profiler/partials/head') }}
<body>
{{ partial('@profiler/partials/header.nav', ['_tag': _tag, '_panel': _panel]) }}
<div class="profiler container-xxl">
    <div class="row">
        <div class="col">
            {% set color = _meta['statusCode'] < 400 ? 'success' : 'danger' %}
            <div class="alert alert-{{ color }} alert-border-left mb-4">
                <div class="fs-5">
                    <span class="badge text-body rounded-2 border border-secondary-subtle">{{ _meta['method'] }}</span>
                    <span class="fw-semibold">{{ _meta['uri'] }}</span>
                </div>
                <div class="row mt-2 text-secondary">
                    <div class="col-auto"><span class="fw-semibold">Response: <span class="badge text-bg-{{ color }} align-text-bottom">{{ _meta['statusCode'] }}</span></span></div>
                    <div class="col-auto"><span class="fw-semibold">Route:</span> {{ _meta['route'] ? _meta['route'] : 'null' }}</div>
                    <div class="col-auto"><span class="fw-semibold">Time:</span> {{ _meta['requestTime'].format('c') }}</div>
                    <div class="col-auto"><span class="fw-semibold">Tag:</span> {{ _tag }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-auto">
            {{ partial('@profiler/partials/collectors.nav', ['_tag': _tag, '_panel': _panel, '_meta': _meta]) }}
        </div>
        <div class="col">
            {% block title %}<h2 class="mb-3">{{ _panel }}</h2>{% endblock %}
            {% block panel %}{% endblock %}
        </div>
    </div>
</div>
{{ partial('@profiler/partials/footer') }}
{% block js %}{% endblock %}
</body>
</html>
