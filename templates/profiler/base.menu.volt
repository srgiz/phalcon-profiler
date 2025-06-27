{# profiler/base.menu #}
<span class="icon-link">
    {% block icon %}
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
            <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
        </svg>
    {% endblock %}
    {{ panel }}
</span>
{% block badge %}
    {% if badge is defined %}
        {#<span class="badge badge-small bg-{{ active ? 'primary' : 'secondary' }}-subtle text-{{ active ? 'primary' : 'secondary' }} ms-3">{{ badge }}</span>#}
        <span class="badge badge-small bg-secondary-subtle text-secondary ms-3">{{ badge }}</span>
    {% endif %}
{% endblock %}
