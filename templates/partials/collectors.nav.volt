{# partials/collectors.nav #}
<div class="list-group card-shadow">
    {% for collector in this.profilerManager.collectors() %}
        <a href="{{ url(['for': '_profiler-tag', 'tag': _tag], ['panel': collector.name()]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center{{ collector.name() === _panel ? ' active' : '' }}">
            {{ partial(method_exists(collector, 'menuPath') and collector.menuPath() ? collector.menuPath() : '@profiler/profiler/base.menu.volt', ['panel': collector.name(), 'active': collector.name() === _panel, 'meta': _meta['collectors'][collector.name()] is defined ? _meta['collectors'][collector.name()] : []]) }}
        </a>
    {% endfor %}
</div>
