{# bar #}
{% set gutter = 0.75 %}
<div id="phalcon-profiler" style="position: fixed; bottom: 0; left: 0; right: 0; background: #1a1d20; color: #dee2e6">
    <div style="display: flex; justify-content: space-between; font-size: 1rem; line-height: 2.25rem">
        <div style="display: flex; margin-right: {{ gutter*2 }}rem">
            <span style="padding: 0 {{ gutter }}rem; font-weight: 600; background: {{ _meta['statusCode'] < 400 ? '#198754' : (_meta['statusCode'] < 500 ? '#fd7e14' : '#dc3545') }}">{{ _meta['statusCode'] }}</span>
            <span style="margin-left: {{ gutter }}rem"><span style="color: #adb5bd">Route:</span> {{ _meta['route']|e }}</span>
            <span style="margin-left: {{ gutter*2 }}rem">{{ _meta['executionTime'] }}&nbsp;<span style="color: #adb5bd">ms</span></span>
            <span style="margin-left: {{ gutter*2 }}rem">{{ '%.2F'|format(_meta['peakMemoryUsage']) }}&nbsp;<span style="color: #adb5bd">MiB</span></span>
        </div>
        <div style="display: flex">
            <a href="{{ url(['for': '_profiler-tag', 'tag': _tag]) }}" style="margin-right: {{ gutter }}rem; text-decoration: none; color: #6ea8fe" target="_blank">{{ _tag }}</a>
            <a title="Close" style="display: block; line-height: 1.15rem; padding: 0.6rem {{ gutter }}rem; background: #343a40; color: inherit; cursor: pointer" onclick="document.getElementById('phalcon-profiler').remove()">
                <svg style="height: 1rem; width: auto; vertical-align: text-top" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                </svg>
            </a>
        </div>
    </div>
</div>
